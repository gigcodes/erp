<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class StatusMappingHistory extends Model
{
    const STATUS_TYPE_PURCHASE = 'Purchase';

    const STATUS_TYPE_SHIPPING = 'Shipping';

    const STATUS_TYPE_RETURN_EXCHANGE = 'Return Exchange';

    public function statusMapping()
    {
        return $this->belongsTo(\App\StatusMapping::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
