<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesUser extends Model
{
    protected $table = "sales_user";

    protected $fillable = ['name','user_name','password','is_active','employ_code'];
}
