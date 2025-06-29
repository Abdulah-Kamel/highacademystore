<?php

namespace App\Http\Actions;

use App\Models\MaterialVideo;
use App\Traits\ImageTrait;

class MaterialVideoAction extends MainAction
{
    use ImageTrait;

    public function __construct()
    {
        $this->model = new MaterialVideo();
        $this->fileFolder = 'material_videos';
    }

    public function storeMaterialVideo($data)
    {
        if (isset($data['video']) && isset($data['image'])) {
            $data['video'] = $this->uploadImage($data['video'], $this->fileFolder);
            $data['image'] = $this->uploadImage($data['image'], $this->fileFolder);
            return $this->store($data);
        }
    }

    public function updateMateriaVideo($id, $data)
    {
        if (isset($data['image'])) {
            unlink($this->find($id)->image);
            $data['image'] = $this->uploadImage($data['image'], $this->fileFolder);
        }
        if (isset($data['video'])) {
            unlink($this->find($id)->video);
            $data['video'] = $this->uploadImage($data['video'], $this->fileFolder);
        }

        return $this->find($id)->update($data);
    }

    public function deleteMaterialVideo($id)
    {
        unlink($this->find($id)->video);
        unlink($this->find($id)->image);
        return $this->delete($id);
    }

}
