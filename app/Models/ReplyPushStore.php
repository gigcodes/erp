<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplyPushStore extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        "reply_id", "store_id", "platform_id",
    ];
}
