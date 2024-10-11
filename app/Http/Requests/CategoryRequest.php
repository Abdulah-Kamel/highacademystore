<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'title:ar' => 'required|',
            'title:en' => 'nullable',
            'photo' => 'nullable',
            // 'is_parent' => 'sometimes|in:1',
            // 'parent_id' => 'nullable|exists:categories,id',
        ];
    }

    public function messages()
    {
        return [
            'title:ar.required' => 'The Arabic title field is required.',
            'title:en.required' => 'The English title field is required.',
            'title:ar.unique' => 'The Arabic title has already been taken.',
            'title:en.unique' => 'The English title has already been taken.',
            'photo.mimes' => 'The photo must be a file of type: png, jpg.',
        ];
    }
}
