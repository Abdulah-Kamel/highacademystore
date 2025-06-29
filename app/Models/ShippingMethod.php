<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class ShippingMethod extends Model
{
    // allow mass‐assignment of every column you created
    protected $fillable = [
        'name',
        'type',
        'government',
        'address',
        'phones',
        'fee',
    ];

    // cast JSON & decimal
    protected $casts = [
        'phones'     => 'array',
        'government' => 'integer',
        'fee'        => 'decimal:2',
    ];

    /**
     * Lookup the governorate JSON by ID
     */
    public function getGovernmentNameAttribute(): string
    {
        $all = json_decode(File::get(storage_path('cities/governorates.json')), true);
        $match = collect($all)->firstWhere('id', $this->government);
        return $match['governorate_name_ar'] ?? '—';
    }

    /**
     * (Optional) All orders that used this method
     */
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'shipping_method_id');
    }
}
