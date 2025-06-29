<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainCategory;
use App\Services\MainCategoryService;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\EditCategoryRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MainCategoriesController extends Controller
{
    protected $categoryService;

    public function __construct(MainCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        return view('admin.main_category.index');
    }

    public function datatable()
    {
        $categories = MainCategory::select(['id', 'name', 'icon_image'])->get();

        return DataTables::of($categories)
            ->addColumn('icon_image', function ($row) {
                return '<img src="' . asset('storage/' . $row->icon_image) . '" alt="Category Image" style="width: 100px; height: 100px;">';
            })

            ->addColumn('operation', function ($row) {
                return '
                    <a href="' . route("dashboard.main_categories.edit", $row->id) . '" class="btn btn-primary btn-sm">تعديل</a>
                    <button class="btn btn-danger btn-sm delete_btn" category_id="' . $row->id . '">حذف</button>
                ';
            })
            ->rawColumns(['icon_image', 'operation']) // ✅ Include 'icon_image' in raw columns
            ->toJson();
    }



    public function create()
    {
        return view('admin.main_category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:main_categories|max:255',
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        $iconPath = null;

        if ($request->hasFile('icon_image')) {
            $destinationPath = public_path('storage/images/categories');
            $filename = uniqid() . '.' . $request->file('icon_image')->getClientOriginalExtension();
            $request->file('icon_image')->move($destinationPath, $filename);
            $icon_image = "/images/categories/" . $filename;
        }

        $category = MainCategory::create([
            'name' => $request->name,
            'icon_image' => $icon_image
        ]);

        return response()->json([
            'message' => 'Category created successfully!',
            'category' => $category,
        ]);
    }





    public function edit($id)
    {
        return view('admin.main_category.edit', ['category' => MainCategory::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        try {
            $category = MainCategory::findOrFail($id);

            // ✅ Validate Request
            $request->validate([
                'name' => 'required|string|unique:main_categories,name,' . $id,
                'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg'
            ]);

            // ✅ Preserve existing icon/image paths
            $icon_image = $category->icon_image;

            // ✅ If a new image is uploaded, delete the old one and save the new image
            if ($request->hasFile('icon_image')) {
                if (!empty($category->icon_image) && file_exists(public_path($category->icon_image))) {
                    unlink(public_path($category->icon_image)); // Remove the old image
                }

                // ✅ Save new image
                $destinationPath = public_path('storage/images/categories');
                $filename = uniqid() . '.' . $request->file('icon_image')->getClientOriginalExtension();
                $request->file('icon_image')->move($destinationPath, $filename);
                $icon_image = "/images/categories/" . $filename; // Update new image path
            }


            // ✅ Update the category
            $category->update([
                'name' => $request->name,
                'icon_image' => $icon_image
            ]);

            return response()->json([
                'message' => 'Category updated successfully!',
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            Log::error('Category Update Error: ' . $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }









    public function destroy(Request $request)
    {
        $category = MainCategory::findOrFail($request->id);
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully!',
        ]);
    }
}
