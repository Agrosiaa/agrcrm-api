<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brands';

    protected $fillable = ['name','created_at','updated_at','name_mr'];

    public function brandCategory()
    {
        return $this->hasMany('App\BrandCategory','brand_id');
    }

    public function product()
    {
        return $this->hasMany('App\Product','brand_id');
    }

}
