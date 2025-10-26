<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GovernorateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.governorates.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.governorates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'governorate_name_ar' => 'required|string|max:255',
            'governorate_name_en' => 'required|string|max:255',
            'home_shipping_price' => 'required|numeric|min:0',
            'post_shipping_price' => 'required|numeric|min:0',
        ], [
            'governorate_name_ar.required' => 'الاسم باللغة العربية مطلوب',
            'governorate_name_en.required' => 'الاسم باللغة الإنجليزية مطلوب',
            'home_shipping_price.required' => 'سعر التوصيل للمنزل مطلوب',
            'home_shipping_price.numeric' => 'سعر التوصيل للمنزل يجب أن يكون رقماً',
            'home_shipping_price.min' => 'سعر التوصيل للمنزل لا يمكن أن يكون أقل من صفر',
            'post_shipping_price.required' => 'سعر التوصيل لمكتب البريد مطلوب',
            'post_shipping_price.numeric' => 'سعر التوصيل لمكتب البريد يجب أن يكون رقماً',
            'post_shipping_price.min' => 'سعر التوصيل لمكتب البريد لا يمكن أن يكون أقل من صفر',
        ]);

        Governorate::create([
            'governorate_name_ar' => $request->governorate_name_ar,
            'governorate_name_en' => $request->governorate_name_en,
            'price' => $request->home_shipping_price,
            'home_shipping_price' => $request->home_shipping_price,
            'post_shipping_price' => $request->post_shipping_price,
        ]);

        return redirect()->route('dashboard.governorates.index')
            ->with('success', 'تم إضافة المحافظة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Governorate $governorate)
    {
        return view('admin.governorates.show', compact('governorate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Governorate $governorate)
    {
        return view('admin.governorates.edit', compact('governorate'));
    }

        /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Governorate $governorate)
    {
        $request->validate([
            'governorate_name_ar' => 'required|string|max:255',
            'governorate_name_en' => 'required|string|max:255',
            'home_shipping_price' => 'required|numeric|min:0',
            'post_shipping_price' => 'required|numeric|min:0',
        ], [
            'governorate_name_ar.required' => 'الاسم باللغة العربية مطلوب',
            'governorate_name_en.required' => 'الاسم باللغة الإنجليزية مطلوب',
            'home_shipping_price.required' => 'سعر التوصيل للمنزل مطلوب',
            'home_shipping_price.numeric' => 'سعر التوصيل للمنزل يجب أن يكون رقماً',
            'home_shipping_price.min' => 'سعر التوصيل للمنزل لا يمكن أن يكون أقل من صفر',
            'post_shipping_price.required' => 'سعر التوصيل لمكتب البريد مطلوب',
            'post_shipping_price.numeric' => 'سعر التوصيل لمكتب البريد يجب أن يكون رقماً',
            'post_shipping_price.min' => 'سعر التوصيل لمكتب البريد لا يمكن أن يكون أقل من صفر',
        ]);

        $governorate->update([
            'governorate_name_ar' => $request->governorate_name_ar,
            'governorate_name_en' => $request->governorate_name_en,
            'price' => $request->home_shipping_price,
            'home_shipping_price' => $request->home_shipping_price,
            'post_shipping_price' => $request->post_shipping_price,
        ]);

        return redirect()->route('dashboard.governorates.index')
            ->with('success', 'تم تحديث المحافظة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Governorate $governorate)
    {
        try {
            $governorate->delete();
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المحافظة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المحافظة'
            ], 500);
        }
    }

        /**
     * DataTables data source
     */
    public function datatable(Request $request)
    {
        $query = Governorate::withCount('cities');

        return DataTables::of($query)
            ->addColumn('name_ar', fn($row) => '<strong>' . e($row->governorate_name_ar) . '</strong>')
            ->addColumn('name_en', fn($row) => e($row->governorate_name_en))
            ->addColumn('shipping_price', function ($row) {
                $home = number_format($row->home_cost, 2);
                $post = number_format($row->post_cost, 2);

                return "<div class=\"text-nowrap\">
                            <div><span class=\"badge bg-primary\">منزل</span> {$home} جنيه</div>
                            <div class=\"mt-1\"><span class=\"badge bg-info text-dark\">بريد</span> {$post} جنيه</div>
                        </div>";
            })
            ->addColumn('cities_count', fn($row) => $row->cities_count . ' مدينة')
            ->addColumn('actions', function ($row) {
                $editBtn = '<a href="' . route('dashboard.governorates.edit', $row->id) . '" class="btn btn-sm btn-primary mx-1">
                    <i class="fa fa-edit"></i> تعديل
                </a>';

                $deleteBtn = '<button class="btn btn-sm btn-danger mx-1" onclick="deleteGovernorate(' . $row->id . ')">
                    <i class="fa fa-trash"></i> حذف
                </button>';

                $citiesBtn = '<a href="' . route('dashboard.cities.index', ['governorate' => $row->id]) . '" class="btn btn-sm btn-info mx-1">
                    <i class="fa fa-list"></i> المدن
                </a>';

                return '<div class="d-flex gap-1">' . $editBtn . $citiesBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['name_ar', 'shipping_price', 'actions'])
            ->make(true);
    }
}
