<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::all();
        return view('user.index', compact('offers'));
    }
}
