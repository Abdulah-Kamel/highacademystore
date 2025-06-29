<?php

namespace App\Http\Controllers\Admin;

use App\Http\Actions\ExamAction;
use App\Http\Actions\ExamStudentAction;
use App\Http\Actions\GradeAction;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamsStudents;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExamStudentController extends Controller
{
    public string $folderPath = "Admin.examStudents.";
    public array $data = ['exam_id', 'student_id', 'student_degree'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ExamStudentAction $action, ExamAction $examAction)
    {
        $examID = $request->exam_id;
        $studentIDs = $request->student_id;
        $studentDegree = $request->student_degree;
        $examsStudentIDS = $request->studentExam;
        $exam = $examAction->find($examID);
            $action->storeDegree($examID, $studentIDs, $studentDegree, $examsStudentIDS);
            return response()->json(['success' => 'تم تسجيل الدرجة بنجاح']);
//            return response()->json(['error' => 'عفوا , هناك خطأ حاول مرة اخري']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, ExamAction $examAction, GradeAction $gradeAction, ExamStudentAction $examStudentAction)
    {
        $exam = $examAction->find($id);
        $grade = $gradeAction->find($exam->grade_id);
        if (\request()->ajax()) {
            $students = Student::query()->where('grade_id', $exam->grade_id)->get();
            return DataTables::of($students)
                ->addIndexColumn()
                ->addColumn('student_degree', function ($row) use ($exam, $examStudentAction) {
                    $studentExam = ExamsStudents::where('student_id', $row->id)->where('exam_id', $exam->id)->first();
                    return
                        '
                        <div class="row mt-3">
                            <div class="col mb-4">
                             <input type="number" class="form-control" value="' . @$studentExam->student_degree . '" name="student_degree[' . $row->id . ']" min="0" placeholder="سجل درجة الطالب">
                            </div>
                             <input type="hidden"  name="student_id[' . $row->id . ']" value="' . $row->id . '">
                             <input type="hidden"  name="studentExam[' . $row->id . ']" value="' . @$studentExam->id . '">
                             <div class="col mb-2">
                            ' . $examStudentAction->checkDegree(@$studentExam->student_degree, $exam->degree) . '
                             </div>
                        </div>
                         ';
                })
                ->rawColumns(['student_degree'])
                ->toJson();
        } else {
            return view($this->folderPath . 'index', compact('exam', 'grade'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
