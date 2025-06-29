<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    // Specify the table name if it doesn't follow Laravel's plural naming convention
    protected $table = 'discount';

    // The attributes that are mass assignable.
    protected $fillable = [
        'code',
        'discount',
        'usage_limit',
        'used',
    ];
}
