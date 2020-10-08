<?php

namespace App;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;

class EmailAddress extends Model
{
  
  protected $fillable = [
    'from_name',
    'from_address',
    'driver',
    'host',
    'port',
    'encryption',
    'username',
    'password',
    'store_website_id',
  ];
  
   public function website()
    {
       return $this->hasOne(StoreWebsite::class,'id','store_website_id');
    }


}
