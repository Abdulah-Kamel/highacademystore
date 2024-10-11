<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Stage;
use App\Models\Slider;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;

class UserController extends Controller
{
    public function index()
    {
        $sliders = Slider::with('translations')->get();
        $products = Product::with('translations')->orderBy('created_at', 'desc')->take(8)->get();
        $teachers = Brand::with('translations')->get();
        $categories = Category::with('translations')->get();
        $stages = Stage::with('translations')->get();
        return view('user.index', compact('sliders', 'products', 'teachers', 'categories', 'stages'));
    }

    public function shop(Request $request)
    {
        // الحصول على معرّفات الصفوف الدراسية المرتبطة بالمرحلة المختارة
        $sliderIds = [];
        if ($request->stage_id && !$request->slider_id) {
            $sliderIds = Slider::where('stage_id', $request->stage_id)->pluck('id')->toArray();
        }

        $products = Product::with('translations')
            ->when($request->category_id, function ($q) use ($request) {
                return $q->where('category_id', $request->category_id);
            })
            ->when($request->title, function ($q) use ($request) {
                return $q->where(function ($query) use ($request) {
                    $query->whereTranslationLike('name', '%' . $request->title . '%');
                });
            })
            ->when($request->brand_id, function ($q) use ($request) {
                return $q->where('brand_id', $request->brand_id);
            })
            ->when($request->slider_id, function ($q) use ($request) {
                return $q->where('slider_id', $request->slider_id);
            })
            // إذا تم تحديد المرحلة الدراسية فقط بدون تحديد فصل دراسي
            ->when($request->stage_id && !$request->slider_id, function ($q) use ($sliderIds) {
                return $q->whereIn('slider_id', $sliderIds);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $teachers = Brand::with('translations')->get();
        $categories = Category::with('translations')->get();
        $sliders = Slider::with('translations')->get();
        $stages = Stage::with('translations')->get();

        return view('user.shop', compact('products', 'teachers', 'categories', 'sliders', 'stages'));
    }


    public function productDetail($id)
    {
        $product = Product::FindOrFail($id);
       // return $product->brands;
        return view('user.details', compact('product'));
    }

    public function contactUs()
    {
        return view('user.contact');
    }

    public function card()
    {
        if (count(Cart::instance('shopping')->content()) == 0) {
            return redirect(env('APP_URL'));
        }
        $governoratesData = json_decode(File::get(storage_path('cities/governorates.json')), true);
        $citiesData = json_decode(File::get(storage_path('cities/cities.json')), true);
        return view('user.card', compact('governoratesData', 'citiesData'));
    }


    public function myorders()
    {
        $user = auth()->user();
        $orders = Order::where("user_id", $user->id)->orderBy("created_at", "desc")->get();
        if (!$orders) {
            abort(404);
        }
        return view("user.myorders", compact("orders"));
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

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
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
        // dd($request->all());
        $this->validate(
            $request,
            [
                'name' => 'string|required',
                'email' => 'required|unique:users',
                'phone' => 'required|numeric',
                'address' => 'required',
                'password' => 'min:6|required|confirmed',
            ]
        );
        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $check = User::create($data);
        // Session::put('user',$data['email']);
        Auth::login($check);

        if ($check) {
            // toastr()->success('Successfuly Register');
            return redirect()->route('user.home');
        } else {
            // toastr()->error('An error has occurred please try again later.');
            return redirect()->back();
        }
    }

    public function userLogout()
    {
        Session::forget('user');
        Auth::logout();
        // toastr()->success('Successfuly Logout');
        return redirect()->route('user.home');
    }

}
