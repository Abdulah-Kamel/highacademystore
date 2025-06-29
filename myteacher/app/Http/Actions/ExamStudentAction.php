<?php

namespace App\Http\Actions;

use App\Models\ExamsStudents;

class ExamStudentAction extends MainAction
{
    public function __construct()
    {
        $this->model = new ExamsStudents();
    }

    public function checkDegree($student_degree, $exam_degree): string
    {
        if(!is_null($student_degree))
        {
            if ($student_degree == $exam_degree) {
                return '<p style="font-weight: bold; color: green">ممتاز</p>';
            } elseif ($student_degree < $exam_degree && $student_degree >= ($exam_degree / 2)) {
                return '<p style="font-weight: bold; color: orange">متوسط</p>';
            } elseif ($student_degree <= ($exam_degree / 2)) {
                return '<p style="font-weight: bold; color: red">ضعيف</p>';
            }
        }
        else {
            return '<p style="font-weight: bold; color: red">لم يتم تسجيل الدرجة حتي الان</p>';
        }
    }

    public function storeDegree($examID, $studentIDs, $studentDegree, $examsStudentIDS)
    {
        $examAction = new ExamAction();
        $exam = $examAction->find($examID);
        if (in_array(null, $examsStudentIDS)) {
            ExamsStudents::whereIn('id', $examsStudentIDS)->delete();
            foreach ($studentIDs as $key => $studentID) {
                if ($studentDegree[$key] <= $exam->degree) {
                    ExamsStudents::create([
                        'student_id' => $studentID,
                        'student_degree' => $studentDegree[$key],
                        'exam_id' => $examID
                    ]);
                }

            }
        } else {
            foreach ($examsStudentIDS as $key => $examsStudentID) {
                if ($studentDegree[$key] <= $exam->degree) {
                    $this->find($examsStudentID)->update([
                        'student_id' => $studentIDs[$key],
                        'student_degree' => $studentDegree[$key],
                        'exam_id' => $examID
                    ]);
                }

            }
        }


    }
}
