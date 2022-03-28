<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use SoftDeletes;

    public function Order(){
        return $this->belongsTo(Order::class,'db_order_id','id');
    }
}
