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
    public function save($request, $data)
    {
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $destinationPath = public_path('storage/images/products');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $filename);
            $data['photo'] = $filename;
        }
        $product = Product::create($data);
        return $product;
    }

    # Edit
    public function update($request, $product, $data)
    {
        if ($request->hasFile('photo')) {
            if ($product->photo != 'default.png') {
                Storage::delete('public/images/products/' . $product->photo);
            }
            $image = $request->file('photo');
            $destinationPath = public_path('storage/images/products');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $filename);
            $data['photo'] = $filename;
        }
        $product->update($data);
        return $product;
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_deleted' => true]);
        return true;
    }
}
