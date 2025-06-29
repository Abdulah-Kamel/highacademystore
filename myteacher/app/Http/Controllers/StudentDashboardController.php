<?php

namespace App\Http\Controllers;

use App\Models\VideoView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mostWatchedVideos = VideoView::select('material_video_id', DB::raw('COUNT(*) as views_count'))
            ->with('material_video')
            ->groupBy('material_video_id')
            ->orderByDesc('views_count')
            ->where('student_id',Auth::guard('student')->user()->id)
            ->get();

        return view('Admin.student_dashboard.index')->with(['mostWatchedVideos'=>$mostWatchedVideos]);
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
