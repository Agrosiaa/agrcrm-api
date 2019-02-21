<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkOrderStatusDetail extends Model
{
    protected $table = "work_order_status_details";

    protected $fillable = ['order_id','work_order_status_id','role_id'];

    public function orders()
    {
        return $this->belongsTo('App\Order','order_id');
    }
}
