<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstagramThread extends Model
{
    public function conversation() {
        return $this->hasMany(InstagramDirectMessages::class, 'instagram_thread_id', 'id');
    }

    public function lead() {
        return $this->belongsTo(ColdLeads::class, 'cold_lead_id', 'id');
    }
}
