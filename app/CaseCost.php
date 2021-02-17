<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CaseCost extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"case_id","billed_date","amount","paid_date","amount_paid","other"})
     */
    protected $fillable =['case_id','billed_date','amount','paid_date','amount_paid','other'];

    public function case()
    {
        return $this->belongsTo(LegalCase::class,'case_id');
    }
}
