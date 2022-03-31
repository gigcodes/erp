<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnExchangeStatusLog extends Model
{
    /**
     * @var string
    
     * @SWG\Property(property="status_name",type="string")
     * @SWG\Property(property="message",type="string")

     */
    

    protected $fillable = [
		'return_exchanges_id',
        'status_name',
        'status',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
