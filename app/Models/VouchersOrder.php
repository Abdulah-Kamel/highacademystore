<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VouchersOrder extends Model
{
    use HasFactory;
    protected $fillable=[
        "coupon_id",
        "user_id",
        "method",
        "ref_code",
        "quantity",
        "state",
        "image",
        "account"
        ];
}
