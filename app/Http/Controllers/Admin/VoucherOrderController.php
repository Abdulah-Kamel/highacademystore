<?php

namespace App\Http\Controllers\Admin;

use App\Mail\successCoupon;
use Illuminate\Support\Facades\Mail;
use App\Models\Voucher;
use App\Models\VouchersOrder;
use App\Models\Coupon;
use App\Models\User;
use App\Traits\ImageTrait;
use App\Traits\DeleteTrait;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Yajra\DataTables\Facades\DataTables;
use Mpdf\Mpdf;

class VoucherOrderController extends Controller
{
    use ImageTrait, DeleteTrait, GeneralTrait;

    public function __construct()
    {
    }

    public function index()
    {
        return view('admin.voucher_order.index');
    }

    public function datatable(Request $request)
    {
        // بناء الاستعلام بناءً على الفلتر المرسل
        $query = VouchersOrder::query();

        // التحقق من وجود فلتر الحالة وتطبيقه على الاستعلام
        if ($request->has('state') && !empty($request->state)) {
            $query->where('status', $request->state);
        }

        // ترتيب النتائج حسب id
        $voucherOrders = $query->orderBy('id', 'DESC')->get();

        logger($voucherOrders);

        return DataTables::of($voucherOrders)
            ->editColumn('id', function ($row) {
                return $row->id;
            })
            ->addColumn('details', function ($row) {
                $details = '<a href="' . route('dashboard.voucher_order.details', $row->id) . '" type="button" class="btn btn-lg btn-block btn-success lift text-uppercase p-3 ">تفاصيل</a>';
                return $details;
            })
            ->editColumn('method', function ($row) {
                return $row->method ?? "لا يوجد";
            })
            ->addColumn('account', function ($row) {
                return $row->account  ?? "لا يوجد";
            })
            ->addColumn('coupon', function ($row) {
                $coupon = Coupon::findOrFail($row->coupon_id);
                return $coupon->name  ?? "لا يوجد";
            })
            ->editColumn('quantity', function ($row) {
                return @$row->quantity  ;
            })
            ->addColumn('image', function ($row) {
                $link = asset('images/reciept/') . "/" . $row->image;
                return "<a href='" . $link . "' target='_blank'>عرض الصورة</a>" ;
            })
            ->addColumn('state', function ($row) {
                switch ($row->state) {
                    case "pending":
                        return "<h2 class='badge bg-warning text-dark'>منتظر التحقق</h2>";
                    case "success":
                        return "<h2 class='badge bg-success'>طلب ناجح</h2>";
                    case "cancelled":
                        return "<h2 class='badge bg-danger'>طلب ملغي</h2>";
                }
            })
            ->rawColumns(['state', 'account', 'method', 'details', 'image', 'coupon', 'id'])
            ->toJson();
    }


    public function details($id)
    {
        $order = VouchersOrder::find($id);
//        dd($order);
        $coupon = Coupon::findOrFail($order->coupon_id);

        return view('admin.voucher_order.details', compact('order', 'coupon'));
    }

    public function changestate(Request $request)
    {
        $state = $request->state;
        $orderid = $request->id;
        $order = VouchersOrder::findOrFail($orderid);
        try {
            DB::beginTransaction();
            if ($state == "1") {
                $order->state = "success";
                $coupon = Coupon::findOrFail($order->coupon_id);
                $users = User::findOrFail($order->user_id);
                $details = [
                    'id' => $order->id,
                    'name' => $users->name,
                    'coupon' => $coupon->name,
                    "email" => $users->email
                ];
                Mail::to($users->email)->send(new successCoupon($details));

                $qty = $order->quantity;

                $vouchers = Voucher::where("coupon_id", $coupon->id)
                ->where("is_used", 0)
                ->take($qty)
                ->get();

                foreach ($vouchers as $voucher) {
                    $voucher = Voucher::find($voucher->id);
                    $voucher->is_used = 1;
                    $voucher->user_id=  $users->id;
                    $voucher->save();
                }
            } elseif ($state == "2") {
                $order->state = "cancelled";
            }
            $order->save();
            DB::commit();

            return response()->json([
                "success" => false,
                'code' => 200,
                'msg' => "تنفيذ الاجراء"
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            return response()->json([
                "success" => false,
                'code' => 400,
                'msg' => "خطأ اثناء التنفيذ"
            ], 400);
        }
    }
}
