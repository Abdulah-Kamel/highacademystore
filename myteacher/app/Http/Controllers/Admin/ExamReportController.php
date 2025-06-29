<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class ExamReportController extends Controller
{
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (\request()->ajax()) {
            $students = Student::query()->select('students.*', 'exams_students.student_degree')
                ->join('exams_students', 'students.id', '=', 'exams_students.student_id')
                ->orderByDesc('exams_students.student_degree')
                ->where('exam_id', $id)
                ->limit(10)
                ->get();
            $returnHtml = view('Admin.exams.reports', compact('students'))->render();
            return response()->json(['html' => $returnHtml]);
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
