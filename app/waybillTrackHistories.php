<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class waybillTrackHistories extends Model
{
    protected $table = 'waybill_track_histories';

    protected $fillable = ['waybill_id','comment','dat','order_status_id'];

    public function waybill(){
    	return $this->belongsto('App\Waybill');
    }
}
