<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Models\Product;
use App\Models\Category;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\BrandResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SubCategoryResource;
use App\Models\Brand;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ImageTrait;

    public function __construct() {
        $this->middleware('auth:api',['except'=>['login','register']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        if($validator->fails()) {
            return response()->json($validator->errors()->first(),422);
        }
        if(!$token=auth('api')->attempt($validator->validated())) {
            return response()->json(['error'=>'Unauthorized',401]);
        }
        return $this->createNewToken($token);
    }

    public function createNewToken($token) {
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth('api')->factory()->getTTL()*60,
            'user'=>auth('api')->user()
        ]);
    }

    public function profile() {
        return response()->json(auth('api')->user());
    }

    public function logout() {
        auth('api')->logout();
        return response()->json([
            'message'=>'admin logged out',
        ],201);
    }

    public function updateAdmin(Request $request)
    {
        $admin = Admin::find(auth('api')->id());
        if(!$admin) {
            return response()->json(['error' => 'Admin not found'], 404);
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|unique:admins,email,' . $admin->id,
            'photo' => 'nullable'
        ]);
        if($validator->fails()) {
            return response()->json($validator->errors(),422);
        }
        if ($request->hasFile('photo')) {
            Storage::delete('public/images/admins/' . $admin->photo);
            $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/admins');
            $validator['photo'] = $image_name;
        }
        $validatedData = $validator->validated();
        $admin->update($validatedData);
        return response()->json([
            'admin' => $admin,
            'message' => 'Admin updated successfully',
        ], 200);
    }

    public function index()
    {
        $products = Product::get();
        return response()->json([
            'status' => 'success',
            'message' => 'All Products',
            'data' => $products,
        ], 201);
    }

    public function store(Request $request , ProductService $productService)
    {
        $validator = Validator::make($request->all(),[
            'name:ar'=>'string|required|unique:product_translations,name',
            'name:en'=>'string|required|unique:product_translations,name',
            'description:ar'=>'string|required',
            'description:en'=>'string|required',
            'price'=>'required|numeric|min:0|max:10000',
            'have_offer' => 'required',
            'offer_type' => 'required_if:have_offer,1|in:percentage,value',
            'offer_value' => 'required_if:have_offer,1|numeric|min:0',
            'photo' => 'nullable|mimes:png,jpg',
            'category_id'=>'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'brand_id'=>'required|exists:brands,id',
            'final_price' => 'required',
        ]);
        if($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        $data = $request->only('name:ar', 'name:en', 'description:ar', 'description:en', 'price', 'final_price', 'category_id', 'child_cat_id', 'brand_id', 'offer_type', 'offer_value', 'have_offer');
        $product = $productService->save($request,$data);

        // if ($request->hasFile('photo')) {
        //     $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/products');
        //     $validator['photo'] = $image_name;
        // }
        // $validator['offer_type'] = $request->input('offer_type', 'value');
        // $validator['offer_value'] = $request->input('offer_value', 0);
        // $validatedData = $validator->validated();
        // $product = Product::create($validatedData);

        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }

    public function category()
    {
        $categories = Category::where('is_parent', 1)->get();
        return CategoryResource::collection($categories);
    }
    public function subcategory()
    {
        $subcategories = Category::where('is_parent', 0)->get();
        return SubCategoryResource::collection($subcategories);
    }
    public function brand()
    {
        $brands = Brand::get();
        return BrandResource::collection($brands);
    }
}
