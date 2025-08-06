<?php

namespace App\Services;

use App\Models\Offer;
use Illuminate\Support\Facades\Storage;

class OfferService
{
    public function getAllOffers()
    {
        return Offer::all();
    }

    public function getOfferById($id)
    {
        return Offer::findOrFail($id);
    }

    public function storeOffer($request)
    {
        $data = [];
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $destinationPath = public_path('storage/images/offers');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $filename);

            $data['image'] = $filename;
        }

        return Offer::create($data);
    }

    public function updateOffer($request, $id)
    {
        $offer = Offer::findOrFail($id);
        $data = [];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($offer->image) {
                Storage::delete('public/images/offers/' . $offer->image);
            }
            
            $image = $request->file('image');
            $destinationPath = public_path('storage/images/offers');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $filename);
            $data['image'] = $filename;
        }

        $offer->update($data);
        return $offer;
    }

    public function deleteOffer($id)
    {
        $offer = Offer::findOrFail($id);

        // Delete image from storage
        if ($offer->image && Storage::exists('public/images/offers/' . $offer->image)) {
            Storage::delete('public/images/offers/' . $offer->image);
        }

        return $offer->delete();
    }
}
