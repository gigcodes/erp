<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseReceivable extends Model
{
    use SoftDeletes;
    protected  $fillable = ['case_id','currency','receivable_date','received_date','receivable_amount','received_amount','description','other','status','user_id','updated_by'];
    protected  $dates = ['deleted_at'];

    public function case()
    {
        return $this->belongsTo(LegalCase::class,'case_id');
    }
}
