<?php

namespace App\Services;

use App\Models\Stage;
use App\Traits\ImageTrait;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class StageService
{
    use ImageTrait;

    # Index
    public function findAll()
    {
        $Stage = Stage::with('translations')->newQuery();
        return $Stage;
    }

    # Insert
    public function save($request, $data)
    {
        if ($request->photo) {
            $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/Stages');
            $data['photo'] = $image_name;
        }
        $Stage = Stage::create($data);
        return $Stage;
    }

    # Edit
    public function update($request, $Stage, $data)
    {
        $data = $request->only('title:ar', 'title:en', 'description:ar', 'description:en', 'is_active');

        if ($request->photo) {
            if ($Stage->photo != 'default.png') {
                Storage::delete('public/images/Stages/' . $Stage->photo);
            }
            $image_name = $this->ImageNamePath($request->file('photo'), 'public/images/Stages');
            $data['photo'] = $image_name;
        }

        $Stage->update($data);
        return $Stage;
    }
}
