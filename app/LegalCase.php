<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalCase extends Model
{
    use SoftDeletes;
    protected $table = 'cases';
    protected $fillable = ['lawyer_id', 'case_number', 'for_against', 'court_detail', 'phone','default_phone', 'whatsapp_number', 'status', 'resource', 'last_date', 'next_date', 'cost_per_hearing', 'remarks', 'other'];
    protected $dates = ['deleted_at'];

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class,'lawyer_id');
    }

    public function chat_message()
    {
        return $this->hasMany(ChatMessage::class,'case_id');
    }

    public function costs()
    {
        return $this->hasMany(CaseCost::class,'case_id');
    }

    public function cashFlows()
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }

    public function receivables()
    {
        return $this->hasMany(CaseReceivable::class,'case_id');
    }
}
