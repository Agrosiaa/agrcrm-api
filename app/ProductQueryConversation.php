<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductQueryConversation extends Model
{
    protected $table = 'product_query_conversation';
    protected $fillable = ['conversation','product_id','from_id','created_at','updated_id'];
}
