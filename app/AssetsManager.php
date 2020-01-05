<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AssetsCategory;

class AssetsManager extends Model
{

  //use SoftDeletes;
  protected $table = 'assets_manager';

  protected $casts = [
      'notes' => 'array'
  ];

  protected $fillable = [
    'name', 'asset_type', 'category_id', 'purchase_type', 'payment_cycle', 'amount', 'archived','password','provider_name','location','currency'];

  public function category()
    {
        return $this->hasOne(AssetsCategory::class,'id','category_id');
    }
  
}
