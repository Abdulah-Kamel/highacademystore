<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
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
            'title:ar' => 'required',
            // 'title:en' => 'required|unique:slider_translations,title',
            // 'description:ar' => 'required',
            // 'description:en' => 'required',
            // 'photo' => 'required',
            // 'is_active' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'title:ar.required' => 'The Arabic title field is required.',
            'title:en.required' => 'The English title field is required.',
            'title:ar.unique' => 'The Arabic title has already been taken.',
            'title:en.unique' => 'The English title has already been taken.',
            'description:ar.required' => 'The Arabic description field is required.',
            'description:en.required' => 'The English description field is required.',
        ];
    }
}
