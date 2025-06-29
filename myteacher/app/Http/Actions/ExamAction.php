<?php

namespace App\Http\Actions;

use App\Models\Exam;

class ExamAction extends MainAction
{
    public function __construct()
    {
        $this->model = new Exam();
    }
}
