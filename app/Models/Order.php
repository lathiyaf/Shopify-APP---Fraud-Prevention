<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    public function OrderDetails(){
        return $this->hasOne(OrderDetail::class,'db_order_id','id');
    }
}
