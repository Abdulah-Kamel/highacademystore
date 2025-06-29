<?php

namespace App\Http\Controllers\Admin;

use App\Models\Stage;
use App\Models\Slider;
use App\Traits\ImageTrait;
use App\Traits\DeleteTrait;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Services\SliderService;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Requests\SliderRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EditSliderRequest;
use Yajra\DataTables\Facades\DataTables;

class SliderController extends Controller
{
    use ImageTrait, DeleteTrait, GeneralTrait;

    protected $sliderService;

    public function __construct(SliderService $sliderService)
    {
        $this->sliderService = $sliderService;
    }

    public function index()
    {
        // $sliders = Slider::with('translations')->actived()->get();
        return view('admin.slider.index');
    }

    public function datatable()
    {
        $sliders = $this->sliderService->findAll();
        return DataTables::of($sliders)
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
                $delete = '<a slider_id=' . $row->id . ' class="btn btn-lg btn-block btn-danger lift text-uppercase delete_btn"> حذف</a>';
                $edit = '<a href="' . route('dashboard.slider.edit', $row->id) . '" type="button" class="btn btn-lg btn-block btn-success lift text-uppercase">تعديل</a>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['operation' => 'operation', 'photo' => 'photo'])
            ->toJson();
    }

    public function create()
    {
        $stages = Stage::all();
        return view('admin.slider.create', compact('stages'));
    }

    public function store(SliderRequest $request)
    {
        $data = $request->only('title:ar', 'title:en', 'stage_id', 'description:ar', 'description:en', 'is_active');
        $slider = $this->sliderService->save($request, $data);
        return $this->JsonData($slider);
    }

    public function edit($id)
    {
        $slider = slider::FindOrFail($id);
        $stages = Stage::all();
        return view('admin.slider.edit', compact('slider', 'stages'));
    }

    public function update(EditSliderRequest $request)
    {
        $slider = slider::FindOrFail($request->slider_id);
        $data = $request->only('title:ar', 'stage_id', 'title:en', 'description:ar', 'description:en', 'is_active');
        $slider = $this->sliderService->update($request, $slider, $data);
        return $this->JsonData($slider);
    }

    public function destroy(Request $request)
    {
        $slider = slider::FindOrFail($request->id);
        if ($slider->photo != 'default.png') {
            Storage::delete('public/images/sliders/' . $slider->photo);
        }
        return $this->Delete($request->id, $slider);
    }
}
