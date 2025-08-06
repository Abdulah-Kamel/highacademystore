<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditProductRequest;
use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\MainCategory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Slider;
use App\Services\ProductService;
use App\Traits\DeleteTrait;
use App\Traits\GeneralTrait;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    use ImageTrait, DeleteTrait, GeneralTrait;

    protected $productService;
    private $colors = ['احمر', 'ازرق', 'اسود', 'بني', 'اصفر', 'ابيض', 'اخضر'];
    private $sizes = ['كبير', 'متوسط', 'صغير'];

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $categories = Category::all(); // Changed from MainCategory to Category
        return view('admin.Product.index', compact('categories'));
    }

    public function datatable(Request $request)
    {
        try {
            $query = Product::select('products.*')
                ->with(['mainCategory', 'category'])
                ->join('product_translations as t', function ($join) {
                    $join->on('products.id', '=', 't.product_id')
                        ->where('t.locale', app()->getLocale());
                })
                ->orderBy('products.created_at', 'desc');

            // Apply filters
            if ($request->filled('name')) {
                $searchTerm = $request->name;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('t.name', 'like', '%' . $searchTerm . '%');
                });
            }

            if ($request->filled('main_category_id')) {
                $query->where('main_category_id', $request->main_category_id);
            }

            // Default to showing active products (is_deleted = 0) if status is not specified
            if ($request->filled('status')) {
                $query->where('is_deleted', $request->status);
            } else {
                $query->where('is_deleted', 0);
            }

            if ($request->filled('best_seller') && $request->best_seller !== 'all') {
                $query->where('best_seller', $request->best_seller);
            }

            return DataTables::eloquent($query)
                ->addColumn('id', function ($row) {
                    return $row->id;
                })
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('description', function ($row) {
                    return $row->description;
                })
                ->addColumn('short_name', function ($row) {
                    return $row->short_name ?? '';
                })
                ->addColumn('price', function ($row) {
                    return $row->price;
                })
                ->addColumn('quantity', function ($row) {
                    return $row->quantity;
                })
                ->addColumn('main_category_id', function ($row) {
                    return $row->mainCategory->name ?? '';
                })
                ->addColumn('category_id', function ($row) {
                    return $row->category->title ?? '';
                })
                ->addColumn('slider_id', function ($row) {
                    return $row->slider_id ?? '';
                })
                ->addColumn('brand_id', function ($row) {
                    return $row->brand_id ?? '';
                })
                ->addColumn('best_seller', function ($row) {
                    return $row->best_seller
                        ? '<span class="badge bg-success">نعم</span>'
                        : '<span class="badge bg-danger">لا</span>';
                })
                ->addColumn('is_deleted', function ($row) {
                    return $row->is_deleted
                        ? '<span class="badge bg-danger">مخفى</span>'
                        : '<span class="badge bg-success">نشط</span>';
                })
                ->addColumn('state', function ($row) {
                    return $row->state
                        ? '<span class="badge bg-success">متوفر</span>'
                        : '<span class="badge bg-danger">غير متوفر</span>';
                })
                ->addColumn('photo', function ($row) {
                    return $row->image_path
                        ? '<img src="' . $row->image_path . '" alt="product-image" style="height:120px;width:150px" class="avatar rounded me-2">'
                        : '';
                })
                ->addColumn('operation', function ($row) {
                    $edit = '<a href="' . route('dashboard.product.edit', $row->id) . '" type="button" class="btn btn-lg me-2 btn-block btn-success lift text-uppercase ">تعديل</a>';

                    // Show either hide or show button based on is_deleted status
                    $toggleButton = '';
                    if ($row->is_deleted) {
                        $toggleButton = '<button data-id="' . $row->id . '" data-action="show" class="btn btn-lg me-2 btn-block btn-info lift text-uppercase text-white toggle-visibility-btn">إظهار</button>';
                    } else {
                        $toggleButton = '<button data-id="' . $row->id . '" data-action="hide" class="btn btn-lg me-2 btn-block btn-warning lift text-uppercase text-white toggle-visibility-btn">إخفاء</button>';
                    }

                    $forceDelete = '<button data-id="' . $row->id . '" class="btn btn-lg me-2 btn-block btn-danger lift text-uppercase force-delete-btn">حذف نهائي</button>';
                    return $edit . $toggleButton . $forceDelete;
                })
                ->rawColumns(['operation', 'photo', 'best_seller', 'is_deleted', 'state'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function create()
    {
        $main_categories = MainCategory::orderBy('name', 'ASC')->get(); // Fetch main categories
        $categories = Category::get();
        $brands = Brand::get();
        $sliders = Slider::get();
        $colors = $this->colors;
        $sizes = $this->sizes;

        return view('admin.Product.create', compact('main_categories', 'categories', 'brands', 'sliders', 'colors', 'sizes'));
    }


    public function store(ProductRequest $request)
    {
        try {
            $data = $request->only('name:ar', 'name:en', 'slider_id', "short_name", 'commit', 'description:ar', 'description:en', 'quantity', 'price', "tax", "slowTax", 'final_price', 'main_category_id', 'category_id', 'child_cat_id', 'brand_id', 'offer_type', 'offer_value', 'have_offer', 'sizes', 'colors', 'max_qty_for_order');
            // Handle best_seller field
            $data['best_seller'] = $request->input('best_seller', 0); // Default to 0 if not provided

            $product = $this->productService->save($request, $data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $destinationPath = public_path('storage/images');
                    $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move($destinationPath, $filename);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => "/images/" . $filename,
                    ]);
                }
            }

            return $this->JsonData($product);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function edit($id)
    {
        $product = Product::FindOrFail($id);
        $main_categories = MainCategory::orderBy('name', 'ASC')->get();
        $categories = Category::get();
        $sliders = Slider::get();
        $brands = Brand::get();
        $colors = $this->colors;
        $sizes = $this->sizes;
        return view('admin.Product.edit', compact('product', 'main_categories', 'categories', 'brands', 'sliders', 'sizes', 'colors'));
    }


    public function update(EditProductRequest $request)
    {
        $product = Product::findOrFail($request->product_id);

        $data = $request->only('name:ar', 'name:en', 'slider_id', "short_name", 'commit', 'description:ar', 'description:en', 'price', "tax", 'slowTax', "quantity", 'main_category_id', 'child_cat_id', 'category_id', 'brand_id', 'offer_type', 'offer_value', 'have_offer', 'sizes', 'colors', 'max_qty_for_order');
        $data['best_seller'] = $request->best_seller;
        $data['is_deleted'] = $request->is_deleted;
        $data['state'] = $request->state;


        $product = $this->productService->update($request, $product, $data);
        return response()->json(['message' => 'Product updated successfully!']);
    }


    public function softDelete(Request $request)
    {
        try {
            $product = Product::findOrFail($request->id);
            $action = $request->input('action', 'hide');
            $isDeleted = $action === 'hide' ? 1 : 0;

            $product->update(['is_deleted' => $isDeleted]);

            $message = $isDeleted
                ? 'تم إخفاء المنتج بنجاح'
                : 'تم إظهار المنتج بنجاح';

            return response()->json([
                'success' => true,
                'message' => $message,
                'id' => $request->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error moving product to trash: ' . $e->getMessage()
            ], 500);
        }
    }

    public function forceDelete(Request $request)
    {
        try {
            $product = Product::findOrFail($request->id);
            $product->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Product permanently deleted successfully!',
                'id' => $request->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error permanently deleting product: ' . $e->getMessage()
            ], 500);
        }
    }
}
