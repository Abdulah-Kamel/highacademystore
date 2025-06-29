<?php

namespace App\Services;

use App\Models\MiniAdmin;
use App\Traits\ImageTrait;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class MiniAdminService
{
    use ImageTrait;

    # Index
    public function findAll()
    {
        $miniAdmin = MiniAdmin::get();
        return $miniAdmin;
    }

    # Insert
    public function save($request, $data)
    {
        $miniAdmin = MiniAdmin::create($data);
        return $miniAdmin;
    }

    # Edit
    public function update($request, $miniAdmin, $data)
    {
        $data = $request->only("name", 'email', 'password');
        if ($data['password'] == null) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($request->password);
        }
        $miniAdmin->update($data);
        return $miniAdmin;
    }
}
