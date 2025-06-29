<?php

namespace App\Http\Actions;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Session;
use App\Models\Student;

class DashboardAction extends MainAction
{
    public function getCounts()
    {
        return [
            'grades' => Grade::count(),
            'sessions' => Session::count(),
            'students' => Student::count(),
            'exams' => Exam::count()
        ];
    }
}
