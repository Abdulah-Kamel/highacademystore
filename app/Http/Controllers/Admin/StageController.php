<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditstageRequest;
use App\Http\Requests\stageRequest;
use App\Models\Stage;
use App\Services\StageService;
use App\Traits\DeleteTrait;
use App\Traits\GeneralTrait;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;


class StageController extends Controller
{
    use ImageTrait, DeleteTrait, GeneralTrait;

    protected $stageService;

    public function __construct(StageService $stageService)
    {
        $this->stageService = $stageService;
    }

    public function index()
    {
        // $stages = stage::with('translations')->actived()->get();
        return view('admin.stage.index');
    }

    public function datatable()
    {
        $stages = $this->stageService->findAll();
        return DataTables::of($stages)
            ->addColumn('title', function ($row) {
                return $row->title;
            })
            // ->addColumn('description', function ($row) {
            //     return $row->description;
            // })
            // ->addColumn('is_active', function ($row) {
            //     return $row->is_active == 1 ? 'Active' : 'InActive';
            // })
            ->addColumn('photo', function ($row) {
                $image = '<img src ="' . $row->image_path . '" alt="profile-image" style="height:120px;width:150px" class="avatar rounded me-2" >';
                return $image;
            })
            ->addColumn('operation', function ($row) {
                $delete = '<a stage_id=' . $row->id . ' class="btn btn-lg btn-block btn-danger lift text-uppercase delete_btn"> حذف</a>';
                $edit = '<a href="' . route('dashboard.stage.edit', $row->id) . '" type="button" class="btn btn-lg btn-block btn-success lift text-uppercase">تعديل</a>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['operation' => 'operation', 'photo' => 'photo'])
            ->toJson();
    }

    public function create()
    {
        return view('admin.stage.create');
    }

    public function store(stageRequest $request)
    {
        $data = $request->only('title:ar', 'title:en', 'description:ar', 'description:en', 'is_active');
        $stage = $this->stageService->save($request, $data);
        return $this->JsonData($stage);
    }

    public function edit($id)
    {
        $stage = stage::FindOrFail($id);
        return view('admin.stage.edit', compact('stage'));
    }

    public function update(EditstageRequest $request)
    {
        $stage = stage::FindOrFail($request->stage_id);
        $data = $request->only('title:ar', 'title:en', 'description:ar', 'description:en', 'is_active');
        $stage = $this->stageService->update($request, $stage, $data);
        return $this->JsonData($stage);
    }

    public function destroy(Request $request)
    {
        $stage = stage::FindOrFail($request->id);
        if ($stage->photo != 'default.png') {
            Storage::delete('public/images/stages/' . $stage->photo);
        }
        return $this->Delete($request->id, $stage);
    }

}
