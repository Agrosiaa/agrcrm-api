<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $sluggable = [
        'build_from' => 'name',
        'save_to'    => 'slug',
    ];
}
