<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FaqController extends Controller
{
    public function index()
    {
        // Clean up duplicates on page load
        $this->cleanupDuplicateOrders();
        
        return view('admin.faq.index');
    }

    /**
     * Clean up duplicate display orders
     */
    private function cleanupDuplicateOrders()
    {
        $faqs = Faq::orderBy('id')->get();
        $usedOrders = [];
        $counter = 1;

        foreach ($faqs as $faq) {
            // If this order is already used or is 0, assign a new one
            if (in_array($faq->display_order, $usedOrders) || $faq->display_order == 0) {
                // Find next available order
                while (in_array($counter, $usedOrders)) {
                    $counter++;
                }
                $faq->update(['display_order' => $counter]);
                $usedOrders[] = $counter;
                $counter++;
            } else {
                $usedOrders[] = $faq->display_order;
            }
        }
    }

    public function create()
    {
        return view('admin.faq.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive'
        ], [
            'question.required' => 'السؤال مطلوب',
            'answer.required' => 'الإجابة مطلوبة',
            'display_order.integer' => 'الترتيب يجب أن يكون رقماً صحيحاً',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة يجب أن تكون نشط أو غير نشط'
        ]);

        $maxOrder = Faq::max('display_order') ?? 0;

        // Always default to max+1 if no valid number provided
        $order = $request->filled('display_order')
            ? (int) $request->display_order
            : ($maxOrder + 1);

        // Check if there's already an FAQ with this display_order
        $existingFaq = Faq::where('display_order', $order)->first();

        if ($existingFaq) {
            // Instead of updating existing, shift all FAQs with order >= $order by 1
            Faq::where('display_order', '>=', $order)->increment('display_order');
            
            // Create new FAQ with the desired order
            Faq::create([
                'question' => $request->question,
                'answer' => $request->answer,
                'display_order' => $order,
                'status' => $request->status
            ]);

            $message = 'تم إضافة السؤال والإجابة وإعادة ترتيب الأسئلة بنجاح';
        } else {
            // Create a new FAQ
            Faq::create([
                'question' => $request->question,
                'answer' => $request->answer,
                'display_order' => $order,
                'status' => $request->status
            ]);

            $message = 'تم إضافة السؤال والإجابة بنجاح';
        }

        return redirect()->route('dashboard.faqs')->with('success', $message);
    }

    public function show(Faq $faq)
    {
        return view('admin.faq.show', compact('faq'));
    }

    public function edit(Faq $faq)
    {
        return view('admin.faq.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive'
        ], [
            'question.required' => 'السؤال مطلوب',
            'answer.required' => 'الإجابة مطلوبة',
            'display_order.integer' => 'الترتيب يجب أن يكون رقماً صحيحاً',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة يجب أن تكون نشط أو غير نشط'
        ]);

        $newOrder = $request->filled('display_order') 
            ? (int) $request->display_order 
            : $faq->display_order;

        // Check if there's already another FAQ with this display_order (excluding current FAQ)
        $existingFaq = Faq::where('display_order', $newOrder)
                          ->where('id', '!=', $faq->id)
                          ->first();

        if ($existingFaq) {
            // Swap the display orders
            $currentOrder = $faq->display_order;
            
            // Temporarily set existing FAQ to a high number to avoid constraint issues
            $existingFaq->update(['display_order' => 999999]);
            
            // Update current FAQ with new order
            $faq->update([
                'question' => $request->question,
                'answer' => $request->answer,
                'display_order' => $newOrder,
                'status' => $request->status
            ]);
            
            // Set existing FAQ to current FAQ's old order
            $existingFaq->update(['display_order' => $currentOrder]);
            
            $message = 'تم تحديث السؤال والإجابة وتبديل الترتيب بنجاح';
        } else {
            // No conflict, just update normally
            $faq->update([
                'question' => $request->question,
                'answer' => $request->answer,
                'display_order' => $newOrder,
                'status' => $request->status
            ]);
            
            $message = 'تم تحديث السؤال والإجابة بنجاح';
        }

        return redirect()->route('dashboard.faqs')->with('success', $message);
    }

    public function destroy(Faq $faq)
    {
        try {
            $faq->delete();
            return response()->json([
                'success' => true,
                'message' => 'تم حذف السؤال والإجابة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف السؤال والإجابة'
            ], 500);
        }
    }

    public function datatable(Request $request)
    {
        $query = Faq::query()->orderBy('display_order');

        return DataTables::of($query)
            ->addColumn('question', fn($row) => '<strong>' . e($row->question) . '</strong>')
            ->addColumn('answer', function ($row) {
                return strlen($row->answer) > 100
                    ? e(mb_substr($row->answer, 0, 100)) . '...'
                    : e($row->answer);
            })
            ->addColumn('display_order', fn($row) => $row->display_order)
            ->addColumn(
                'status',
                fn($row) => $row->status === 'active'
                    ? '<span class="badge bg-success">نشط</span>'
                    : '<span class="badge bg-secondary">غير نشط</span>'
            )
            ->addColumn('actions', function ($row) {
                $editBtn = '<a href="' . route('dashboard.faqs.edit', $row->id) . '" class="btn btn-sm btn-primary mx-1">
                    <i class="fa fa-edit"></i> تعديل
                </a>';

                $deleteBtn = '<button class="btn btn-sm btn-danger mx-1" onclick="deleteFaq(' . $row->id . ')">
                    <i class="fa fa-trash"></i> حذف
                </button>';

                return '<div class="d-flex gap-1">' . $editBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['question', 'answer', 'status', 'actions'])
            ->make(true);
    }

    /**
     * Manual cleanup route for duplicates
     */
    public function cleanupDuplicates()
    {
        $this->cleanupDuplicateOrders();
        
        return redirect()->route('dashboard.faqs')->with('success', 'تم تنظيف الترتيبات المكررة بنجاح');
    }
}
