<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'name',
        'mobile',
        'address',
        'address2',
        'near_post',
        'date',
        'status',
        'is_paid',
        'code',
        'total',
        'cash_number',
        'image',
        'instapay',
        'temp_mobile',
        'tracker',
        'shipping_method_id',
        'shipping_name',
        'shipping_address',
        'method',
        'amount',
        'delivery_fee'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }


    public function getImagePathAttribute()
    {
        return asset('storage/images/screens/' . $this->image);
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method');
    }
}
