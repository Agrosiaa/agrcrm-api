<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    protected $table = 'order_history';

    protected $fillable = ['is_email_sent','order_id','order_status_id','user_id','customer_cancel_reasons_id','created_at','updated_at','reason','comment','work_order_status_id','sales_id'];
    public function cancel()
    {
        return $this->belongsTo('App\CancelReason','customer_cancel_reasons_id');
    }
}
