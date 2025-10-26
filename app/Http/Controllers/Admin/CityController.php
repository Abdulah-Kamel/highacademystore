<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $governorate = null;
        if ($request->has('governorate')) {
            $governorate = Governorate::findOrFail($request->governorate);
        }
        
        $governorates = Governorate::all();
        
        return view('admin.cities.index', compact('governorate', 'governorates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $governorates = Governorate::all();
        $selectedGovernorate = null;
        
        if ($request->has('governorate')) {
            $selectedGovernorate = Governorate::findOrFail($request->governorate);
        }
        
        return view('admin.cities.create', compact('governorates', 'selectedGovernorate'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'status' => 'boolean'
        ], [
            'governorate_id.required' => 'المحافظة مطلوبة',
            'governorate_id.exists' => 'المحافظة المحددة غير موجودة',
            'name_ar.required' => 'الاسم باللغة العربية مطلوب',
            'name_en.required' => 'الاسم باللغة الإنجليزية مطلوب'
        ]);

        City::create([
            'governorate_id' => $request->governorate_id,
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'status' => $request->has('status')
        ]);

        return redirect()->route('dashboard.cities.index', ['governorate' => $request->governorate_id])
            ->with('success', 'تم إضافة المدينة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        return view('admin.cities.show', compact('city'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        $governorates = Governorate::all();
        return view('admin.cities.edit', compact('city', 'governorates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'status' => 'boolean'
        ], [
            'governorate_id.required' => 'المحافظة مطلوبة',
            'governorate_id.exists' => 'المحافظة المحددة غير موجودة',
            'name_ar.required' => 'الاسم باللغة العربية مطلوب',
            'name_en.required' => 'الاسم باللغة الإنجليزية مطلوب'
        ]);

        $city->update([
            'governorate_id' => $request->governorate_id,
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'status' => $request->has('status')
        ]);

        return redirect()->route('dashboard.cities.index', ['governorate' => $request->governorate_id])
            ->with('success', 'تم تحديث المدينة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        try {
            $city->delete();
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المدينة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المدينة'
            ], 500);
        }
    }

    /**
     * DataTables data source
     */
    public function datatable(Request $request)
    {
        $query = City::with('governorate');
        
        if ($request->has('governorate') && $request->governorate) {
            $query->where('governorate_id', $request->governorate);
        }

        return DataTables::of($query)
            ->addColumn('name_ar', fn($row) => '<strong>' . e($row->name_ar) . '</strong>')
            ->addColumn('name_en', fn($row) => e($row->name_en))
            ->addColumn('governorate', fn($row) => e($row->governorate->name_ar))
            ->addColumn('status', function ($row) {
                return $row->status 
                    ? '<span class="badge bg-success">نشط</span>'
                    : '<span class="badge bg-secondary">غير نشط</span>';
            })
            ->addColumn('actions', function ($row) {
                $editBtn = '<a href="' . route('dashboard.cities.edit', $row->id) . '" class="btn btn-sm btn-primary mx-1">
                    <i class="fa fa-edit"></i> تعديل
                </a>';

                $deleteBtn = '<button class="btn btn-sm btn-danger mx-1" onclick="deleteCity(' . $row->id . ')">
                    <i class="fa fa-trash"></i> حذف
                </button>';

                return '<div class="d-flex gap-1">' . $editBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['name_ar', 'status', 'actions'])
            ->make(true);
    }

    /**
     * Import cities from JSON file
     */
    public function importFromJson()
    {
        try {
            $jsonPath = storage_path('cities/cities.json');
            
            if (!file_exists($jsonPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ملف المدن غير موجود'
                ], 404);
            }

            $cities = json_decode(file_get_contents($jsonPath), true);
            $imported = 0;
            $skipped = 0;

            foreach ($cities as $cityData) {
                $exists = City::where('governorate_id', $cityData['governorate_id'])
                    ->where('name_ar', $cityData['city_name_ar'])
                    ->exists();

                if (!$exists) {
                    City::create([
                        'governorate_id' => $cityData['governorate_id'],
                        'name_ar' => $cityData['city_name_ar'],
                        'name_en' => $cityData['city_name_en'],
                        'status' => true
                    ]);
                    $imported++;
                } else {
                    $skipped++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "تم استيراد {$imported} مدينة، تم تخطي {$skipped} مدينة موجودة مسبقاً"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استيراد المدن: ' . $e->getMessage()
            ], 500);
        }
    }
}
