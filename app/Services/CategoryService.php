<?php

namespace App\Services;

use App\Models\Category;
use App\Traits\ImageTrait;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class CategoryService
{
    use ImageTrait;

    # Index
    public function findAll()
    {
        $categories = Category::with('translations', 'parent')->newQuery();
        return $categories;
    }

    # Insert
    public function save($request,$data)
    {
        if ($request->photo) {
            $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/categories');
            $data['photo'] = $image_name;
        }
        $data['is_parent'] = $request->input('is_parent', 0);
        $category = Category::create($data);
        return $category;
    }

    # Edit
    public function update($request, $category,$data)
    {
        $data['is_parent'] = $request->input('is_parent', 0);
        if ($request->is_parent == 1) {
            $data['parent_id'] = null;
        }
        if ($request->photo) {
            if ($category->photo != 'default.png') {
                Storage::delete('public/images/categories/' . $category->photo);
            }
            $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/categories');
            $data['photo'] = $image_name;
        }

        $category->update($data);
        return $category;
    }
}
