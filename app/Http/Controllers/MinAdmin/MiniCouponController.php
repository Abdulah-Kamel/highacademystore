<?php

namespace App\Http\Controllers\MinAdmin;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Coupon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class MiniCouponController extends Controller
{
    public function index()
    {
        return view('min_admin.coupon.index');
    }

  public function datatable()
{
    $coupons = Coupon::all();
    return DataTables::of($coupons)
        ->addColumn('name', function ($row) {
            return $row->name;
        })
        ->addColumn('price', function ($row) {
            return $row->price;
        })
        ->addColumn('image', function ($row) {
            if($row->image !== null){
            $image = '<img src="' . url($row->image). '" alt="coupon-image" style="height:80px;width:100px" class="rounded">';
            return $image;
            }else{
                return null;
            }
        })
        ->addColumn('count', function ($row) {
            return $row->vouchers->where('is_used', 0)->count();
        })
        ->addColumn('operation', function ($row) {
            $delete = '<a coupon_id=' . $row->id . ' class="btn btn-danger delete_btn">حذف</a>';
            $edit = '<a href="' . route('dashboard.mini.coupons.edit', $row->id) . '" class="btn btn-success">تعديل</a>';
            $add = '<a href="' . route('dashboard.mini.vouchers', $row->id) . '" class="btn btn-primary">ادارة الاكواد</a>';
            return $edit . ' ' . $delete. ' '.$add;
        })
        ->rawColumns(['operation' => 'operation', 'image' => 'image', 'count'=>'count'])
        ->toJson();
}

public function add()
    {
        return view('min_admin.coupon.create');
    }
    
     public function store(Request $request)
{
    // التحقق من صحة البيانات
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'type' => 'required|in:weekly,monthly,package',
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();

        // حفظ الصورة في المسار public/images/coupons
        $image->storeAs('public/images/coupons', $imageName);

        // تسجيل المسار المختلف في قاعدة البيانات
        $imagePath = 'data/images/coupons/' . $imageName;
    }

    // حفظ الكوبون في قاعدة البيانات
    $coupon = Coupon::create([
        'name' => $request->name,
        'price' => $request->price,
        'image' => $imagePath, // تسجيل المسار المعدل في قاعدة البيانات
        'type' => $request->type
    ]);

    // إرجاع استجابة JSON بنجاح
    return response()->json([
        'message' => 'تم حفظ الكوبون بنجاح',
        'coupon' => $coupon
    ], 201);
}

public function edit(Coupon $coupon){
    return view('min_admin.coupon.edit', compact('coupon'));
}

public function update(Request $request, $id)
{
    // التحقق من صحة البيانات
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'type' => 'required|in:weekly,monthly,package',
    ]);

    // جلب الكوبون بناءً على المعرف
    $coupon = Coupon::findOrFail($id);

    // تحديث الصورة إذا تم رفع صورة جديدة
    if ($request->hasFile('image')) {
        // حذف الصورة القديمة إذا كانت موجودة
        if ($coupon->image && Storage::exists('public/' . str_replace('/storage/', '', $coupon->image))) {
            Storage::delete('public/' . str_replace('/storage/', '', $coupon->image));
        }

        // رفع الصورة الجديدة
        $image = $request->file('image');
        $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('public/images/coupons', $imageName);

        $imagePath = 'data/images/coupons/' . $imageName;
       
        $coupon->image = $imagePath;
    }

    // تحديث البيانات الأخرى
    $coupon->name = $request->name;
    $coupon->price = $request->price;
    $coupon->type = $request->type;
    $coupon->save();

    // إرجاع استجابة
    return response()->json([
        'message' => 'تم تحديث الكوبون بنجاح',
        'coupon' => $coupon,
    ]);
}

public function destroy(Request $request)
{
    $request->validate([
        'id' => 'required|exists:coupons,id',
    ]);

    
    $coupon = Coupon::findOrFail($request->id);

   
    if ($coupon->image && Storage::exists('public/' . str_replace('/storage/', '', $coupon->image))) {
        Storage::delete('public/' . str_replace('/storage/', '', $coupon->image));
    }

  
    $coupon->delete();

   
    return response()->json([
        'id' => $request->id,
        'message' => 'تم حذف الكوبون بنجاح',
    ]);
}


}
