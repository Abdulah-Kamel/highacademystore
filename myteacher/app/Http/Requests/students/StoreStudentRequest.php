<?php

namespace App\Http\Requests\students;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'phone' => 'required | unique:students,phone',
            'parent_phone' => 'required | unique:students,parent_phone',
            'address' => 'required',
            'grade_id' => 'required | exists:grades,id',
            'sessionsIDS.*' => 'required | exists:sessions,id'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'عفوا , يرجي ادخال اسم الطالب',
            'phone.required' => 'عفوا , يرجي ادخال رقم هاتف الطالب',
            'phone.unique' => 'عفوا , رقم الهاتف الطالب موجود مسبقا',
            'parent_phone.required' => 'عفوا , يرجي ادخال رقم هاتف ولي الامر',
            'parent_phone.unique' => 'عفوا , رقم هاتف ولي الامر موجود مسبقا',
            'address.required' => 'عفوا , يرجي ادخال عنوان الطالب',
            'grade_id.required' => 'عفوا , يرجي اختيار  الصف الدراسي',
            'sessionsIDS.required' => 'عفوا , يرجي اختيار  الحصة',
            'grade_id.exists' => 'عفوا ,هذا الصف الدراسي غير موجود',
        ];
    }
}
