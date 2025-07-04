<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditMinAdminRequest extends FormRequest
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
            'email' => 'nullable|email',
        ];
    }

    public function messages()
    {
        return [
            'email.email' => 'البريد الالكتروني يجب ان يكون بصيغة البريد الالكتروني',
        ];
    }
}
