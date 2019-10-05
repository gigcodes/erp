<?php

namespace App;

use App\Category;
use App\Supplier;
use Illuminate\Database\Eloquent\Model;


class SupplierCategoryCount extends Model
{
    protected $fillable = [ 'supplier_id', 'category_id', 'cnt'];

    public function supplier(){
        return $this->hasOne(Supplier::class,'id','supplier_id');
    }
    public function category(){
        return $this->hasOne(Category::class,'id','category_id');
    }
}
