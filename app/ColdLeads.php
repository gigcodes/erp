<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColdLeads extends Model
{
    public function threads() {
        return $this->hasOne(InstagramThread::class, 'cold_lead_id', 'id');
    }

    public function account() {
        return $this->belongsTo(Account::class);
    }
}
