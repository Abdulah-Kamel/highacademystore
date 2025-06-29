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
        // Validate request
        $data = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_books' => 'required|integer|min:1'
        ]);

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

        // Validate request
        $data = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_books' => 'required|integer|min:1'
        ]);

        // Handle image update

        if ($request->hasFile('image')) {
            // Delete old image
            if ($offer->image) {
                Storage::delete('public/images/offers/' . $offer->image);
                $image = $request->file('image');
                $destinationPath = public_path('storage/images/offers');
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $filename);
                $data['image'] = $filename;
            }
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

    /**
     * Apply Offer Discount Based on Cart Items
     */
    public function applyOfferDiscount($cartItems)
    {
        $totalItems = count($cartItems);
        $offer = Offer::where('minimum_books', '<=', $totalItems)
            ->orderByDesc('minimum_books') // Get the best applicable offer
            ->first();

        if ($offer) {
            $cartTotal = collect($cartItems)->sum('price');

            if ($offer->type === 'percentage') {
                $discount = ($cartTotal * $offer->value) / 100;
            } else {
                $discount = $offer->value;
            }

            return ['discount' => $discount, 'offer' => $offer];
        }

        return ['discount' => 0, 'offer' => null];
    }
}
