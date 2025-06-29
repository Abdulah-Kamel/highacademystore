<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'orders_details';

    protected $fillable = ['order_id','product_id','amout','price','total_price','size','color'];

    public function orders()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
