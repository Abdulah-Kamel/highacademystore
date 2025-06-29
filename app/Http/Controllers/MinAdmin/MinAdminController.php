<?php

namespace App\Http\Controllers\MinAdmin;

use toastr;
use App\Models\MiniAdmin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class MinAdminController extends Controller
{
    use ImageTrait;

    public function index()
    {
        return view('min_admin.barcode.index');
    }


    public function login()
    {
        return view('min_admin.auth.login');
    }

    public function signin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (auth()->guard('mini_admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('dashboard.miniadmin');
        } else {
            toastr()->error('Email or password are wrong.');
            return redirect()->back();
        }
    }

    function adminLogout()
    {
        auth()->guard('mini_admin')->logout();
        return redirect()->route('login');
    }

}
