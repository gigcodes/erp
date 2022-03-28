<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadProductPriceCountLogs extends Model
{
       /**
     * @var string
     * @SWG\Property(property="id",type="integer")
     * @SWG\Property(property="order_id",type="integer")
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="stage",type="string")
     * @SWG\Property(property="log",type="longText")
     * @SWG\Property(property="created_by",type="datetime")
     * @SWG\Property(property="product_id",type="interger")
     */
	public $table  = "lead_product_price_count_logs";
    protected $fillable = ['id', 'order_id','product_id', 'customer_id', 'log', 'original_price', 'promotion_per', 'promotion', 'segment_discount', 'segment_discount_per', 'total_price', 'created_at', 'updated_at'];

    public function product()
    {
    	return $this->hasOne("\App\Products","id","product_id");
    }

}
