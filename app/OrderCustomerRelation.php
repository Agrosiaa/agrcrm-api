<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderCustomerRelation extends Model
{
    protected $table = 'order_customer_info';

    protected $fillable = [
        'id','billing_address','shipping_address','created_at',
        'updated_at',
    ];
}
