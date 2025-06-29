<?php

namespace App\Http\Controllers\Admin;

use App\Http\Actions\MoneyAction;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentSessions;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MoneyController extends Controller
{
    public string $folderPath = "Admin.money.";
    public array $data = ['student_id', 'is_paid', 'month'];
    public string $route = "money";

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
    public function store(Request $request, MoneyAction $action)
    {
        $data = $request->only($this->data);
        if ($request->recordId == null) {
            $action->store($data);
            return response()->json(['success' => 'تم دفع فلوس الشهر لهذا الطالب']);
        } else {
            $action->updateMoney($data, $request);
            return response()->json(['success' => 'تم تعديل دفع فلوس الشهر لهذا الطالب']);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (\request()->ajax()) {
            $studentsIDs = StudentSessions::query()->where('session_id', $id)->pluck('student_id')->ToArray();
            $students = Student::query()->whereIn('id', $studentsIDs)->get();
            return DataTables::of($students)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if (!is_null(@$row->moneyStatus->is_paid)) {
                        if ($row->moneyStatus->is_paid == 1) {
                            return '<p style="font-weight: bold;color: green">تم الدفع</p>';
                        } elseif ($row->moneyStatus->is_paid == 0) {
                            return '<p style="font-weight: bold;color: red">لم يدفع</p>';
                        }
                    } else {
                        return '<p style="font-weight: bold;color: red">لم يتم تسجيله حتى الآن</p>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    if (@$row->moneyStatus->is_paid == 1) {
                        return
                            '<button class="btn btn-danger" id="btnNotPay" data-id=" ' . @$row->moneyStatus->id . ' ">الغاء دفع الفلوس</button>';
                    } else {
                        return
                            '<button class="btn btn-success" id="btnPay" data-extra="' . @$row->moneyStatus->id . '" data-id=" ' . $row->id . ' ">دفع الفلوس</button>';
                    }

                })
                ->rawColumns(['status', 'actions'])
                ->toJson();
        } else {
            return view($this->folderPath . 'index', compact('id'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, MoneyAction $action)
    {
        $data = $request->only($this->data);
        $action->updateMoney($data, $request);
        return response()->json(['success' => 'تم تعديل دفع فلوس الشهر لهذا الطالب']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
