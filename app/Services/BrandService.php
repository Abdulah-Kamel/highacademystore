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
        if($request->photo){
            $image_name = $this->ImageNamePath($request->file('photo'),'public/images/brands');
            $data['photo']= $image_name;
        }
        $brand = Brand::create($data);
        return $brand;
    }

    # Edit
    public function update($request, $brand,$data)
    {
        if ($request->photo) {
            if ($brand->photo != 'default.png') {
                Storage::delete('public/images/brands/' . $brand->photo);
            }
            $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/brands');
            $data['photo'] = $image_name;
        }

        $brand->update($data);
        return $brand;
    }
}
