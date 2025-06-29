<?php

namespace App\Http\Controllers\MinAdmin;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Coupon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Voucher;
use Illuminate\Http\Request;

class MiniVoucherController extends Controller
{
     public function index(Coupon $coupon)
    {
        
        return view('min_admin.voucher.index', compact('coupon'));
    }

  public function datatable(Coupon $coupon)
{
    $vouchers = $coupon->vouchers;
    return DataTables::of($vouchers)
        ->addColumn('code', function ($row) {
            return $row->code;
        })->addColumn('image', function ($row) {
            if($row->image !== null){
            $image = '<img src="' . url($row->image). '" alt="coupon-image" style="height:80px;width:100px" class="rounded">';
            return $image;
            }else{
                return null;
            }
        })
       ->addColumn('state', function ($row) {
    if ($row->is_used) {
        return '<span class="badge bg-danger">مستخدم</span>';
    } else {
        return '<span class="badge bg-success">غير مستخدم</span>';
    }
})

        ->addColumn('operation', function ($row) {
            $delete = '<a voucher_id=' . $row->id . ' class="btn btn-danger delete_btn">حذف</a>';
            $edit = '<a href="' . route('dashboard.mini.vouchers.edit', $row->id) . '" class="btn btn-success">تعديل</a>';
         
            return $edit . ' ' . $delete;
        })
        ->rawColumns(['operation' => 'operation', 'state' => 'state', "image" => "image"])
        ->toJson();
}

public function add(Coupon $coupon)
    {
        return view('min_admin.voucher.create', compact('coupon'));
    }
    
       public function store(Coupon $coupon, Request $request)
    {
       
        $request->validate([
    'code' => 'required_without:image|string|max:255',
    'image' => 'required_without:code|image|mimes:jpeg,png,jpg,gif|max:2048',
]);

$imagePath = null;

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();

        // حفظ الصورة في المسار public/images/coupons
        $image->storeAs('public/images/vouchers', $imageName);

        // تسجيل المسار المختلف في قاعدة البيانات
        $imagePath = 'data/images/vouchers/' . $imageName;
    }

      
$voucher = $coupon->vouchers()->create([
    'code' => $request->code,
    'image' => $imagePath
   
]);


        // إرجاع استجابة JSON بنجاح
        return response()->json([
            'message' => 'تم حفظ الكود بنجاح',
            'coupon' => $voucher
        ], 201);
    }
    
    public function edit(Voucher $voucher){
        return view('min_admin.voucher.edit', compact('voucher'));
    }
    
   public function update(Request $request, $id)
{
    // التحقق من صحة البيانات
    $request->validate([
        'code' => 'nullable|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'remove_image' => 'nullable|boolean' // حقل اختياري لإزالة الصورة
    ]);

    $voucher = Voucher::findOrFail($id);

    // تحديث الكود حتى لو تم إرساله فارغًا
    if ($request->has('code')) {
        $voucher->code = $request->code;
    }

    // إزالة الصورة إذا تم إرسال remove_image = true
    if ($request->remove_image) {
        if ($voucher->image && Storage::exists('public/' . str_replace('/storage/', '', $voucher->image))) {
            Storage::delete('public/' . str_replace('/storage/', '', $voucher->image));
        }
        $voucher->image = null; // إزالة المسار من قاعدة البيانات
    }

    // التحقق من الصورة الجديدة
    if ($request->hasFile('image')) {
        // حذف الصورة القديمة إذا كانت موجودة
        if ($voucher->image && Storage::exists('public/' . str_replace('/storage/', '', $voucher->image))) {
            Storage::delete('public/' . str_replace('/storage/', '', $voucher->image));
        }

        // رفع الصورة الجديدة
        $image = $request->file('image');
        $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('public/images/vouchers', $imageName);

        $imagePath = 'storage/images/vouchers/' . $imageName; // المسار الجديد
        $voucher->image = $imagePath;
    }

    // حفظ التغييرات
    $voucher->save();

    // إرجاع الاستجابة
    return response()->json([
        'message' => 'تم تحديث الكوبون بنجاح',
        'voucher' => $voucher,
    ]);
}


public function destroy(Request $request)
{
    $request->validate([
        'id' => 'required|exists:vouchers,id',
    ]);

    
    $voucher = Voucher::findOrFail($request->id);
    $voucher->delete();

   
    return response()->json([
        'id' => $request->id,
        'message' => 'تم حذف الكوبون بنجاح',
    ]);
}

}
