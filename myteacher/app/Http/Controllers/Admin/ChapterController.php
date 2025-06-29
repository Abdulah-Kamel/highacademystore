<?php

namespace App\Http\Controllers\Admin;

use App\Http\Actions\ChapterAction;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Exam;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ChapterController extends Controller
{
    public string $folderPath = "Admin.chapters.";
    public array $data = ['name'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $chapters = Chapter::query()->get();
            return DataTables::of($chapters)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('actions', function ($row) {
                    return
                        '<button class="btn btn-warning" id="btnEdit" data-id=" ' . $row->id . ' ">تعديل</button>
                         <button class="btn btn-danger" id="btnDelete" data-id=" ' . $row->id . ' ">حذف</button>
                         ';
                })
                ->rawColumns(['actions', 'name'])
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
            $html = view($this->folderPath . 'create')->render();
            return response()->json(['html' => $html]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ChapterAction $action)
    {
        $data = $request->only($this->data);
        $action->store($data);
        return response()->json(['success' => 'تم اضافة الفصل الدراسي بنجاح']);
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
    public function edit(string $id, ChapterAction $action)
    {
        if (\request()->ajax()) {
            $html = view($this->folderPath . 'edit')->with(['obj' => $action->find($id)])->render();
            return response()->json(['html' => $html]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, ChapterAction $action)
    {
        $data = $request->only($this->data);
        $action->find($id)->update($data);
        return response()->json(['success' => 'تم تعديل الفصل الدراسي بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, ChapterAction $action)
    {
        $action->delete($id);
        return response()->json(['success' => 'تم حذف الفصل الدراسي بنجاح']);
    }
}
