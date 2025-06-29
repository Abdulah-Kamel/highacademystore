<?php

namespace App\Http\Controllers\Student;

use App\Http\Actions\ChapterAction;
use App\Http\Actions\MaterialVideoAction;
use App\Http\Controllers\Controller;
use App\Models\MaterialVideo;
use Illuminate\Http\Request;

class StudentMatreialVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MaterialVideoAction $action, ChapterAction $chapterAction)
    {
        return view('Admin.student_material_video.index')
            ->with(['videos' => $action->get(), 'chapters' => $chapterAction->get()]);
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
            $videos = MaterialVideo::where('chapter_id', $id)->get();
            $html = view('Admin.Render.filter_material_videos')->with(['videos' => $videos])->render();
            return response()->json(['html' => $html]);
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
