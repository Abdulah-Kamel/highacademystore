<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Traits\ImageTrait;
use App\Traits\DeleteTrait;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Services\BrandService;
use Illuminate\Validation\Rule;
use App\Http\Requests\BrandRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditBrandRequest;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
    use ImageTrait, DeleteTrait, GeneralTrait;

    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index()
    {
        return view('admin.Brand.index');
    }

    public function datatable()
    {
        $brands = $this->brandService->findAll();
        return DataTables::of($brands)
            ->addColumn('title', function ($row) {
                return $row->title;
            })
            ->addColumn('photo', function ($row) {
                $image = '<img src ="' . $row->image_path . '" alt="profile-image" style="height:120px;width:150px" class="avatar rounded me-2" >';
                return $image;
            })
            ->addColumn('operation', function ($row) {
                $delete = '<a brand_id=' . $row->id . ' class="btn btn-lg btn-block btn-danger lift text-uppercase delete_btn"> حذف</a>';
                $edit = '<a href="' . route('dashboard.teachers.edit', $row->id) . '" type="button" class="btn btn-lg btn-block btn-success lift text-uppercase">تعديل</a>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['operation' => 'operation', 'photo' => 'photo'])
            ->toJson();
    }

    public function create()
    {
        return view('admin.Brand.create');
    }

    public function store(BrandRequest $request)
    {
        $data=$request->only('title:ar','title:en','description:ar','description:en');
        $brand = $this->brandService->save($request,$data);
        return $this->JsonData($brand);
    }

    public function edit($id)
    {
        $brand = Brand::FindOrFail($id);
        return view('admin.Brand.edit', compact('brand'));
    }

    public function update(EditBrandRequest $request)
    {
        $brand = Brand::FindOrFail($request->brand_id);
        $data = $request->only('title:ar', 'title:en','description:ar','description:en');
        $brand = $this->brandService->update($request,$brand,$data);
        return $this->JsonData($brand);
    }

    public function destroy(Request $request)
    {
        $brand = Brand::FindOrFail($request->id);
        if ($brand->photo != 'default.png') {
            Storage::delete('public/images/brands/' . $brand->photo);
        }
        return $this->Delete($request->id, $brand);
    }
}
