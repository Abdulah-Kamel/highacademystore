<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OfferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        try {
            // Log the incoming request
            Log::info('=== DATATABLE REQUEST ===');
            
            // Get the draw parameter (required by DataTables)
            $draw = request()->input('draw', 1);
            
            // Get all offers
            $offers = $this->offerService->getAllOffers();
            
            // Log the count of offers
            Log::info('Number of offers found: ' . $offers->count());
            
            // Prepare the data array for DataTables
            $data = [];
            
            foreach ($offers as $offer) {
                // Create the image URL
                $imageUrl = $offer->image ? asset('storage/images/offers/' . $offer->image) : null;
                
                // Create the image HTML
                $imageHtml = $imageUrl 
                    ? '<img src="' . $imageUrl . '" alt="offer-image" style="height:120px;width:150px" class="avatar rounded me-2">' 
                    : 'No Image';
                
                // Create the action buttons
                $actionButtons = 
                    '<a href="' . route('dashboard.offers.edit', $offer->id) . '" class="btn btn-success btn-sm">Edit</a> ' .
                    '<button type="button" class="btn btn-danger btn-sm delete_btn" data-id="' . $offer->id . '">Delete</button>';
                
                // Add the offer data to the data array
                $data[] = [
                    'id' => $offer->id,
                    'image' => $imageHtml,
                    'operation' => $actionButtons
                ];
            }
            
            // Prepare the response
            $response = [
                'draw' => (int) $draw,
                'recordsTotal' => $offers->count(),
                'recordsFiltered' => $offers->count(),
                'data' => $data
            ];
            
            Log::info('Sending DataTables response', [
                'draw' => $draw,
                'records' => $offers->count(),
                'data_count' => count($data)
            ]);
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            $authUser = auth('admin')->user();
            $errorData = [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => request()->all(),
                'auth_user' => $authUser ? [
                    'id' => $authUser->id,
                    'name' => $authUser->name,
                    'email' => $authUser->email,
                    // Add other relevant user fields as needed
                ] : null
            ];
            
            Log::error('DATATABLE ERROR:', $errorData);
            
            return response()->json([
                'draw' => (int) request()->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while loading the data.',
                'debug' => config('app.debug') ? $errorData : null
            ], 500);
        }
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
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            // Store offer
            $this->offerService->storeOffer($request);

            return response()->json(['message' => 'تم إضافة العرض بنجاح'], 200);
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
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            // Update offer
            $this->offerService->updateOffer($request, $id);

            return response()->json(['message' => 'تم تحديث العرض بنجاح'], 200);
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
