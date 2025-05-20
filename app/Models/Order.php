<?php

namespace App\Models;

use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    // relation with user model
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function items(){
        return $this->hasMany(OrderItem::class);
    }

    public static function generateOrderNumber(){
        $year = date('Y');
        $randomNumber = strtoupper(substr(uniqid(),-6));
        return "ORD-{$year}-{$randomNumber}";
    }
}
