<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class EditProductRequest extends FormRequest
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
            'name:ar' => ['required'],
            // 'name:en' => ['required', Rule::unique('product_translations', 'name')->ignore($this->product_id, 'product_id')],
            'description:ar'=>'string|required',
            // 'description:en'=>'string|required',
            'price'=>'required|numeric|min:0|max:10000',
            // 'offer_value'=>'numeric|nullable|min:0|max:10000',
            // 'offer_type'=>'string|nullable',
            // 'have_offer'=>'required',
            'photo' => 'nullable',
            'category_id'=>'nullable|exists:categories,id',
            'slider_id'=>'nullable|exists:sliders,id',
            // 'child_cat_id'=>'nullable|exists:categories,id',
            'brand_id'=>'nullable|exists:brands,id',
        ];
    }

    public function messages()
    {
        return [
            'name:ar.required' => 'The Arabic name field is required.',
            'name:en.required' => 'The English name field is required.',
            'name:ar.unique' => 'The Arabic name has already been taken.',
            'name:en.unique' => 'The English name has already been taken.',
            'description:ar.required' => 'The Arabic description field is required.',
            'description:en.required' => 'The English description field is required.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price field must be a number.',
            'price.min' => 'The price field must be greater than or equal to 0.',
            'price.max' => 'The price field must be less than or equal to 10000.',
            'offer_value.min' => 'The offer value field must be greater than or equal to 0.',
            'offer_value.max' => 'The offer value field must be less than or equal to 10000.',
            'offer_value.numeric' => 'The offer value field must be a number.',
            'photo.mimes' => 'The photo must be a file of type: png, jpg.',
            'category_id.exists' => 'The selected category is invalid.',
            'child_cat_id.exists' => 'The selected subcategory is invalid.',
            'brand_id.exists' => 'The selected brand is invalid.',
        ];
    }
}
