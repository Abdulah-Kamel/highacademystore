<?php

namespace App\Http\Controllers\Admin;

use App\Http\Actions\AdminAction;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    use ImageTrait;

    public string $folderPath = "Admin.admins.";
    public array $data = ['name', 'email', 'phone', 'image', 'password'];
    public string $route = "admins";
    public string $folderImages = "admins";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $admins = Admin::all();
            return DataTables::of($admins)
                ->addIndexColumn()
                ->editColumn('image', function ($row) {
                    return $this->getImage($row->image);
                })
                ->addColumn('status', function ($row) {
                    if (Auth::guard('admin')->user()->id == $row->id) {
                        return '<p style="font-weight: bold; color: green">نشط الان</p>';
                    } else {
                        return '<p style="font-weight: bold; color: red">غير نشط  </p>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    if (Auth::guard('admin')->user()->id == $row->id) {
                        return '<button class="btn btn-warning" id="btnEdit" data-id=" ' . $row->id . ' ">تعديل</button>';
                    } else {
                        return
                            '<button class="btn btn-warning" id="btnEdit" data-id=" ' . $row->id . ' ">تعديل</button>
                         <button class="btn btn-danger" id="btnDelete" data-id=" ' . $row->id . ' ">حذف</button>';
                    }

                })
                ->rawColumns(['image', 'actions','status'])
                ->toJson();
        } else {
            return view($this->folderPath . 'index');
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\request()->ajax()) {
            $returnHtml = view($this->folderPath . 'create')->render();
            return response()->json(['html' => $returnHtml]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AdminAction $action)
    {
        $data = $request->only($this->data);
        $admin = $action->storeAdmin($data);
        return response()->json(['success' => 'Message']);
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
    public function edit(string $id, AdminAction $action)
    {
        if (\request()->ajax()) {
            $returnHtml = view($this->folderPath . 'edit')
                ->with(['obj' => $action->find($id)])
                ->render();
            return response()->json(['html' => $returnHtml]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, AdminAction $action)
    {
        $data = $request->only($this->data);
        $action->updateAdmin($id, $data);
        return response()->json(['success' => 'تم تعديل المستخدم بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, AdminAction $action)
    {
        $action->delete($id);
        return response()->json(['success' => 'الحذف بنجاح']);
    }
}
