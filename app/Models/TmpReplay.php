<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TmpReplay extends Model
{
    use HasFactory;
    protected $table = 'tmp_replies';

    protected $fillable = [
        'chat_message_id',
        'suggested_replay',
        'is_approved',
        'is_rejected',
        'type',
    ];
}
