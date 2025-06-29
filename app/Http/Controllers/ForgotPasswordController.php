<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{

    public function newpassform($token, Request $request)
    {
        $email = $request->query('email');
        return view('user.password.ResetForm', compact('email', "token"));
    }

     public function sendResetLinkEmail(Request $request)
    {
  
        $request->validate(["email" => "required|email"]);

        try {
           
            $status = Password::broker('users')->sendResetLink($request->only("email"));

      
            if ($status === Password::RESET_LINK_SENT) {
                return back()->with("status", 'تم إرسال رابط إعادة تعيين كلمة المرور بنجاح.');
            } elseif ($status === Password::RESET_THROTTLED) {
                return back()->withErrors(["email" => 'العديد من المحاولات. يرجى المحاولة مرة أخرى لاحقًا.']);
            } else {
                return back()->withErrors(["email" => 'لم يتم العثور على بريد إلكتروني مطابق.']);
            }
        } catch (\Exception $e) {
          
            return back()->withErrors(["email" => 'حدث خطأ أثناء محاولة إرسال رابط إعادة تعيين كلمة المرور. يرجى المحاولة مرة أخرى لاحقًا.']);
        }
    }


    public function reset(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required|min:8|confirmed",
            "token" => "required",
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $status = Password::broker('users')->reset(
            $request->only("email", "password", "password_confirmation", "token"),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()
                ->route("user.login")
                ->with("status", __($status))
            : back()->withErrors(["email" => __($status)]);
    }
}
