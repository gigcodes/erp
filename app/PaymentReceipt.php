<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
  protected $fillable = ['date','worked_minutes','payment','status','task_id','developer_task_id','user_id','rate_estimated','remarks','currency'];
    public function user() {
      return  $this->belongsTo('App\User');
    }
}
