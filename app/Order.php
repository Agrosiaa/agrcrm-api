<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'base_price','discount','discounted_price','quantity','consignment_number','packing_slip_number',
        'customer_id','order_status_id','customer_ship_to_id','customer_bill_to_id','product_id','cart_id',
        'shipping_method_id','delivery_type_id','payment_method_id','created_at','updated_at','order_customer_info_id',
        'cart_items','seller_id','tax_rate','tax_information','seller_address','delivery_date','sla_bridge',
        'payment_gateway_data','dispatch_date','pick_up_date','subtotal','selling_price','hsn_code_tax_relation_id','is_web_order',
        'length','width','is_configurable','krishimitra_id','weight_logistic','article_number','consignment_date','commission_percent',
        'logistic_percent','is_ps_campaign','agrosiaa_campaign_charges','vendor_campaign_charges','sales_id','referral_code',
        'agrosiaa_discount','delivery_amount','special_note'
    ];

    public function product()
    {
        return $this->belongsTo('App\Product','product_id');
    }
    public function customer()
    {
        return $this->belongsTo('App\Customer','customer_id');
    }
    public function seller()
    {
        return $this->belongsTo('App\Seller','seller_id');
    }
    public function cart()
    {
        return $this->belongsTo('App\Cart','cart_id');
    }
    public function shippingMethod()
    {
        return $this->belongsTo('App\ShippingMethod','shipping_method_id');
    }
    public function DeliveryMethod()
    {
        return $this->belongsTo('App\DeliveryType','delivery_type_id');
    }
    public function PaymentMethod()
    {
        return $this->belongsTo('App\PaymentMethod','payment_method_id');
    }
    public function shippingAddress()
    {
        return $this->belongsTo('App\CustomerAddress','customer_ship_to_id');
    }
    public function billingAddress()
    {
        return $this->belongsTo('App\CustomerAddress','customer_bill_to_id');
    }
    public function orderStatus()
    {
        return $this->belongsTo('App\OrderStatus','order_status_id');
    }
    public function orderHistory()
    {
        return $this->hasMany('App\OrderHistory','order_id');
    }
    public function ordersCustomerInfo()
    {
        return $this->belongsTo('App\OrdersCustomerInfo','order_customer_info_id');
    }
    public function invoice()
    {
        return $this->hasOne('App\Invoice','order_id');
    }
}
