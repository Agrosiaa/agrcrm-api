<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $table = 'customer_addresses';

    protected $fillable = [
        'at_post','taluka','district','state','pincode','is_default',
        'full_name','mobile','flat_door_block_house_no','name_of_premise_building_village',
        'area_locality_wadi','road_street_lane','customer_id'
    ];


    public function customer(){
        return $this->belongsTo('App\Customer','customer_id');
    }
}