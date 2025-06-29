<?php

namespace App\Http\Actions;

use App\Models\Admin;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Hash;

class AdminAction extends MainAction
{
    use ImageTrait;

    public function __construct()
    {
        $this->model = new Admin();
    }

    public function storeAdmin($data)
    {
        if (isset($data['image'])) {
            $data['image'] = $this->uploadImage($data['image'], 'images');
        }
        $data['password'] = Hash::make($data['password']);
        return $this->store($data);
    }

    public function updateAdmin($id, $data)
    {
        if (isset($data['image'])) {
            $data['image'] = $this->uploadImage($data['image'], 'images');
        }
        return $this->find($id)->update($data);

    }

}
