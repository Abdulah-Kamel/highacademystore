<?php

namespace App\Http\Controllers\Admin;

use App\Http\Actions\GradeAction;
use App\Http\Actions\SessionAction;
use App\Http\Actions\StudentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\students\StoreStudentRequest;
use App\Models\Student;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    public string $folderPath = "Admin.students.";
    public array $data = ['name', 'phone', 'password', 'parent_phone', 'address', 'grade_id','image'];
    public string $route = "students";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $students = Student::query()->get();
            return DataTables::of($students)
                ->addIndexColumn()
                ->editColumn('grade_id', function ($row) {
                    return $row->grades->name ?? '';
                })
                ->addColumn('actions', function ($row) {
                    return
                        '
                        <button class="btn btn-warning" id="btnEdit" data-id=" ' . $row->id . ' ">تعديل</button>
                         <button class="btn btn-danger" id="btnDelete" data-id=" ' . $row->id . ' ">حذف</button>
                         <button class="btn btn-dark" id="btnShow" data-id=" ' . $row->id . ' ">تفاصيل</button>
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
    public function create(GradeAction $gradeAction, SessionAction $sessionAction)
    {
        if (\request()->ajax()) {
            $returnHtml = view($this->folderPath . 'create')
                ->with([
                    'grades' => $gradeAction->get(),
                    'sessions' => $sessionAction->get(),
                ])
                ->render();
            return response()->json(['html' => $returnHtml]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request, StudentAction $action)
    {
        $data = $request->only($this->data);
        $action->storeStudent($data, $request->sessionsIDS);
        return response()->json(['success' => 'تم تسجيل الطالب بنجاح']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, StudentAction $action)
    {
        if (\request()->ajax()) {
            $returnHtml = view($this->folderPath . 'show')
                ->with([
                    'obj' => $action->find($id),
                ])->render();
            return response()->json(['html' => $returnHtml]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, GradeAction $gradeAction, SessionAction $sessionAction, StudentAction $studentAction)
    {
        if (\request()->ajax()) {
            $returnHtml = view($this->folderPath . 'edit')
                ->with([
                    'grades' => $gradeAction->get(),
                    'sessions' => $sessionAction->get(),
                    'obj' => $studentAction->find($id),
                    'studentSessionsIDS' => $studentAction->getSessions($id)
                ])
                ->render();
            return response()->json(['html' => $returnHtml]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, StudentAction $studentAction)
    {
        $data = $request->only($this->data);
        $studentAction->updateStudent($id, $data, $request->sessionsIDS);
        return response()->json(['success' => 'تم تعديل الطالب بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, StudentAction $studentAction)
    {
        $studentAction->delete($id);
        return response()->json(['success' => 'تم حذف الطالب بنجاح']);
    }
}
