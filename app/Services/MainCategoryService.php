<?php

namespace App\Services;

use App\Models\MainCategory;
use Illuminate\Support\Facades\Storage;

class MainCategoryService
{
    public function save(array $data) // Fix: Only accept $data, not $request
    {
        return MainCategory::create($data);
    }

    public function findAll()
    {
        return MainCategory::all();
    }

    public function update($request, $category, $data)
    {


        if ($request->hasFile('icon_image')) {
            Storage::delete('public/images/categories/' . $category->icon_image);
        }
        $image = $request->file('icon_image');
        $destinationPath = public_path('storage/images/categories');
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $icon_image = $image->move($destinationPath, $filename);

        // ✅ Update category
        $category->update([
            'name' => $data['name'],
            'icon_image' => $icon_image
        ]);

        return $category;
    }


    public function delete(MainCategory $category)
    {
        return $category->delete();
    }
}
