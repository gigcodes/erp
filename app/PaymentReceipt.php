<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;

class PaymentReceipt extends Model
{
	use Mediable;
  protected $fillable = ['date','worked_minutes','payment','status','task_id','developer_task_id','user_id','rate_estimated','remarks','currency','billing_start_date','billing_end_date'];
  
    public function user() {
      return  $this->belongsTo('App\User');
    }
}
