<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $table = 'sellers';

    protected $fillable = ['address','commission','city_id','user_id','company','vat','cst','user_id','city_id','seeds_lic_number','seeds_exp_date','fertilizers_lic_number','fertilizers_exp_date',
        'pesticides_lic_number','pesticides_exp_date','others_lic_number','others_exp_date','created_at','updated_at',
        'pan_card','shop_act','vat_certificate','cancelled_cheque','seeds_licence','fertilizers_licence','others_licence','pesticides_licence','gstin','gstin_certificate','alternate_email'
        ];

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
    public function city(){
        return $this->hasOne('App\City','id');
    }
    public function bankDetails(){
        return $this->hasOne('App\BankDetails','seller_id');
    }
    public function product(){
        return $this->hasMany('App\Product','seller_id');
    }
    public function addresses(){
        return $this->hasMany('App\SellerAddress','seller_id');
    }
    public function order()
    {
        return $this->hasOne('App\Order','seller_id');
    }
    public function vendorLicenses()
    {
        return $this->hasMany('App\VendorLicenses','vendor_id');
    }
}
