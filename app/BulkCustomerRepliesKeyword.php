<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulkCustomerRepliesKeyword extends Model
{
    public function customers() {
        return $this->belongsToMany(Customer::class, 'bulk_customer_replies_keyword_customer', 'keyword_id', 'customer_id', 'id', 'id');
    }
}
