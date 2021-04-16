<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Agronomy extends Model
{
    protected $table = "agronomy";
    protected $fillable = ['crop_name','slug','cover_image','crop_image','is_active','crop_name_mr','created_at','updated_at'];

    public function agronomyInfo()
    {
        return $this->hasMany('App\AgronomyInfo','agronomy_id');
    }
}