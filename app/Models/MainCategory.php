<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon_image']; // ❌ Removed 'icon'

    // ✅ Accessor to get the full image URL if icon_image exists
    public function getIconImageUrlAttribute()
    {
        return $this->icon_image ? asset('storage/' . $this->icon_image) : null;
    }
}
