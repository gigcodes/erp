<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstagramAutomatedMessages extends Model
{
    public function account() {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function target() {
        return $this->belongsTo(Influencers::class, 'target_id', 'id');
    }
}
