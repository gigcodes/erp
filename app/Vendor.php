<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{

    use SoftDeletes;

  protected $fillable = [
    'category_id', 'name', 'address', 'phone', 'default_phone', 'whatsapp_number', 'email', 'social_handle', 'website', 'login', 'password', 'gst', 'account_name', 'account_swift', 'account_iban'
  ];

  public function products()
  {
    return $this->hasMany('App\VendorProduct');
  }

  public function agents()
  {
    return $this->hasMany('App\Agent', 'model_id')->where('model_type', 'App\Vendor');
  }

  public function chat_messages()
  {
    return $this->hasMany('App\ChatMessage');
  }

  public function category()
  {
    return $this->belongsTo('App\VendorCategory');
  }

    public function payments()
    {
        return $this->hasMany(VendorPayment::class);
    }

    public function cashFlows()
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }
}
