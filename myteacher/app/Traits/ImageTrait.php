<?php

namespace App\Traits;

trait ImageTrait
{

    public function uploadImage($image, $folder)
    {
        if (isset($image)) {
            $imageName = uniqid() . $image->getClientOriginalName();
            $path = $image->move($folder, $imageName);
            return $path;
        }
    }

    public function getImage($image)
    {
        return '<img src="' . asset('') . $image . '" style="border-radius: 50%;" width="100px" height="100px">';

    }

}
