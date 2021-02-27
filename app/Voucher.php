<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
  protected $fillable = [
    'user_id', 'delivery_approval_id', 'category_id', 'description', 'travel_type', 'amount', 'paid', 'date','reject_reason','resubmit_count','reject_count'  ];

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function category()
  {
    return $this->belongsTo('App\VoucherCategory');
  }

    public function chat_message()
    {
        return $this->hasMany(ChatMessage::class,'voucher_id');
    }

    public function cashFlows()
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }
}
