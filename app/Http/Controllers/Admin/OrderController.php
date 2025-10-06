<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\delivery;
use App\Mail\successPaid;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Services\SliderService;
use App\Traits\DeleteTrait;
use App\Traits\GeneralTrait;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;
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
        $shippingMethods = ShippingMethod::all();
        return view('admin.order.index', compact('shippingMethods'));
    }

    public function barcodeOrders()
    {
        return view('admin.barcode.orders');
    }

    public function orderbarcode()
    {
        return view('admin.barcode.index');
    }

    public function datatable(Request $request)
    {
        $query = Order::with(['user', 'shipping'])
            ->select('orders.*')
            ->orderBy('id', 'DESC');

        if ($request->has('state') && !empty($request->state)) {
            $query->where('status', $request->state);
        }
        if ($request->filled('shipping') && $request->shipping !== 'all') {
            $query->whereHas('shipping', function ($q) use ($request) {
                $q->where('type', $request->shipping);
            });
        }



        return DataTables::of($query)
            ->editColumn('name', function ($row) {
                return $row->name ?? ($row->user->name ?? 'N/A');
            })
            ->editColumn('phone', function ($row) {
                return $row->mobile;
            })
            ->editColumn('temp_mobile', function ($row) {
                return $row->temp_mobile;
            })
            ->editColumn('email', function ($row) {
                return $row->user->email ?? '';
            })
            ->editColumn('address', function ($row) {
                return $row->address ?? '';
            })
            ->addColumn('details', function ($row) {
                return '<a href="' . route('dashboard.orders.details', $row->id) . '" type="button" class="btn btn-lg btn-block btn-success lift text-uppercase p-3 ">تفاصيل</a>';
            })
            ->addColumn('edit_order', function ($row) {
                return '<a href="' . route('dashboard.editOrder', $row->id) . '" type="button" class="btn btn-lg btn-block btn-danger lift text-uppercase p-3 ">تعديل الطلب</a>';
            })
            ->addColumn('change_status', function ($row) {
                $dropdown = '<select class="form-control change-status" data-order-id="' . $row->id . '" onchange="handleStatusChange(this)">';
                $dropdown .= '<option value="" disabled selected>تغيير الحالة</option>';
                $statuses = ['pending', 'success', 'cancelled', 'reserved'];
                if ($row->status == 'new') {
                    $statuses = ['pending'];
                }
                foreach ($statuses as $status) {
                    if ($row->status != $status) {
                        $statusText = '';
                        switch ($status) {
                            case 'pending':
                                $statusText = 'طلب معلق';
                                break;
                            case 'success':
                                $statusText = 'طلب ناجح';
                                break;
                            case 'cancelled':
                                $statusText = 'طلب ملغي';
                                break;
                            case 'reserved':
                                $statusText = 'طلب محجوز';
                                break;
                        }
                        if ($statusText) {
                            $dropdown .= '<option value="' . route('dashboard.changeStatus', ['id' => $row->id, 'status' => $status]) . '">' . $statusText . '</option>';
                        }
                    }
                }
                $dropdown .= '</select>';
                return $dropdown;
            })
            ->editColumn('method', function ($row) {
                return $row->method ?? '';
            })
            ->addColumn('account', function ($row) {
                return $row->account;
            })
            ->addColumn('image', function ($row) {
                if ($row->image) {
                    $link = asset('storage/images/screens/' . $row->image);
                    return "<a href='" . $link . "' target='_blank'>عرض الصورة</a>";
                }
                return "لا يوجد";
            })
            ->addColumn('barcode', function ($row) {
                return $row->barcode;
            })
            ->addColumn('addbarcode', function ($row) {
                return '<a href=' . route('dashboard.order.editbarcode', $row->id) . ' type="button" class="btn btn-sm btn-block btn-success lift text-uppercase">أضافه الباركود</a>';
            })
            ->addColumn('admin_addbarcode', function ($row) {
                return '<a href=' . route('dashboard.orders.editbarcode', $row->id) . ' type="button" class="btn btn-sm btn-block btn-success lift text-uppercase">أضافه الباركود</a>';
            })
            ->addColumn('shipping_method', function ($row) {
            // return $row->shipping_name ?? $row->shipping->name;
            if ($method = $row->shipping) {
                    if ($method->address) {
                        return "{$method->name}";
                    } else {
                        return "{$method->name}";
                    }
                }
                //  else {
                //     return "{$row->shipping_method}";
                // }
                return '—';
            })

            ->addColumn('state', function ($row) {
                switch ($row->status) {
                    case "new":
                        return "<h2 class='badge bg-warning text-dark'>طلب جديد</h2>";
                    case "success":
                        return "<h2 class='badge bg-success'>طلب ناجح</h2>";
                    case "cancelled":
                        return "<h2 class='badge bg-danger'>طلب ملغي</h2>";
                    case "pending":
                        return "<h2 class='badge bg-info'>طلب معلق</h2>";
                    case "reserved":
                        return "<h2 class='badge bg-primary'>طلب محجوز</h2>";
                    default:
                        return '';
                }
            })
            ->rawColumns(['details', 'edit_order', 'change_status', 'image', 'state', 'addbarcode', 'admin_addbarcode'])
            ->toJson();
    }



    public function details($id)
    {
        $order = Order::with('shipping')->findOrFail($id);
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
                $order->tracker = "shipped"; // second stage
                $details = [
                    'id' => $order->id,
                    'name' => $order->name,
                    'shipping' => $order->shipping,
                ];
                $order->load(['shipping', 'orderDetails.products']);

                Mail::to($order->user->email)->queue(new successPaid($order));
            } elseif ($state == "2") {
                $order->status = "cancelled";
                foreach ($order->orderDetails as $detail) {
                    $product = Product::find($detail->product_id);
                    $product->quantity = $product->quantity + $detail->amout;
                    if ($product->state == 0) {
                        // $product->state = 1;
                    }
                    $product->save();
                }
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

    public function editbarcode($id)
    {
        $order = Order::find($id);
        return view('min_admin.barcode.edit', compact('order'));
    }

    public function admineditbarcode($id)
    {
        $order = Order::find($id);
        return view('admin.barcode.edit', compact('order'));
    }

    public function addbarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'id' => 'required|exists:orders,id'
        ], [
            'barcode.required' => 'حقل الباركود مطلوب',
            'barcode.string' => 'الباركود يجب أن يكون نص',
            'id.required' => 'معرف الطلب مطلوب',
            'id.exists' => 'الطلب غير موجود'
        ]);

        $barcode = $request->barcode;
        $orderid = $request->id;
        $order = Order::find($orderid);
        try {
            DB::beginTransaction();

            $order->barcode = $barcode;
            $order->tracker = "delivered"; // third stage
            $order->save();

            DB::commit();

            $shippingMethod = ShippingMethod::find($order->shipping_method);

            $details = [
                'id' => $order->id,
                'name' => $order->name,
                'shipping' => $shippingMethod,
                'barcode' => $order->barcode
            ];

            Mail::to($order->user->email)->queue(new delivery($details));

            return response()->json([
                "success" => true,
                'code' => 200,
                'msg' => "تم تحديث الباركود بنجاح"
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


    public function export(Request $request)
    {
        $limit = $request->query('limit', 10);
        $status = $request->query('status');
        $shipping = $request->query('shipping');

        $query = Order::with('shipping')->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($shipping) {
            $query->where('shipping_method', $shipping);
        }

        $orders = $query->limit($limit)->get();

        $mpdf = new Mpdf([
            'default_font' => 'DejaVu Sans',
            'mode' => 'utf-8',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'format' => 'A4'
        ]);
        $html = view('admin.order.export', compact('orders'))->render();

        $mpdf->WriteHTML($html);
        $mpdf->Output('orders.pdf', 'D');
        exit;
    }

    public function successExport(Request $request)
    {
        ini_set('pcre.backtrack_limit', 10000000);

        $limit  = $request->query('limit', 10);
        $status = $request->query('status');
        $shipping = $request->query('shipping');

        $query = Order::with(['orderDetails.products', 'shipping'])
            ->whereDoesntHave('shipping', function ($q) {
                $q->where('type', 'branch');
            })
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($shipping) {
            $query->where('shipping_method', $shipping);
        }

        $orders = $query->limit($limit)->get();

        $mpdf = new Mpdf([
            'default_font'   => 'DejaVu Sans',
            'mode'           => 'utf-8',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'format'         => 'A4',
        ]);

        $html = view('admin.order.successExport', compact('orders'))->render();
        $mpdf->WriteHTML($html);
        $mpdf->Output('success-orders.pdf', 'D');
        exit;
    }
    
    public function groupedExport(Request $request)
    {
        ini_set('pcre.backtrack_limit', 10000000);
        $limit = $request->query('limit', 10);
        $status = $request->query('status');
        $shipping = $request->query('shipping');

        $query = Order::with('shipping')->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($shipping) {
            $query->where('shipping_method', $shipping);
        }

        $orders = $query->limit($limit)->get();

        $mpdf = new Mpdf([
            'default_font' => 'DejaVu Sans',
            'mode' => 'utf-8',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'format' => 'A4'
        ]);
        $html = view('admin.order.groupedExport', compact('orders'))->render();

        $mpdf->WriteHTML($html);
        $mpdf->Output('grouped-orders.pdf', 'D');
        exit;
    }


    public function branchExport(Request $request)
    {
        ini_set('pcre.backtrack_limit', 10000000);

        $limit  = $request->query('limit', 10);
        $status = $request->query('status');
        $shipping = $request->query('shipping');

        $query = Order::with(['orderDetails.products', 'shipping'])
            ->whereHas('shipping', function ($q) {
                $q->where('type', 'branch');
            })
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($shipping) {
            $query->where('shipping_method', $shipping);
        }

        $orders = $query->limit($limit)->get();

        $mpdf = new Mpdf([
            'default_font'   => 'DejaVu Sans',
            'mode'           => 'utf-8',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'format'         => 'A4',
        ]);

        $html = view('admin.order.export', compact('orders'))->render();
        $mpdf->WriteHTML($html);
        $mpdf->Output('branch-orders.pdf', 'D');
        exit;
    }

    public function changeStatus($order_id, $status)
    {
        $order = Order::find($order_id);
        switch ($status) {
            case 'success':
                $order->update(['status' => 'success']);
                break;
            case 'reserved':
                $order->update(['status' => 'reserved']);
                break;
            case 'pending':
                $order->update(['status' => 'pending']);
                break;
            case 'cancelled':
                $order->update(['status' => 'cancelled']);
                break;
            default:
                $order->update(['status' => 'Not Found']);
        }

        return redirect()->to(route('dashboard.orders'));
    }

    //    public function changeSuccessStatus($order_id)
    //    {
    //        $order = Order::find($order_id);
    //        $order->update(['status' => 'success']);
    //        return redirect()->to(route('dashboard.orders'));
    //    }

    public function updateOrder(Request $request, $order_id)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'mobile'                => 'required|string',
            'address'               => 'required|string|max:255',
            'address2'              => 'nullable|string|max:255',
            'near_post'             => 'nullable|string|max:255',
            'shipping_method'    => 'required|exists:shipping_methods,id',
        ]);

        $order = Order::findOrFail($order_id);
        $order->fill($data);

        // overwrite name/address from the shipping method
        $method = \App\Models\ShippingMethod::find($data['shipping_method']);
        $order->shipping_method  = $method->id;
        $order->shipping_name    = $method->name;
        $order->shipping_address = $method->address;

        $order->save();

        return redirect()
            ->route('dashboard.orders')
            ->with('success', 'تم تعديل الطلب بنجاح');
    }



    public function editOrder($order_id)
    {
        $order = Order::findOrFail($order_id);
        $shippingMethods = ShippingMethod::all();
        $products = Product::where('quantity', '>', 0)->get();
        return view('admin.order.edit', compact('order', 'shippingMethods', 'products'));
    }
    
    public function updateBook(Request $request,  $order_id)
    {
        $order = Order::findOrFail($order_id);
        if ($request->has('remove')) {
            $detailId = $request->input('remove');
            $order->orderDetails()->where('id', $detailId)->delete();
        }
        // 🔹 Update existing items
        if ($request->has('items')) {
            foreach ($request->items as $itemData) {
                $detail = $order->orderDetails()->where('id', $itemData['id'])->first();
                if ($detail) {
                    $detail->amout = $itemData['amount'];
                    $detail->total_price = $detail->price * $itemData['amount'];
                    $detail->save();
                }
            }
        }
           // 🔹 Add a new product
        if ($request->has('new_item') && !empty($request->new_item['product_id'])) {
            $productId = $request->new_item['product_id'];
            $amount = $request->new_item['amount'] ?? 1;

            $product = Product::find($productId);
            if ($product) {
                $order->orderDetails()->create([
                    'product_id' => $product->id,
                    'price' => $product->price, // assume product has price column
                    'amout' => $amount,
                    'total_price' => $product->price * $amount,
                ]);
            }
        }

        // 🔹 Recalculate order total
        $orderAmount = $order->orderDetails()->sum('total_price');

        $order->amount = $orderAmount;

        $order->total = $orderAmount + $order->delivery_fee;

        $order->save();

        return redirect()->back()->with('success', 'تم تحديث الطلب بنجاح ✨');
    }
    public function update_all_reversed_order(): \Illuminate\Http\RedirectResponse
    {
        $orders = Order::query()->where('status', '=', 'reserved')->get();
        foreach ($orders as $order) {
            $order->update(['status' => 'success']);
        }
        return redirect()->to(route('dashboard.orders'));
    }
}
