<?php

namespace App\Http\Actions;

use App\Models\Chapter;

class ChapterAction extends MainAction
{
    public function __construct()
    {
        $this->model = new Chapter();
    }

}
