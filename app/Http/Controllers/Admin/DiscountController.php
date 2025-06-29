<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class DiscountController extends Controller
{
    /**
     * Display a listing of the coupons.
     */
    public function index()
    {
        // Retrieve the global discount setting from the discount_settings table.
        $discountSetting = DB::table('discount_settings')->first();

        return view('admin.discount.index', compact('discountSetting'));
    }



    public function toggleDiscountFeature(Request $request)
    {
        // Retrieve the current discount setting
        $setting = DB::table('discount_settings')->first();

        if ($setting) {
            // Toggle the value
            $newValue = !$setting->discount_enabled;

            // Update the table
            DB::table('discount_settings')->update([
                'discount_enabled' => $newValue,
                'updated_at' => now()
            ]);

            return response()->json([
                'message' => 'تم تحديث حالة الخصم بنجاح!',
                'discount_enabled' => $newValue
            ], 200);
        }

        return response()->json(['error' => 'Setting not found.'], 404);
    }


    /**
     * Return JSON data for DataTables.
     */
    public function datatable()
    {
        $coupons = Discount::select(['id', 'code', 'discount', 'usage_limit', 'used'])->get();

        return DataTables::of($coupons)
            ->addColumn('operation', function ($row) {
                return '
                    <a href="' . route("dashboard.discount.edit", $row->id) . '" class="btn btn-primary btn-sm">تعديل</a>
                    <button class="btn btn-danger btn-sm delete_btn" data-id="' . $row->id . '">حذف</button>
                ';
            })
            ->rawColumns(['operation'])
            ->toJson();
    }

    /**
     * Show the form for creating a new coupon.
     */
    public function create()
    {
        return view('admin.discount.create');
    }

    /**
     * Store a newly created coupon in storage.
     */
    public function store(Request $request)
    {
        try {
            $data =  $request->validate([
                'code'        => 'required|string|unique:discount,code',
                'discount'    => 'required|numeric',
                'usage_limit' => 'nullable|integer',
            ], [
                'code.unique' => 'This coupon code already exists.',
                'code.required' => 'Coupon code is required.',
                'discount.required' => 'Discount is required.',
                'usage_limit.integer' => 'Usage limit must be an integer.',
            ]);
            $coupon = Discount::create($data);

            return response()->json([
                'message' => 'Coupon created successfully!',
                'coupon'  => $coupon,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json($e->errors(), 422);
        }
    }

    /**
     * Show the form for editing the specified coupon.
     */
    public function edit($id)
    {
        $coupon = Discount::findOrFail($id);
        return view('admin.discount.edit', compact('coupon'));
    }

    /**
     * Update the specified coupon in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $coupon = Discount::findOrFail($id);

            $data = $request->validate([
                'code'        => 'required|string|unique:discount,code,' . $coupon->id,
                'discount'    => 'required|numeric',
                'usage_limit' => 'nullable|integer',
            ]);

            $coupon->update($data);

            return response()->json([
                'message' => 'Coupon updated successfully!',
                'coupon'  => $coupon,
            ]);
        } catch (\Exception $e) {
            Log::error('Coupon Update Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified coupon from storage.
     */
    public function destroy(Request $request)
    {
        $coupon = Discount::findOrFail($request->id);
        $coupon->delete();

        return response()->json([
            'message' => 'Coupon deleted successfully!',
        ]);
    }
}
