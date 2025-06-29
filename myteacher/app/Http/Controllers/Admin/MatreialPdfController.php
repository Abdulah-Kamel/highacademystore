<?php

namespace App\Http\Controllers\Admin;

use App\Events\NewMaterialPdfEvent;
use App\Http\Actions\ChapterAction;
use App\Http\Actions\MaterialPdfAction;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\MaterialPdf;
use App\Models\Student;
use App\Notifications\NewMaterialPDFNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class MatreialPdfController extends Controller
{
    public string $folderPath = "Admin.material_pdf.";
    public array $data = ['title', 'file', 'chapter_id'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $materials_pdf = MaterialPdf::query()->get();
            return DataTables::of($materials_pdf)
                ->addIndexColumn()
                ->editColumn('title', function ($row) {
                    return $row->title;
                })
                ->editColumn('file', function ($row) {
                    return '<a href="' . $row->file . '" download=""> تحميل الملف</a>';
                })
                ->editColumn('chapter_id', function ($row) {
                    return $row->chapters->name;
                })
                ->addColumn('actions', function ($row) {
                    return
                        '<button class="btn btn-warning" id="btnEdit" data-id=" ' . $row->id . ' ">تعديل</button>
                         <button class="btn btn-danger" id="btnDelete" data-id=" ' . $row->id . ' ">حذف</button>
                         ';
                })
                ->rawColumns(['actions', 'title', 'file', 'chapter_id'])
                ->toJson();
        } else {
            return view($this->folderPath . 'index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ChapterAction $action)
    {
        if (\request()->ajax()) {
            $html = view($this->folderPath . 'create')
                ->with(['chapters' => $action->get()])
                ->render();
            return response()->json(['html' => $html]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, MaterialPdfAction $action)
    {
        $data = $request->only($this->data);
        $material_pdf = $action->storeMaterialPdf($data);
        $chapter = Chapter::findorfail($material_pdf->chapter_id);
        Notification::send(Student::all(), new NewMaterialPDFNotifications($chapter->name));
        Broadcast(new NewMaterialPdfEvent('تم اضافه ملف دراسي جديد'));
        return response()->json(['success' => 'تم اضافة الملف الدراسي بنجاح']);
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
    public function edit(string $id, MaterialPdfAction $action, ChapterAction $chapterAction)
    {
        if (\request()->ajax()) {
            $html = view($this->folderPath . 'edit')
                ->with(['chapters' => $chapterAction->get(), 'obj' => $action->find($id)])
                ->render();
            return response()->json(['html' => $html]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, MaterialPdfAction $action)
    {
        $data = $request->only($this->data);
        $action->updateMateriaPdf($id, $data);
        return response()->json(['success' => 'تم تعديل الملف الدراسي بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, MaterialPdfAction $action)
    {
        $action->deleteMaterialPdf($id);
        return response()->json(['success' => 'تم حذف الملف الدراسي بنجاح']);
    }
}
