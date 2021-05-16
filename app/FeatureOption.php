<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeatureOption extends Model
{
    protected $table = 'feature_options';

    protected $fillable = [
        'name','feature_id','added_by','created_at','updated_at'
    ];

    public function feature()
    {
        return $this->belongsTo('App\Feature','id');
    }
}
