<?php

namespace App\Http\Controllers\Admin;

use App\Http\Actions\GradeAction;
use App\Http\Actions\SessionAction;
use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Session;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SessionController extends Controller
{
    public string $folderPath = "Admin.sessions.";
    public array $data = ['name', 'start_from', 'start_to', 'grade_id'];
    public string $route = "sessions";
    public array $days = [
        ['arabic' => 'السبت', 'english' => 'Saturday'],
        ['arabic' => 'الاحد', 'english' => 'Sunday'],
        ['arabic' => 'الاثنين', 'english' => 'Monday'],
        ['arabic' => 'الثلاثاء', 'english' => 'Tuesday'],
        ['arabic' => 'الاربعاء', 'english' => 'Wednesday'],
        ['arabic' => 'الخميس', 'english' => 'Thursday'],
        ['arabic' => 'الجمعة', 'english' => 'Friday'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $sessions = Session::query()->get();
            return DataTables::of($sessions)
                ->addIndexColumn()
                ->editColumn('grade_id', function ($row) {
                    return $row->grades->name ?? '';
                })
                ->editColumn('studentNumber', function ($row) {
                    return $row->students->count() ?? '';
                })
                ->addColumn('actions', function ($row) {
                    return
                        '<button class="btn btn-warning" id="btnEdit" data-id=" ' . $row->id . ' ">تعديل</button>
                         <button class="btn btn-danger" id="btnDelete" data-id=" ' . $row->id . ' ">حذف</button>';
                })
                ->rawColumns(['actions', 'grade_id','studentNumber'])
                ->toJson();
        } else {
            return view($this->folderPath . 'index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(GradeAction $action)
    {
        if (\request()->ajax()) {
            $returnHtml = view($this->folderPath . 'create')
                ->with([
                    'days' => $this->days,
                    'grades' => $action->get(),
                ])
                ->render();
            return response()->json(['html' => $returnHtml]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, SessionAction $action)
    {
        $data = $request->only($this->data);
        $days = $request->days;
        $action->storeSession($data, $days);
        return response()->json(['success' => 'تم اضافة الحصة بنجاح']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, SessionAction $action)
    {
        $sessions = $action->getSessions($id);
        $returnHtml = view('Admin.students.sessions')
            ->with(['sessions' => $sessions])
            ->render();
        return response()->json(['html' => $returnHtml]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, SessionAction $action, GradeAction $gradeAction)
    {
        if (\request()->ajax()) {
            $returnHtml = view($this->folderPath . 'edit')
                ->with([
                    'obj' => $action->find($id),
                    'days' => $this->days,
                    'grades' => $gradeAction->get(),
                    'sessionDays' => $action->getDays($id)
                ])
                ->render();
            return response()->json(['html' => $returnHtml]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, SessionAction $action)
    {
        $data = $request->only($this->data);
        $action->updateSession($id, $data, $request->days);
        return response()->json(['success' => 'تم تعديل الحصة بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, SessionAction $action)
    {
        $action->delete($id);
        return response()->json(['success' => 'تم حذف الحصة بنجاح']);
    }
}
