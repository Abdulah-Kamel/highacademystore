<?php

namespace App\Services;

use App\Models\Slider;
use App\Traits\ImageTrait;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class SliderService
{
    use ImageTrait;

    # Index
    public function findAll()
    {
        $slider = Slider::with('translations')->newQuery();
        return $slider;
    }

    # Insert
    public function save($request, $data)
    {
        if ($request->photo) {
            $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/sliders');
            $data['photo'] = $image_name;
        }
        $slider = Slider::create($data);
        return $slider;
    }

    # Edit
    public function update($request, $slider, $data)
    {
        $data = $request->only('title:ar', 'stage_id', 'title:en', 'description:ar', 'description:en', 'is_active');

        if ($request->photo) {
            if ($slider->photo != 'default.png') {
                Storage::delete('public/images/sliders/' . $slider->photo);
            }
            $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/sliders');
            $data['photo'] = $image_name;
        }

        $slider->update($data);
        return $slider;
    }
}
