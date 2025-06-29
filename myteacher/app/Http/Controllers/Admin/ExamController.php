<?php

namespace App\Http\Controllers\Admin;

use App\Http\Actions\ExamAction;
use App\Http\Actions\GradeAction;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Student;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExamController extends Controller
{
    public string $folderPath = "Admin.exams.";
    public array $data = ['title', 'degree', 'grade_id', 'date'];
    public string $route = "exams";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $exams = Exam::query()->get();
            return DataTables::of($exams)
                ->addIndexColumn()
                ->editColumn('grade_id', function ($row) {
                    return $row->grades->name ?? '';
                })
                ->addColumn('actions', function ($row) {
                    return
                        ' <a href="' . route('exams-students.show', $row->id) . '">
                         <button class="btn btn-dark"  data-id=" ' . $row->id . '">تسجيل الدرجات</button></a>
                         <button class="btn btn-primary" id="btnReport" data-id=" ' . $row->id . ' ">التقرير</button>
                        <button class="btn btn-warning" id="btnEdit" data-id=" ' . $row->id . ' ">تعديل</button>
                         <button class="btn btn-danger" id="btnDelete" data-id=" ' . $row->id . ' ">حذف</button>

                         ';
                })
                ->rawColumns(['actions', 'grade_id'])
                ->toJson();
        } else {
            return view($this->folderPath . 'index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(GradeAction $gradeAction,)
    {
        if (\request()->ajax()) {
            $returnHtml = view($this->folderPath . 'create')
                ->with([
                    'grades' => $gradeAction->get(),
                ])
                ->render();
            return response()->json(['html' => $returnHtml]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ExamAction $action)
    {
        $data = $request->only($this->data);
        $action->store($data);
        return response()->json(['success' => 'تم تسجيل الامتحان بنجاح']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, GradeAction $gradeAction, ExamAction $examAction)
    {
        if (\request()->ajax()) {
            $returnHtml = view($this->folderPath . 'edit')
                ->with([
                    'grades' => $gradeAction->get(),
                    'obj' => $examAction->find($id),
                ])
                ->render();
            return response()->json(['html' => $returnHtml]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, ExamAction $examAction)
    {
        $data = $request->only($this->data);
        $examAction->find($id)->update($data);
        return response()->json(['success' => 'تم تعديل الامتحان بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, ExamAction $examAction)
    {
        $examAction->delete(($id));
        return response()->json(['success' => 'تم حذف الامتحان بنجاح']);
    }
}
