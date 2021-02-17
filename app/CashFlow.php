<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CashFlow extends Model
{
  /**
     * @var string
     * @SWG\Property(enum={"user_id", "cash_flow_category_id", "description", "date", "amount", "type","expected","actual","currency","status","order_status","updated_by","cash_flow_able_id","cash_flow_able_type"})
     */
  protected $fillable = [
    'user_id', 'cash_flow_category_id', 'description', 'date', 'amount', 'type','expected','actual','currency','status','order_status','updated_by','cash_flow_able_id','cash_flow_able_type'
  ];

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function files()
  {
    return $this->hasMany('App\File', 'model_id')->where('model_type', 'App\CashFlow');
  }

    public function cashFlowAble()
    {
        return $this->morphTo()->withTrashed();
  }

    public function getModelNameAttribute()
    {
        
  }
}
