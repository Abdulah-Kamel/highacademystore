<?php

namespace App\Http\Actions;

use App\Models\Grade;

class GradeAction extends MainAction
{
    public function __construct()
    {
        $this->model = new Grade();
    }

}
