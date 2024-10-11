<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = ['user_id','date','status','is_paid','code','total','cash_number','image','instapay'];

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
        return asset('storage/images/screens/'. $this->image);
    }

}
