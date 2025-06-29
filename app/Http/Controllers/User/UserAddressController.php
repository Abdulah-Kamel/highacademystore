<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class UserAddressController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userid = $user->id;
        $addresses = UserAddress::where('user_id', $userid)->get();

        return view('user.Shipping_Address.index', compact('addresses'));
    }
    public function create()
    {
        $governoratesData = json_decode(File::get(storage_path('cities/governorates.json')), true);
        $citiesData = json_decode(File::get(storage_path('cities/cities.json')), true);
        return view('user.Shipping_Address.create', compact('governoratesData', 'citiesData'));
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate(
            [
                'address' => ['required', 'string'],
                'governorate' => [
                    'required',
                ],
                'city' => [
                    'required',
                ],
                'near_post' => [
                    'required',
                    'string',
                ],
                'name' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        $nameParts = explode(' ', trim($value));
                        if (count($nameParts) < 3) {
                            $fail('يجب ان يكون الاسم ثلاثى');
                        }
                        foreach ($nameParts as $word) {
                            if (!preg_match('/^[\p{Arabic}]+$/u', $word)) {
                                $fail('يجب أن يحتوي الاسم على أحرف عربية فقط');
                            }
                        }
                    }
                ],
                'mobile' => [
                    'required',
                    'regex:/^(01[0125])[0-9]{8}$/'
                ],
                'temp_mobile' => [
                    'required',
                    'regex:/^(01[0125])[0-9]{8}$/'
                ],
            ],
            [
                'governorate.required' => 'برجاء اختيار المحافظة',
                'city.required' =>  'برجاء اختيار المدينة',
                'name.required' => 'برجاء ادخال الاسم',
                'name.string' => 'برجاء ادخال الاسم',
                'name.regex' => 'برجاء ادخال الاسم',
                'mobile.required' => 'برجاء ادخال رقم الموبايل',
                'mobile.regex' => 'رقم الهاتف غير صحيح',
                'temp_mobile.required' => 'برجاء ادخال رقم الموبايل الاحتياطي',
                'temp_mobile.regex' => 'رقم الهاتف الاحتياطي غير صحيح',
                'address.required' => 'برجاء ادخال العنوان',
                'address.string' => 'برجاء ادخال العنوان',
                'near_post.required' => 'برجاء ادخال اسم اقرب مكتب بريد',
            ]
        );

        $address = new UserAddress();
        $address->fill($data);

        $user->address()->save($address);

        return response()->json(['message' => 'Address created successfully']);
    }
    public function edit($id)
    {
        $governoratesData = json_decode(File::get(storage_path('cities/governorates.json')), true);
        $citiesData = json_decode(File::get(storage_path('cities/cities.json')), true);
        $address = UserAddress::find($id);
        return view('user.Shipping_Address.edit', compact('governoratesData', 'citiesData', 'address'));
    }
    public function update(Request $request)
    {
        $data = $request->validate(
            [
                'id' => ['required', 'exists:user_addresses,id'],
                'address' => ['required', 'string'],
                'governorate' => ['required'],
                'city' => ['required'],
                'near_post' => ['required', 'string'],
                'name' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        $nameParts = explode(' ', trim($value));
                        if (count($nameParts) < 3) {
                            $fail('يجب ان يكون الاسم ثلاثى');
                        }
                        foreach ($nameParts as $word) {
                            if (!preg_match('/^[\p{Arabic}]+$/u', $word)) {
                                $fail('يجب أن يحتوي الاسم على أحرف عربية فقط');
                            }
                        }
                    }
                ],
                'mobile' => [
                    'required',
                    'regex:/^(01[0125])[0-9]{8}$/'
                ],
                'temp_mobile' => [
                    'required',
                    'regex:/^(01[0125])[0-9]{8}$/'
                ],
            ],
            [
                'governorate.required' => 'برجاء اختيار المحافظة',
                'city.required' =>  'برجاء اختيار المدينة',
                'name.required' => 'برجاء ادخال الاسم',
                'name.string' => 'برجاء ادخال الاسم',
                'name.regex' => 'برجاء ادخال الاسم',
                'mobile.required' => 'برجاء ادخال رقم الموبايل',
                'mobile.regex' => 'رقم الهاتف غير صحيح',
                'temp_mobile.required' => 'برجاء ادخال رقم الموبايل الاحتياطي',
                'temp_mobile.regex' => 'رقم الهاتف الاحتياطي غير صحيح',
                'address.required' => 'برجاء ادخال العنوان',
                'address.string' => 'برجاء ادخال العنوان',
                'near_post.required' => 'برجاء ادخال اسم اقرب مكتب بريد',
            ]
        );

        $address = UserAddress::find($data['id']);
        $address->fill(Arr::except($data, ['id']));

        auth()->user()->address()->save($address);

        return response()->json(['message' => 'Address updated successfully']);
    }

    public function destroy()
    {
        $user = auth()->user();
        $data = request()->validate([
            'id' => ['required', 'exists:user_addresses,id'],
        ]);

        $address = UserAddress::find($data['id']);

        $address->delete();
        return response()->json(['message' => 'Address deleted successfully']);
    }
}
