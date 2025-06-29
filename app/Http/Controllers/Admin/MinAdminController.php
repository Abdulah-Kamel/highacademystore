<?php

namespace App\Http\Controllers\Admin;

use App\Models\MiniAdmin;
use App\Traits\ImageTrait;
use App\Traits\DeleteTrait;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Services\MiniAdminService;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Requests\MiniAdminRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EditMinAdminRequest;
use Yajra\DataTables\Facades\DataTables;

class MinAdminController extends Controller
{
    use ImageTrait, DeleteTrait, GeneralTrait;

    protected $MiniAdminService;

    public function __construct(MiniAdminService $MiniAdminService)
    {
        $this->MiniAdminService = $MiniAdminService;
    }

    public function index()
    {
        // $sliders = Slider::with('translations')->actived()->get();
        return view('admin.mini_admin.index');
    }

    public function datatable()
    {
        $miniAdmin = $this->MiniAdminService->findAll();
        return DataTables::of($miniAdmin)
            ->addColumn('id', function ($row) {
                return $row->id;
            })
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('email', function ($row) {
                return $row->email;
            })
            ->addColumn('operation', function ($row) {
                $delete = '<a min_admin_id=' . $row->id . ' class="btn btn-lg btn-block btn-danger lift text-uppercase delete_btn"> حذف</a>';
                $edit = '<a href="' . route('dashboard.minadmin.edit', $row->id) . '" type="button" class="btn btn-lg btn-block btn-success lift text-uppercase">تعديل</a>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['name' => 'name', 'email' => 'email', 'operation' => 'operation'])
            ->toJson();
    }

    public function create()
    {
        return view('admin.mini_admin.create');
    }

    public function store(MiniAdminRequest $request)
    {
        $data = $request->only('name', 'email', 'password');
        $data['password'] = bcrypt($request->password);
        $miniAdmin = $this->MiniAdminService->save($request, $data);
        return $this->JsonData($miniAdmin);
    }

    public function edit($id)
    {
        $miniAdmin = MiniAdmin::FindOrFail($id);
        return view('admin.mini_admin.edit', compact('miniAdmin'));
    }

    public function update(EditMinAdminRequest $request)
    {
        $miniAdmin = MiniAdmin::FindOrFail($request->min_admin_id);
        $data = $request->only('name', 'email', 'password');
        
        $data = array_filter($data, function ($value) {
            return !is_null($value);
        });

        $miniAdmin = $this->MiniAdminService->update($request, $miniAdmin, $data);

        return $this->JsonData($miniAdmin);
    }

    public function destroy(Request $request)
    {
        $miniAdmin = MiniAdmin::FindOrFail($request->id);
        return $this->Delete($request->id, $miniAdmin);
    }
}
