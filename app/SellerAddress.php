<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerAddress extends Model
{
    protected $table = 'seller_addresses';

    protected $fillable = [
        'address_unique_name','at_post','taluka','district','state','pincode','seller_id','created_at','updated_at','shop_no_office_no_survey_no','name_of_premise_building_village','area_locality_wadi','road_street_lane'
    ];


    public function seller(){
        return $this->belongsTo('App\Seller','seller_id');
    }
}
