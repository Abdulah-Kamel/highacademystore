<?php

namespace App\Http\Controllers\Admin;

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
use App\Services\ProductService;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    use ImageTrait, DeleteTrait, GeneralTrait;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        return view('admin.Product.index');
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
            ->addColumn('offer_type', function ($row) {
                return $row->offer_type == 'percentage' ? "%" . $row->offer_value : "L.E" . $row->offer_value;
            })
            ->addColumn('ProfitPercent', function ($row) {
                return $row->ProfitPercent;
            })
            ->addColumn('category_id', function ($row) {
                return $row->category->title ?? "";
            })
            ->addColumn('child_cat_id', function ($row) {
                return $row->child_cat_id == NULL ? 'no sub category' : $row->sub_cat_info->title;
            })
            ->addColumn('brand_id', function ($row) {
                return $row->brands->title ?? "";
            })
            ->addColumn('slider_id', function ($row) {
                return $row->sliders->title ?? "";
            })
            ->addColumn('photo', function ($row) {
                $image = '<img src ="' . $row->image_path . '" alt="profile-image" style="height:120px;width:150px" class="avatar rounded me-2" >';
                return $image;
            })
            ->addColumn('operation', function ($row) {
                $delete = '<a product_id=' . $row->id . ' class="btn btn-lg btn-block btn-danger lift text-uppercase delete_btn"> حذف</a>';
                $edit = '<a href="' . route('dashboard.product.edit', $row->id) . '" type="button" class="btn btn-lg btn-block btn-success lift text-uppercase">تعديل</a>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['operation' => 'operation', 'photo' => 'photo'])
            ->toJson();
    }

    public function create()
    {
        $categories = Category::get();
        $brands = Brand::get();
        $sliders = Slider::get();
        return view('admin.Product.create', compact('categories', 'brands', 'sliders'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->only('name:ar', 'name:en', 'slider_id', 'description:ar', 'description:en', 'price', 'final_price', 'category_id', 'child_cat_id', 'brand_id', 'offer_type', 'offer_value', 'have_offer');
        $product = $this->productService->save($request, $data);
        return $this->JsonData($product);
    }

    public function edit($id)
    {
        $product = Product::FindOrFail($id);
        $categories = Category::get();
        $sliders = Slider::get();
        $brands = Brand::get();
        return view('admin.Product.edit', compact('product', 'categories', 'brands', 'sliders'));
    }

    public function update(EditProductRequest $request)
    {

        $product = Product::FindOrFail($request->product_id);

        $data = $request->only('name:ar', 'name:en', 'slider_id', 'description:ar', 'description:en', 'price', 'child_cat_id', 'category_id', 'brand_id', 'offer_type', 'offer_value', 'have_offer');
        $data['state'] = $request->has('state') && $request->state === 'on' ? 1 : 0;
        $product = $this->productService->update($request, $product, $data);
        //return $data;
        return $this->JsonData($product);
    }


    public function destroy(Request $request)
    {
        $product = Product::FindOrFail($request->id);
        if ($product->photo != 'default.png') {
            Storage::delete('public/images/products/' . $product->photo);
        }
        return $this->Delete($request->id, $product);
    }
}
