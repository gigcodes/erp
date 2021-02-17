<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BloggerPayment extends Model
{
    use SoftDeletes;
    /**
     * @var string
     * @SWG\Property(enum={"blogger_id", "payment_date", "paid_date", "payable_amount", "paid_amount", "description", "other", "status", "user_id", "updated_by", "currency"})
     */
    protected $fillable = ['blogger_id', 'payment_date', 'paid_date', 'payable_amount', 'paid_amount', 'description', 'other', 'status', 'user_id', 'updated_by', 'currency'];
    /**
     * @var string
     * @SWG\Property(enum={"deleted_at"})
     */
    protected $dates = ['deleted_at'];

    public function blogger()
    {
        return $this->belongsTo(Blogger::class);
    }
}
