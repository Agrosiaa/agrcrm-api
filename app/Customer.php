<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'user_id','is_web','created_at','updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function addresses()
    {
        return $this->hasMany('App\CustomerAddress','customer_id');
    }
}