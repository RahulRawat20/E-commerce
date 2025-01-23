<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    public function user(){
        return $this->belongsTo('User::class');
    }

    public function orderItems(){
        return $this->hasMany('orderItem::class');
    }
    public function transaction(){
        return $this->hasOne('orderItem::class');
    }
}
