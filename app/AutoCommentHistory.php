<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutoCommentHistory extends Model
{
    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function hashtag() {
        return $this->belongsTo(AutoReplyHashtags::class, 'auto_reply_hashtag_id', 'id');
    }
}
