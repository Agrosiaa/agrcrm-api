<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostOffice extends Model
{
    protected $table = 'post_office';

    protected $fillable =['office_name','pincode','taluka','district','state'];

    public function scopeWithoutTimestamps()
    {
        $this->timestamps = false;
        return $this;
    }
}
