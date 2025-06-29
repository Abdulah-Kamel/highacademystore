<?php

namespace App\Http\Controllers\Admin;

use toastr;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{
    use ImageTrait;

    public function index()
    {
        return view('admin.index');
    }

    public function register()
    {
        return view('admin.auth.register');
    }

    public function signup(AdminRequest $request)
    {
        $data = $request->only('name', 'email');
        if ($request->hasFile('photo')) {
            $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/admins');
            $data['photo'] = $image_name;
        }
        $data['password'] = bcrypt($request->password);
        $admins = Admin::create($data);
        auth()->guard('admin')->login($admins);
        if ($admins) {
            toastr()->success('Data has been saved successfully!');
            return redirect(route('dashboard.index'));
        }
    }

    public function login()
    {
        return view('admin.auth.login');
    }

    public function signin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (auth()->guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('dashboard.index');
        } else {
            toastr()->error('Email or password are wrong.');
            return redirect()->back();
        }
    }

    function adminLogout()
    {
        auth()->guard('admin')->logout();
        return redirect()->route('login');
    }

    public function profile()
    {
        return view('admin.auth.profile');
    }

    public function AccountUpdate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:admins,email,' . Auth::user()->id,
            'photo' => 'nullable|mimes:jpg,png'
        ]);
        $admin = Admin::FindOrFail(Auth::user()->id);
        if ($request->photo) {
            Storage::delete('public/images/admins/' . $admin->photo);
            $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/admins');
            $data['photo'] = $image_name;
        }
        $admin->update($data);
        toastr()->success('Data has been updated successfully!');
        return redirect()->route('dashboard.profile');
    }

    public function changePass(Request $request)
    {
        $request->validate([
            'old_password' => 'required|min:6',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        $current_admin = auth()->user();
        if (Hash::check($request->old_password, $current_admin->password)) {
            $current_admin->update([
                'password' => bcrypt($request->new_password)
            ]);
            toastr()->success('Password has been Updated successfully!');
            return redirect()->route('dashboard.profile');
        } else {
            toastr()->error('old password does not matched');
            return redirect()->back();
        }
    }
}
