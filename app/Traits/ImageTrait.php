<?php

namespace App\Traits;

trait ImageTrait
{
    public function ImageNamePath($name, $path)
    {
        $name_path = $path;
        $image = $name;
        $image_name = $image->getClientOriginalName();
        $path = $name->storeAs($name_path, $image_name);
        return $image_name;
    }
}
