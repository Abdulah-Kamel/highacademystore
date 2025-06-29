<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Accessor for image URL
    public function getImagePathAttribute()
    {
        return $this->image ? ('storage/images/offers/' . $this->image) : null;
    }

    /**
     * Determine if an offer applies to the given cart quantity.
     */
    public function appliesToCart($cartQuantity)
    {
        return $cartQuantity >= $this->minimum_books;
    }

    /**
     * Calculate the discount amount based on the cart total.
     */
    public function calculateDiscount($cartTotal)
    {
        if ($this->type === 'percentage') {
            return ($this->value / 100) * $cartTotal;
        } elseif ($this->type === 'fixed') {
            return min($this->value, $cartTotal); // Ensure we don't discount more than the total price
        }
        return 0;
    }
}
