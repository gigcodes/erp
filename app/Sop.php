<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use App\PurchaseProductOrderLog;

class Sop extends Model
{
    protected $table ="sops";
     protected $fillable = ['name','content'];
 
    public function purchaseProductOrderLogs(){
        return $this->hasOne(PurchaseProductOrderLog::class, 'purchase_product_order_id', 'id');
    }
 
    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function emails()
    {
        return $this->hasMany('App\Email', 'model_id', 'id');
    }
}
