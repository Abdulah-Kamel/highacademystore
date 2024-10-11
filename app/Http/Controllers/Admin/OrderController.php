<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Slider;
use App\Traits\ImageTrait;
use App\Traits\DeleteTrait;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Services\SliderService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\SliderRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EditSliderRequest;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    use ImageTrait, DeleteTrait, GeneralTrait;

    protected $sliderService;

    public function __construct(SliderService $sliderService)
    {
        $this->sliderService = $sliderService;
    }

    public function index()
    {
        return view('admin.order.index');
    }

   public function datatable(Request $request)
{
    // بناء الاستعلام بناءً على الفلتر المرسل
    $query = Order::query();

    // التحقق من وجود فلتر الحالة وتطبيقه على الاستعلام
    if ($request->has('state') && !empty($request->state)) {
        $query->where('status', $request->state);
    }

    // ترتيب النتائج حسب id
    $orders = $query->orderBy('id', 'DESC')->get();

    return DataTables::of($orders)
        ->editColumn('name', function ($row) {
            return $row->users->name ?? "";
        })
        ->editColumn('phone', function ($row) {
            return $row->users->phone ?? "";
        })
        ->editColumn('email', function ($row) {
            return $row->users->email ?? "";
        })
        ->editColumn('address', function ($row) {
            return $row->address ?? "";
        })
        ->addColumn('details', function ($row) {
            $details = '<a href="' . route('dashboard.orders.details', $row->id) . '" type="button" class="btn btn-lg btn-block btn-success lift text-uppercase p-3 ">تفاصيل</a>';
            return $details;
        })
        ->editColumn('method', function ($row) {
            return $row->method ?? "";
        })
        ->addColumn('account', function ($row) {
            return $row->account;
        })
        ->addColumn('image', function ($row) {
            $link = asset('images/reciept/') . "/" . $row->image;
            return "<a href='" . $link . "' target='_blank'>عرض الصورة</a>";
        })
        ->addColumn('fast_ship', function ($row) {
            if ($row->is_fastDelivery == 0) {
                return "<h6 class='text-danger'>شحن عادي</h6>";
            } elseif ($row->is_fastDelivery == 1) {
                return "<h6 class='text-success'>شحن سريع</h6>";
            }
        })
        ->addColumn('state', function ($row) {
            switch ($row->status) {
                case "new":
                    return "<h2 class='badge bg-warning text-dark'>طلب جديد</h2>";
                case "success":
                    return "<h2 class='badge bg-success'>طلب ناجح</h2>";
                case "cancelled":
                    return "<h2 class='badge bg-danger'>طلب ملغي</h2>";
            }
        })
        ->rawColumns(['name', 'phone', 'email', 'address', 'method', 'details', 'account', 'image', 'state', 'fast_ship'])
        ->toJson();
}


    public function details($id)
    {
        $order = Order::find($id);
        return view('admin.order.details', compact('order'));
    }

    public function changestate(Request $request)
    {
        $state = $request->state;
        $orderid = $request->id;
        $order = Order::findOrFail($orderid);
        try {
            DB::beginTransaction();
            if ($state == "1") {
                $order->status = "success";
                $order->is_paid = "1";
            } elseif ($state == "2") {
                $order->status = "cancelled";
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
            return response()->json([
                "success" => false,
                'code' => 400,
                'msg' => "خطأ اثناء التنفيذ"
            ], 400);
        }
    }

}
