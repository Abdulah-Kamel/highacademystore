<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OldStudentController extends Controller
{
    public string $folderPath = "Admin.oldStudents.";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $students = Student::onlyTrashed()->get();
            return DataTables::of($students)
                ->addIndexColumn()
                ->editColumn('grade_id', function ($row) {
                    return $row->grades->name ?? '';
                })
                ->addColumn('actions', function ($row) {
                    return
                        '
                        <button class="btn btn-success" id="btnRestore" data-id=" ' . $row->id . ' ">استعادة</button>
                         <button class="btn btn-danger" id="btnDelete" data-id=" ' . $row->id . ' ">حذف نهائي</button>
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        $student = Student::onlyTrashed()->find($id);
        if ($student) {
            $student->restore();
            return response()->json(['success' => 'تم استعادة الطالب بنجاح']);
        } else {
            return response()->json(['error' => 'حدث خطأ , حاول مرة اخري']);
        }

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
        $student = Student::onlyTrashed()->find($id);
        if ($student) {
            $student->forceDelete();
            return response()->json(['success' => 'تم حذف نهائيا الطالب بنجاح']);
        } else {
            return response()->json(['error' => 'حدث خطأ , حاول مرة اخري']);
        }
    }
}
