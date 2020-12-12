<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Krishimitra extends Model
{
    protected $table = 'krishimitra';

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
}
