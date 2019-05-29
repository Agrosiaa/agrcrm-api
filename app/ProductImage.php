<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';

    protected $fillable = ['name','position','product_id','created_at','updated_id','alternate_text'];

    public function product(){
        return $this->belongsTo('App\Product','product_id');
    }
}
