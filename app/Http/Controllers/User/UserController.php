<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\MainCategory;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\Slider;
use App\Models\Stage;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Voucher;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log; // Update Log facade import
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    private $colors = ['احمر', 'ازرق', 'اسود', 'بني', 'اصفر', 'ابيض', 'اخضر'];
    private $sizes = ['كبير', 'متوسط', 'صغير'];

    public function index()
    {
        $sliders = Slider::with('translations')->get();
        $products = Product::with('translations')
            ->where("state", true)
            ->where('is_deleted', false) // ✅ Hide deleted products
            ->orderBy('created_at', 'desc')->take(8)->get();

        $mostOrderedProducts = Product::with('translations')
            ->where("state", true)
            ->where('products.best_seller', 1)
            ->where('is_deleted', false) // ✅ Hide deleted products
            ->orderBy('created_at', 'desc')->take(8)->get();

        $teachers = Brand::with('translations')->get();
        $categories = Category::with('translations')->get();
        $stages = Stage::with('translations')->get();
        $main_categories = MainCategory::orderBy('name', 'ASC')->get();
        $offers = Offer::orderBy('created_at', 'desc')->get();


        return view('user.index', compact(
            'sliders',
            'products',
            'mostOrderedProducts',
            'teachers',
            'categories',
            'stages',
            'main_categories',
            'offers',
        ));
    }


    public function fqa()
    {
        return view('user.fqa');
    }

    public function shop(Request $request)
    {

        $sliderIds = [];
        if ($request->stage_id && !$request->slider_id) {
            $sliderIds = Slider::where('stage_id', $request->stage_id)->pluck('id')->toArray();
        }

        $products = Product::with('translations')
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->title, fn($q) => $q->whereTranslationLike('name', '%' . $request->title . '%'))
            ->when($request->brand_id, fn($q) => $q->where('brand_id', $request->brand_id))
            ->when($request->slider_id, fn($q) => $q->where('slider_id', $request->slider_id))
            ->when($request->stage_id && !$request->slider_id, fn($q) => $q->whereIn('slider_id', $sliderIds))
            ->when($request->main_category_id, fn($q) => $q->where('main_category_id', $request->main_category_id))
            ->when($request->size, fn($q) => $q->whereJsonContains('sizes', $request->size))
            ->when($request->color, fn($q) => $q->whereJsonContains('colors', $request->color))
            ->orderBy('is_deleted', 'asc') // Show active products first
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $teachers = Brand::with('translations')->get();
        $categories = Category::with('translations')->get();
        $sliders = Slider::with('translations')->get();
        $stages = Stage::with('translations')->get();
        $main_categories = MainCategory::orderBy('name', 'ASC')->get();
        $sizes = $this->sizes;
        $colors = $this->colors;

        return view('user.shop', compact('products', 'teachers', 'categories', 'sliders', 'stages', 'main_categories', 'sizes', 'colors'));
    }


    public function vouchers(Request $request)
    {
        $name = $request->query('name', '');
        $type = $request->query('type', '');

        $coupons = Coupon::when($name, function ($query, $name) {
            $query->where('name', 'LIKE', '%' . e($name) . '%');
        })->when($type, function ($query, $type) {
            $query->where('type', $type);
        })->orderBy('created_at', 'desc')->get();

        return view('user.vouchers', compact('coupons'));
    }


    public function productDetail($id)
    {
        $product = Product::where('id', $id)->where('is_deleted', false)->first();

        if (!$product) {
            abort(404, 'المنتج غير متاح.');
        }

        return view('user.details', compact('product'));
    }


    public function contactUs()
    {
        return view('user.contact');
    }

    public function card(Request $request)
    {
        $user = auth()->user();
        $cartItems = Cart::instance('shopping')->content();

        // ✅ Check if any item in the cart is deleted
        foreach ($cartItems as $item) {
            $product = Product::find($item->id);
            if (!$product || $product->is_deleted) {
                Cart::instance('shopping')->remove($item->rowId);
            }
        }

        if (count(Cart::instance('shopping')->content()) == 0) {
            return redirect(env('APP_URL'))->with('warning', 'تم حذف المنتجات الغير متاحة.');
        }

        $orders = null; // Keep the variable name 'orders' for the view

        // Check if an address ID is provided (for saved addresses)
        if ($request->has('id') && !empty($request->id)) {
            $orders = \App\Models\UserAddress::find($request->id);
        }
        // Otherwise, check for address details in the query string (for temporary addresses)
        elseif ($request->has('name') && $request->has('mobile')) { // check for core fields
            // Create a new UserAddress instance from the request data, but don't save it.
            $orders = new \App\Models\UserAddress($request->only([
                'name',
                'mobile',
                'temp_mobile',
                'governorate',
                'city',
                'address',
                'near_post'
            ]));
        }

        $shippingMethods = ShippingMethod::all();
        $governoratesData = json_decode(File::get(storage_path('cities/governorates.json')), true);
        $citiesData = json_decode(File::get(storage_path('cities/cities.json')), true);

        return view('user.card', compact('governoratesData', 'citiesData', 'orders', 'shippingMethods'));
    }


    public function cardData()
    {
        $user = auth()->user();

        // 1. Get saved addresses
        $savedAddresses = \App\Models\UserAddress::where('user_id', $user->id)->get();

        // 2. Get addresses from the last 3 orders and parse them
        $orderAddresses = \App\Models\Order::where('user_id', $user->id)
            ->whereHas('shipping', function ($query) {
                $query->where('type', '!=', 'branch');
            })
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($order) {
                $governorate = null;
                $city = null;

                // The detailed address is in `address2`.
                $detailedAddress = $order->address2;

                // `address` contains the combined governorate and city string.
                $govCityString = $order->address;

                // Split the governorate and city string.
                $addressParts = preg_split('/(\s*-\s*|\s*,\s*)/', $govCityString, 2);

                if (count($addressParts) === 2) {
                    $governorate = trim($addressParts[0]);
                    $city = trim($addressParts[1]);
                } elseif (count($addressParts) === 1) {
                    // If only one part, assume it's the governorate.
                    $governorate = trim($addressParts[0]);
                }

                $mobile = $order->mobile;
                if ($mobile && substr($mobile, 0, 1) !== '0') {
                    $mobile = '0' . $mobile;
                }

                $temp_mobile = $order->temp_mobile;
                if ($temp_mobile && substr($temp_mobile, 0, 1) !== '0') {
                    $temp_mobile = '0' . $temp_mobile;
                }

                return new \App\Models\UserAddress([
                    'name' => $order->name,
                    'mobile' => $mobile,
                    'temp_mobile' => $temp_mobile,
                    'governorate' => $governorate,
                    'city' => $city,
                    'address' => $detailedAddress, // Use the content of address2
                    'near_post' => $order->near_post,
                ]);
            });

        // 3. Merge and make unique
        $allAddresses = $savedAddresses->concat($orderAddresses);
        $uniqueAddresses = $allAddresses->unique(function ($address) {
            // Make uniqueness check more robust
            return trim($address->name) . trim($address->mobile) . trim($address->governorate) . trim($address->city) . trim($address->address);
        });

        return view('user.card_data', ['addresses' => $uniqueAddresses->values()]);
    }


    public function myorders()
    {
        $user = auth()->user();
        $orders = Order::where("user_id", $user->id)->orderBy("created_at", "desc")->get();
        if (!$orders) {
            abort(404);
        }

        // Count successful and cancelled orders
        $successCount = $orders->where('status', 'success')->count();
        $cancelledCount = $orders->where('status', 'cancelled')->count();
        $reservedCount = $orders->where('status', 'reserved')->count();

        $totalOrders = $orders->count();

        // Calculate success percentage
        $successPercentage = ($totalOrders > 0) ? round(($successCount / $totalOrders) * 100, 2) : 0;

        return view("user.myorders", compact('orders', 'successCount', 'cancelledCount', 'reservedCount', 'successPercentage'));
    }

    public function editOrder($id)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        // Only allow editing if order is in a modifiable state
        if (!in_array($order->status, ['new', 'pending'])) {
            return redirect()->back()->with('error', 'لا يمكن تعديل الطلب في حالته الحالية');
        }

        return view('user.order.edit', [
            'order' => $order
        ]);
    }

    public function updateOrder(Request $request, $id)
    {

        $order = Order::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();


        // Only allow updating if order is in a modifiable state
        if (!in_array($order->status, ['new', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعديل الطلب في حالته الحالية. حالة الطلب: ' . $order->status,
                'current_status' => $order->status // Add this line
            ], 403);
        }

        // Rest of your code...

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'address2' => 'nullable|string|max:1000',
            'near_post' => 'nullable|string|max:1000'
        ], [
            'name.required' => 'حقل الاسم مطلوب',
            'phone.required' => 'حقل الهاتف مطلوب',
            'address.required' => 'حقل العنوان مطلوب'
        ]);

        // Update order with validated data
        $order->update([
            'name' => $validated['name'],
            'mobile' => $validated['phone'],
            'address' => $validated['address'],
            'address2' => $validated['address2'] ?? null,
            'near_post' => $validated['near_post'] ?? null
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الطلب',
                'redirect' => route('user.order.details', $order->id)
            ]);
        }

        return redirect()->route('user.order.details', $order->id)->with('success', 'تم التحديث');
    }
    public function myvouchers()
    {
        $user = auth()->user();
        $vouchers = Voucher::where('is_used', 1)->where("user_id", $user->id)->orderBy('updated_at', 'desc')->get();
        return view("user.myvouchers", compact("vouchers"));
    }
    public function order_details($id)
    {
        $order = Order::findorfail($id);
        return view('user.orderdetails', compact('order'));
    }

    public function login()
    {
        Session::put('url.intended', URL::previous());
        return view('user.login_register');
    }

    public function loginSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6',
        ], [
            'email.exists' => 'البريد الإلكتروني الذي أدخلته غير مسجل لدينا.',
            'password.min' => 'كلمة المرور يجب أن تحتوي على 6 أحرف على الأقل.',
        ]);
        $credentials = $request->only('email', 'password');

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            Session::put('user', $request->email);
            if (Session::get('url.intended')) {
                // toastr()->success('Successfuly Login');
                return Redirect::to(Session::get('url.intended'));
            } else {
                // toastr()->success('Successfuly Login');
                return redirect()->route('front.home');
            }
        } else {
            // toastr()->error('Invaild Email & Password');
            return redirect()->back();
        }
    }

    public function registerSubmit(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'string|required',
                'email' => 'required|unique:users,email',
                // 'phone' => 'required|numeric|regex:/^(01[0125])[0-9]{8}$/',
                // 'address' => 'required',
                'password' => 'min:6|required|confirmed',
            ], [
                'name.required' => 'الاسم مطلوب.',
                'email.required' => 'البريد الإلكتروني مطلوب.',
                'email.unique' => 'البريد الإلكتروني مسجل لدينا مسبقاً.',
                // 'phone.required' => 'رقم الهاتف مطلوب.',
                // 'address.required' => 'العنوان مطلوب.',
                'password.required' => 'كلمة المرور مطلوبة.',
                'password.min' => 'كلمة المرور يجب أن تكون على الأقل 6 حروف.',
                'password.confirmed' => 'كلمة المرور غير مطابقة.',
                // 'phone.regex' => 'رقم الهاتف يجب أن يتكون من 11 رقم.',
                // 'phone.numeric' => 'رقم الهاتف يجب أن يحتوي على ارقام فقط.',
            ]);

            $validatedData['password'] = bcrypt($request->password);
            $validatedData['address'] = "";
            $validatedData['phone'] = "";
            $user = User::create($validatedData);

            Auth::login($user, true);

            return redirect()->route('user.home');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            $customErrors = array_map(function ($error) {
                return $error . ' حاول مرة أخرى';
            }, $errors);

            return redirect()->back()->withErrors($customErrors);
        }
    }

    public function register()
    {
        Session::put('url.intended', URL::previous());
        return view('user.register');
    }

    public function edit()
    {
        $user = Auth()->user();
        return view('user.myaccount', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth()->user();
        $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|numeric|regex:/^(01[0125])[0-9]{8}$/',
            'password' => 'nullable|min:6',
            'profile_image' => 'nullable|image',
            // 'address' => 'required',
        ]);
        $data = $request->only('name', 'email', 'phone', 'address', 'profile_image');
        if ($request->email == $user->email) {
            Arr::forget($data, 'email');
        }
        if ($request->phone == null) {
            Arr::forget($data, 'phone');
        }

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image != 'default.png') {
                Storage::delete('public/images/user/' . $user->profile_image);
            }
            $image = $request->file('profile_image');
            $destinationPath = public_path('storage/images/user');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $filename);
            $data['profile_image'] = $filename;
        }

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return response()->json($user->makeHidden('password'), 200);
    }

    public function userLogout()
    {
        Session::forget('user');
        Auth::logout();
        // toastr()->success('Successfuly Logout');
        return redirect()->route('user.home');
    }
}
