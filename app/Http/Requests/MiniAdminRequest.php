<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MiniAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:mini_admins',
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The admin name field is required.',
            'email.required' => 'The admin email field is required.',
            'email.unique' => 'The admin email has already been taken.',
            'password' => 'The admin password field is required.',
        ];
    }
}
