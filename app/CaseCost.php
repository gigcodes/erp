<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CaseCost extends Model
{
    protected $fillable =['case_id','billed_date','amount','paid_date','amount_paid','other'];

    public function case()
    {
        return $this->belongsTo(LegalCase::class,'case_id');
    }
}
