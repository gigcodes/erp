<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Agent extends Model
{
  /**
     * @var string
     * @SWG\Property(enum={"model_id", "model_type", "name", "phone", "whatsapp_number", "address", "email"})
     */
  protected $fillable = [
    'model_id', 'model_type', 'name', 'phone', 'whatsapp_number', 'address', 'email'
  ];

  public function purchase()
  {
    return $this->hasOne('App\Purchase');
  }

  public function supplier()
  {
    return $this->hasOne('App\Supplier', 'model_id')->where('model_type', 'App\Supplier');
  }
}
