<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes;

    protected $table = 'cart';

    protected $fillable = [
        'sku','product_name','category_name','image_url','unit_price',
        'base_price','discounted_price','discount','quantity','product_id',
        'created_at','updated_at','delivery_price','delivery_type_id',
        'payment_method_id','customer_id','customer_address_id','deleted_at',
        'seller_id','return_period','is_delete_backend','is_configurable','length','width'
    ];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

}
