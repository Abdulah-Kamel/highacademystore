<?php

namespace App\Services;

use App\Models\Product;
use App\Traits\ImageTrait;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class ProductService
{
    use ImageTrait;

    # Index
    public function findAll()
    {
        $products = Product::with('translations', 'sub_cat_info', 'category', 'brands')->orderby("id", "DESC")->newQuery();
        return $products;
    }

    # Insert
    public function save($request,$data)
    {
        if ($request->photo) {
            $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/products');
            $data['photo'] = $image_name;
        }
        $product = Product::create($data);
        return $product;
    }

    # Edit
    public function update($request, $product,$data)
    {
        if ($request->photo) {
            if ($product->photo != 'default.png') {
                Storage::delete('public/images/products/' . $product->photo);
            }
            $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/products');
            $data['photo'] = $image_name;
        }
        $product->update($data);
        return $product;
    }
}
