<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Coupon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.coupon.index');
    }

  public function datatable()
{
    $coupons = Coupon::all();
    $coupons = $coupons->reverse();
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
            $edit = '<a href="' . route('dashboard.coupons.edit', $row->id) . '" class="btn btn-success">تعديل</a>';
            $add = '<a href="' . route('dashboard.vouchers', $row->id) . '" class="btn btn-primary">ادارة الاكواد</a>';
            return $edit . ' ' . $delete. ' '.$add;
        })
        ->rawColumns(['operation' => 'operation', 'image' => 'image', 'count'=>'count'])
        ->toJson();
}

public function add()
    {
        return view('admin.coupon.create');
    }
    
  public function store(Request $request)
    {
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

            // Create directory if it doesn't exist
            $path = public_path('data/images/coupons');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Move the file directly to public folder
            $image->move($path, $imageName);
            $imagePath = 'data/images/coupons/' . $imageName;
        }

        $coupon = Coupon::create([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $imagePath,
            'type' => $request->type
        ]);

        return response()->json([
            'message' => 'تم حفظ الكوبون بنجاح',
            'coupon' => $coupon
        ], 201);
    }

    
    public function edit(Coupon $coupon){
        return view('admin.coupon.edit', compact('coupon'));
    }
    
   public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:weekly,monthly,package',
        ]);

        $coupon = Coupon::findOrFail($id);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($coupon->image && file_exists(public_path($coupon->image))) {
                unlink(public_path($coupon->image));
            }

            $image = $request->file('image');
            $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();

            // Create directory if it doesn't exist
            $path = public_path('data/images/coupons');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Move the file directly to public folder
            $image->move($path, $imageName);
            $coupon->image = 'data/images/coupons/' . $imageName;
        }

        $coupon->name = $request->name;
        $coupon->price = $request->price;
        $coupon->type = $request->type;
        $coupon->save();

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
