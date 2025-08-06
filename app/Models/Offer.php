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
}
