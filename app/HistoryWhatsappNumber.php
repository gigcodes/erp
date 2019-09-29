<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryWhatsappNumber extends Model
{
    public $timestamps = false;
    public $table = "history_whatsapp_number";
    //
    protected $fillable = ['date_time', 'object', 'object_id', 'old_number', 'new_number'];
}
