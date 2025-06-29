<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentSettingsController extends Controller
{
    use ImageTrait;

    public function change_password(Request $request)
    {
        $student = Student::findorfail(Auth::guard('student')->id());
        if (isset($request->password))
        {
            $student->password = Hash::make($request->password);
        }

        $student->image = $this->uploadImage($request->image, 'student_images');
        $student->save();
        return redirect()->route('student_dashboard.index');
    }

    public function get_settings_form()
    {
        return view('Admin.student_settings.settings');
    }
}
