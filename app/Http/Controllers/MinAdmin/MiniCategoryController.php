<?php

namespace App\Http\Controllers\MinAdmin;

use App\Models\Category;
use App\Traits\ImageTrait;
use App\Traits\DeleteTrait;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\EditCategoryRequest;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class MiniCategoryController extends Controller
{
    use ImageTrait, DeleteTrait, GeneralTrait;

    public function getChildByParentID(Request $request, $id)
    {
        // dd($id);
        $category = Category::find($request->id);
        if ($category) {
            $child_id = Category::getChildByParentID($request->id);
            if (count($child_id) <= 0) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'msg' => ''
                ]);
            }
            return response()->json([
                'status' => true,
                'data' => $child_id,
                'msg' => ''
            ]);
        } else {
            return response()->json([
                'status' => false,
                'data' => null,
                'msg' => 'category not found'
            ]);
        }
    }
}
