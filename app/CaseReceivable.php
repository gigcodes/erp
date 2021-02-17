<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CaseReceivable extends Model
{
    use SoftDeletes;
    /**
     * @var string
     * @SWG\Property(enum={"case_id","currency","receivable_date","received_date","receivable_amount","received_amount","description","other","status","user_id","updated_by"})
     */
    protected  $fillable = ['case_id','currency','receivable_date','received_date','receivable_amount','received_amount','description','other','status','user_id','updated_by'];
    /**
     * @var string
     * @SWG\Property(enum={"model_id", "model_type", "name", "phone", "whatsapp_number", "address", "email"})
     */
    protected  $dates = ['deleted_at'];

    public function case()
    {
        return $this->belongsTo(LegalCase::class,'case_id');
    }
}
