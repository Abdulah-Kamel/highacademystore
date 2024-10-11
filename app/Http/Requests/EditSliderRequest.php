<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class EditSliderRequest extends FormRequest
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
            'title:ar' => ['required'],
            // 'title:en' => ['required', Rule::unique('slider_translations', 'title')->ignore($this->slider_id, 'slider_id')],
            // 'description:ar' => 'required',
            // 'description:en' => 'required',
            // 'photo' => 'nullable',
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
            'photo.mimes' => 'The photo must be a file of type: png, jpg.',
        ];
    }
}
