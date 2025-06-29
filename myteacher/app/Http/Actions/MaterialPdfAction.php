<?php

namespace App\Http\Actions;

use App\Models\MaterialPdf;
use App\Traits\ImageTrait;

class MaterialPdfAction extends MainAction
{
    use ImageTrait;

    public function __construct()
    {
        $this->model = new MaterialPdf();
        $this->fileFolder = 'material_pdfs';
    }

    public function storeMaterialPdf($data)
    {
        if (isset($data['file'])) {
            $data['file'] = $this->uploadImage($data['file'], $this->fileFolder);
            return $this->store($data);
        }
    }

    public function updateMateriaPdf($id, $data)
    {
        if (isset($data['file'])) {
            unlink($this->find($id)->file);
            $data['file'] = $this->uploadImage($data['file'], $this->fileFolder);
        }
        return $this->find($id)->update($data);
    }

    public function deleteMaterialPdf($id)
    {
        unlink($this->find($id)->file);
        return $this->delete($id);
    }

}
