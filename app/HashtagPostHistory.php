<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HashtagPostHistory extends Model
{
    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function message() {
        return $this->belongsTo(InstagramAutomatedMessages::class, 'instagram_automated_message_id', 'id');
    }
}
