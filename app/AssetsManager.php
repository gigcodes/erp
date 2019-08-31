<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetsManager extends Model
{

  //use SoftDeletes;
  protected $table = 'assets_manager';

  protected $casts = [
      'notes' => 'array'
  ];

  protected $fillable = [
    'name', 'asset_type', 'category_id', 'purchase_type', 'payment_cycle', 'amount', 'archived'];

  /*public function category()
    {
        return $this->belongsTo('App\AssetsCategory', 'assets_manager','category_id', 'id' );
    }
  */
}
