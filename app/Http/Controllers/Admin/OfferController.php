<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OfferService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OfferController extends Controller
{
    protected $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    public function index()
    {
        return view('admin.offers.index');
    }

    public function datatable()
    {
        $offers = $this->offerService->getAllOffers();
        $offers = $offers->reverse();
        return DataTables::of($offers)
            ->addColumn('image', function ($row) {
                $imagePath = asset('storage/images/offers/' . $row->image);
                return '<img src="' . $imagePath . '" alt="offer-image" style="height:120px;width:150px" class="avatar rounded me-2">';
            })
            ->addColumn('discount_details', function ($row) {
                return $row->type === 'percentage' 
                    ? "{$row->value}% Discount on {$row->minimum_books}+ Books" 
                    : "{$row->value} EGP Discount on {$row->minimum_books}+ Books";
            })
            ->addColumn('operation', function ($row) {
                return '<a href="' . route('dashboard.offers.edit', $row->id) . '" class="btn btn-success btn-sm">Edit</a>
                        <button type="button" class="btn btn-danger btn-sm delete_btn" data-id="' . $row->id . '">Delete</button>';
            })
            ->rawColumns(['image', 'operation'])
            ->toJson();
    }

    public function create()
    {
        return view('admin.offers.create');
    }

    public function store(Request $request)
    {
        try {
            // Validate request
            $data = $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'type' => 'required|in:percentage,fixed',
                'value' => 'required|numeric|min:0',
                'minimum_books' => 'required|integer|min:1'
            ]);

            // Store offer
            $this->offerService->storeOffer($request);

            return response()->json(['message' => 'Offer created successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function edit($id)
    {
        return view('admin.offers.edit', ['offer' => $this->offerService->getOfferById($id)]);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate request
            $data = $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'type' => 'required|in:percentage,fixed',
                'value' => 'required|numeric|min:0',
                'minimum_books' => 'required|integer|min:1'
            ]);

            // Update offer
            $this->offerService->updateOffer($request, $id);

            return response()->json(['message' => 'Offer updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $id = $request->id;
            $this->offerService->deleteOffer($id);
            return response()->json(['success' => 'Offer deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
