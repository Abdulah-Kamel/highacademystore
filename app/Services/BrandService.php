<?php

namespace App\Services;

use App\Models\Brand;
use App\Traits\ImageTrait;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class BrandService
{
    use ImageTrait;

    # Index
    public function findAll()
    {
        $brands = Brand::with('translations')->newQuery();
        return $brands;
    }

    # Insert
    public function save($request,$data)
    {
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $destinationPath = public_path('storage/images/brands');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $filename);
            $data['photo'] = $filename;
        }
        $brand = Brand::create($data);
        return $brand;
    }

    # Edit
    public function update($request, $brand,$data)
    {
        if ($request->hasFile('photo')) {
            if ($brand->photo != 'default.png') {
                Storage::delete('public/images/brands/' . $brand->photo);
            }
            $image = $request->file('photo');
            $destinationPath = public_path('storage/images/brands');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $filename);
            $data['photo'] = $filename;
        }

        $brand->update($data);
        return $brand;
    }
}
