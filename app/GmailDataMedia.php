<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GmailDataMedia extends Model
{
    protected $fillable = [
        'gmail_data_id',
        'images',
    ];

    public function gmailDataList()
    {
        return $this->belongsTo(GmailDataList::class);
    }
}
