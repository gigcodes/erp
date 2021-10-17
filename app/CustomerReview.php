<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CustomerReview extends Model
{
    /**
     * @var string
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="amount",type="string")
     * @SWG\Property(property="stars",type="string")
     * @SWG\Property(property="comment",type="string")
     * @SWG\Property(property="status",type="integer")
     */

    use SoftDeletes;
    use Mediable;
    protected $fillable = [
        'customer_id', 'amount', 'stars', 'comment', 'status',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }
}
