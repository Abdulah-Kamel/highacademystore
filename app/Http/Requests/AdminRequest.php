<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
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
            'photo' => 'nullable',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6|confirmed'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'name is required',
            'email.required' => 'email is required',
            'email.unique' => 'email is unique',
            'password.required' => 'title en is required',
            'password.confirmed' => 'The password does not matched',
        ];
    }
}
