<?php

namespace App\Http\Controllers\Admin;

use App\Http\Actions\DashboardAction;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Money;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(DashboardAction $action)
    {
        $data = $action->getCounts();
        $absentStudents = Student::select('name', 'grade_id', DB::raw('COUNT(attendances.id) as total_absences'))
            ->leftJoin('attendances', 'students.id', '=', 'attendances.student_id')
            ->where('attendances.status', '=', 0)->whereMonth('attendances.created_at', '=', now()->month)
            ->groupBy('students.id', 'students.name', 'students.grade_id')
            ->orderByDesc('total_absences')
            ->limit(5)
            ->get();
        $attendingStudents = Student::select('name', 'grade_id', DB::raw('COUNT(attendances.id) as total_absences'))
            ->leftJoin('attendances', 'students.id', '=', 'attendances.student_id')
            ->where('attendances.status', '=', 1)->whereMonth('attendances.created_at', '=', now()->month)
            ->groupBy('students.id', 'students.name', 'students.grade_id')
            ->orderByDesc('total_absences')
            ->limit(5)
            ->get();
        $notPaidStudentsIDs = Money::query()->where('is_paid', '=', 0)->whereMonth('created_at', '=', now()->month)->pluck('student_id')->ToArray();
        $notPaidStudents = Student::whereIn('id', $notPaidStudentsIDs)->get();
        return view('Admin.dashboard.index', compact('data', 'absentStudents', 'attendingStudents','notPaidStudents'));
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
