<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuggestedProductList extends Model
{
    protected $fillable = ['customer_id','product_id','chat_message_id','remove_attachment','date'];
}
