<?php

namespace App\Http\Controllers\MinAdmin;

use App\Models\Brand;
use App\Models\Slider;
use App\Models\Product;
use App\Models\Category;
use App\Traits\ImageTrait;
use App\Traits\DeleteTrait;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditProductRequest;
use App\Http\Requests\ProductRequest;
use App\Models\MainCategory;
use App\Models\ProductImage;
use App\Services\ProductService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MiniProductController extends Controller
{
    use ImageTrait, DeleteTrait, GeneralTrait;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        return view('min_admin.Product.index');
    }

    public function datatable()
    {
        $products = $this->productService->findAll();

        return DataTables::of($products)
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('description', function ($row) {
                return $row->description;
            })
            ->addColumn('price', function ($row) {
                return $row->price;
            })
            ->addColumn('tax', function ($row) {
                return $row->tax;
            })
            ->addColumn('slowTax', function ($row) {
                return $row->slowTax;
            })
            ->addColumn('quantity', function ($row) {
                return $row->quantity;
            })
            ->addColumn('short_name', function ($row) {
                return $row->short_name ?? "";
            })
            ->addColumn('commit', function ($row) {
                return $row->commit ?? NULL;
            })
            ->addColumn('offer_type', function ($row) {
                return $row->offer_type == 'percentage' ? "%" . $row->offer_value : "L.E" . $row->offer_value;
            })
            ->addColumn('main_category_id', function ($row) {
                return $row->mainCategory->name ?? "No Main Category";
            })
            ->addColumn('category_id', function ($row) {
                return $row->category->title ?? "";
            })
            ->addColumn('best_seller', function ($row) {
                return $row->best_seller
                    ? '<span class="badge bg-success">Yes</span>'
                    : '<span class="badge bg-danger">No</span>';
            })
            ->addColumn('is_deleted', function ($row) {
                return $row->is_deleted
                    ? '<span class="badge bg-danger">Deleted</span>'
                    : '<span class="badge bg-success">Active</span>';
            })
            ->addColumn('state', function ($row) {
                return $row->state
                    ? '<span class="badge bg-success">متوفر</span>'
                    : '<span class="badge bg-danger">غير متوفر</span>';
            })
            ->addColumn('photo', function ($row) {
                return '<img src ="' . $row->image_path . '" alt="product-image" style="height:120px;width:150px" class="avatar rounded me-2">';
            })
            ->addColumn('operation', function ($row) {
                $delete = '<a product_id=' . $row->id . ' class="btn btn-lg btn-block btn-danger lift text-uppercase delete_btn"> حذف</a>';
                $edit = '<a href="' . route('dashboard.mini.product.edit', $row->id) . '" type="button" class="btn btn-lg btn-block btn-success lift text-uppercase">تعديل</a>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['operation', 'photo', 'best_seller', 'is_deleted', 'state'])
            ->toJson();
    }

    public function create()
    {
        $main_categories = MainCategory::orderBy('name', 'ASC')->get(); // Fetch main categories
        $categories = Category::get();
        $brands = Brand::get();
        $sliders = Slider::get();

        return view('min_admin.Product.create', compact('main_categories', 'categories', 'brands', 'sliders'));
    }


    public function store(ProductRequest $request)
    {
        try {
            $data = $request->only('name:ar', 'name:en', 'slider_id', "short_name", 'commit', 'description:ar', 'description:en', 'quantity', 'price', "tax", "slowTax", 'final_price', 'main_category_id', 'category_id', 'child_cat_id', 'brand_id', 'offer_type', 'offer_value', 'have_offer');

            // Handle best_seller field
            $data['best_seller'] = $request->has('best_seller') ? 1 : 0;

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
        $main_categories = MainCategory::orderBy('name', 'ASC')->get(); // Fetch main categories
        $categories = Category::get();
        $sliders = Slider::get();
        $brands = Brand::get();

        return view('min_admin.Product.edit', compact('product', 'main_categories', 'categories', 'brands', 'sliders'));
    }


    public function update(EditProductRequest $request)
    {
        $product = Product::findOrFail($request->product_id);

        $data = $request->only('name:ar', 'name:en', 'slider_id', "short_name", 'commit', 'description:ar', 'description:en', 'price', "tax", 'slowTax', "quantity", 'main_category_id', 'child_cat_id', 'category_id', 'brand_id', 'offer_type', 'offer_value', 'have_offer');

        $data['best_seller'] = $request->best_seller;
        $data['is_deleted'] = $request->is_deleted;
        $data['state'] = $request->state;


        $product = $this->productService->update($request, $product, $data);
        return response()->json(['message' => 'Product updated successfully!']);
    }


    public function destroy(Request $request)
    {
        try {
            $product = Product::findOrFail($request->id);
            $product->update(['is_deleted' => 1]); // ✅ Soft delete the product

            return response()->json([
                'success' => true,
                'message' => 'Product soft deleted successfully!',
                'id' => $request->id // Send back the ID to remove it from the table
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
    }
}
