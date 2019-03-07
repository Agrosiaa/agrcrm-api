<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = "chat";

    protected $fillable = ['order_id','message','sales_id','work_order_status_id'];
}
