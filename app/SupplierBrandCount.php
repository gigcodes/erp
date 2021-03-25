<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use App\Category;
use App\Supplier;
use Illuminate\Database\Eloquent\Model;

class SupplierBrandCount extends Model
{
	        /**
     * @var string
      * @SWG\Property(property="supplier_id",type="integer")
      * @SWG\Property(property="brand_id",type="integer")
      * @SWG\Property(property="cnt",type="string")
      * @SWG\Property(property="url",type="string")
      * @SWG\Property(property="category_id",type="integer")
     */
    protected $fillable = [ 'supplier_id', 'brand_id', 'cnt','url','category_id'];

        public function supplier(){
            return $this->hasOne(Supplier::class,'id','supplier_id');
        }
        public function category(){
            return $this->hasOne(Category::class,'id','category_id');
        }
}
