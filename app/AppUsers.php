<?php

namespace App;
use Illuminate\Database\Eloquent\Model;


class AppUsers extends Model
{
    protected $table = 'app_users';

    protected $fillable = ['name','imei','mobile','pincode','krishimitra_id'];

}