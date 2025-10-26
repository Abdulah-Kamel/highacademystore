<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'governorate_id',
        'name_ar',
        'name_en',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    /**
     * Get the governorate that owns the city
     */
    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    /**
     * Scope for active cities
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for cities in active governorates
     */
    public function scopeWithActiveGovernorate($query)
    {
        return $query->whereHas('governorate', function ($q) {
            $q->where('status', true);
        });
    }

    /**
     * Get the display name based on current locale
     */
    public function getDisplayNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }
}
