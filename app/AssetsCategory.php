<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetsCategory extends Model
{

  //use SoftDeletes;
  protected $table = 'assets_category';

 

  protected $fillable = [
    'cat_name'];


  /*public function Assetscat()
  {
    return $this->hasMany('App\AssetsManager', 'category_id')->where('id', 'App\AssetsCategory');
  }*/
}
