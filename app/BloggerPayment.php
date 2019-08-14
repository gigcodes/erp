<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloggerPayment extends Model
{
    use SoftDeletes;
    protected $fillable = ['blogger_id', 'payment_date', 'paid_date', 'payable_amount', 'paid_amount', 'description', 'other', 'status', 'user_id', 'updated_by', 'currency'];
    protected $dates = ['deleted_at'];

    public function blogger()
    {
        return $this->belongsTo(Blogger::class);
    }
}
