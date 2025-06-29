<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class ShippingMethodController extends Controller
{
    public function index()
    {
        return view('admin.shipping.index');
    }

    public function datatable(Request $request)
    {
        return DataTables::of(ShippingMethod::query())
            // governorate name lookup
            ->addColumn('gov_name', function ($m) {
                $all = json_decode(File::get(storage_path('cities/governorates.json')), true);
                $g   = collect($all)->firstWhere('id', $m->government);
                return $g['governorate_name_ar'] ?? '—';
            })
            // human-read type
            ->addColumn('type_label', function ($m) {
                return match ($m->type) {
                    'post'   => 'مكتب بريد',
                    'home'   => 'توصيل لباب البيت',
                    'branch' => 'استلام من المكتبة',
                    default  => $m->type,
                };
            })
            // comma-separated phones
            ->addColumn('phones_list', fn($m) => implode(' / ', $m->phones ?? []))
            ->addColumn('fee', fn($m) => $m->fee . ' جنيه')
            ->addColumn('actions', function ($m) {
                return "
                  <a href=\"" . route('dashboard.shipping-methods.edit', $m) . "\" class=\"btn btn-sm btn-primary\">تعديل</a>
                  <button onclick=\"deleteShippingMethod({$m->id})\" class=\"btn btn-sm btn-danger\">حذف</button>
                ";
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create()
    {
        $govs = json_decode(File::get(storage_path('cities/governorates.json')), true);
        return view('admin.shipping.create', compact('govs'));
    }

    public function store(Request $req)
    {
        // if they sent phones as comma-separated string
        if (is_string($req->phones)) {
            $req->merge(['phones' => array_filter(array_map('trim', explode(',', $req->phones)))]);
        }

        $data = $req->validate([
            'name'       => 'required|string|max:191',
            'type'       => 'required|in:post,home,branch',
            'government' => 'nullable|integer',
            'address'    => 'nullable|string|max:191',
            'phones'     => 'required|array|min:1',
            'phones.*'   => 'required|string',
            'fee'        => 'required|numeric|min:0',  // ← new rule
        ]);

        ShippingMethod::create($data);

        return redirect()->route('dashboard.shipping-methods')
            ->with('success', 'تم إنشاء طريقة الشحن بنجاح');
    }

    public function edit(ShippingMethod $shippingMethod)
    {
        $govs = json_decode(File::get(storage_path('cities/governorates.json')), true);
        return view('admin.shipping.edit', compact('shippingMethod', 'govs'));
    }

    public function update(Request $req, ShippingMethod $shippingMethod)
    {
        if (is_string($req->phones)) {
            $req->merge(['phones' => array_filter(array_map('trim', explode(',', $req->phones)))]);
        }

        $data = $req->validate([
            'name'       => 'required|string|max:191',
            'type'       => 'required|in:post,home,branch',
            'government' => 'nullable|integer',
            'address'    => 'nullable|string|max:191',
            'phones'     => 'required|array|min:1',
            'phones.*'   => 'required|string',
            'fee'        => 'required|numeric|min:0',  // ← new rule
        ]);

        $shippingMethod->update($data);

        return redirect()->route('dashboard.shipping-methods')->with('success', 'تم تحديث طريقة الشحن بنجاح');
    }

    public function destroy(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();
        return response()->json(['success' => true]);
    }
}
