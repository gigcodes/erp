<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;

class PaymentReceipt extends Model
{
	 /**
     * @var string
     * @SWG\Property(property="date",type="datetime")
     * @SWG\Property(property="payment_method_id",type="integer")
     * @SWG\Property(property="worked_minutes",type="integer")
     * @SWG\Property(property="payment",type="float")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="task_id",type="interger")
     * @SWG\Property(property="developer_task_id",type="interger")
     * @SWG\Property(property="user_id",type="interger")
     * @SWG\Property(property="rate_estimated",type="string")
     * @SWG\Property(property="remarks",type="string")
     * @SWG\Property(property="currency",type="string")
          * @SWG\Property(property="billing_start_date",type="datetime")
     * @SWG\Property(property="billing_end_date",type="datetime")
   
     */
	use Mediable;
  protected $fillable = ['date','worked_minutes','payment','status','task_id','developer_task_id','user_id','rate_estimated','remarks','currency','billing_start_date','billing_end_date'];
  
    public function user() {
      return  $this->belongsTo('App\User');
    }
}
