<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;

    protected $fillable = [
        'governorate_name_ar',
        'governorate_name_en',
        'price',
        'home_shipping_price',
        'post_shipping_price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'home_shipping_price' => 'decimal:2',
        'post_shipping_price' => 'decimal:2',
    ];

    public $timestamps = false;

    /**
     * Get all cities for this governorate
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    /**
     * Get the display name based on current locale
     */
    public function getDisplayNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->governorate_name_ar : $this->governorate_name_en;
    }

    /**
     * Accessor for name_ar to maintain compatibility
     */
    public function getNameArAttribute()
    {
        return $this->governorate_name_ar;
    }

    /**
     * Accessor for name_en to maintain compatibility
     */
    public function getNameEnAttribute()
    {
        return $this->governorate_name_en;
    }

    /**
     * Accessor for shipping_price to maintain compatibility
     */
    public function getShippingPriceAttribute()
    {
        return $this->home_shipping_price ?? $this->price;
    }

    /**
     * Convenience accessor to always return a numeric home cost.
     */
    public function getHomeCostAttribute()
    {
        if ($this->home_shipping_price !== null) {
            return $this->home_shipping_price;
        }

        return $this->price ?? 0;
    }

    /**
     * Convenience accessor to always return a numeric post-office cost.
     */
    public function getPostCostAttribute()
    {
        if ($this->post_shipping_price !== null) {
            return $this->post_shipping_price;
        }

        return $this->price ?? 0;
    }

    /**
     * Scope to only include active governorates
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
