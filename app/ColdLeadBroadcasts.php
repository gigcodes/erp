<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColdLeadBroadcasts extends Model
{
    public function lead() {
        return $this->belongsToMany(ColdLeads::class, 'lead_broadcasts_lead', 'lead_broadcast_id', 'lead_id', 'id', 'id');
    }
}
