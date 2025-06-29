<?php

namespace App\Http\Controllers\Admin;

use App\Http\Actions\GradeAction;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class GradeController extends Controller
{
    public string $folderPath = "Admin.grades.";
    public array $data = ['name'];
    public string $route = "grades";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $grades = Grade::all();
            return DataTables::of($grades)
                ->addIndexColumn()
                ->addColumn('actions', function ($row) {
                    return
                        '<button class="btn btn-warning" id="btnEdit" data-id=" ' . $row->id . ' ">تعديل</button>
                         <button class="btn btn-danger" id="btnDelete" data-id=" ' . $row->id . ' ">حذف</button>';
                })
                ->rawColumns(['actions'])
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
        if (\request()->ajax()) {
            $returnHtml = view($this->folderPath . 'create')->render();
            return response()->json(['html' => $returnHtml]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, GradeAction $action)
    {
        $data = $request->only($this->data);
        $action->store($data);
        return response()->json(['success' => 'تم اضافة الصف الدراسي بنجاح']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, GradeAction $action)
    {
        if (\request()->ajax()) {
            $returnHtml = view($this->folderPath . 'edit')
                ->with(['obj' => $action->find($id)])
                ->render();
            return response()->json(['html' => $returnHtml]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, GradeAction $action)
    {
        $data = $request->only($this->data);
        $action->find($id)->update($data);
        return response()->json(['success' => 'تم تعديل الصف بنجاح']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, GradeAction $action)
    {
        $action->delete($id);
        return response()->json(['success' => 'تم حذف الصف بنجاح']);
    }
}
