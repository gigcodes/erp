<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrapRequestHistory extends Model
{
    protected $table = 'scrap_request_histories';

    protected $fillable = ['scraper_id', 'date', 'start_time', 'end_time','request_sent','request_failed'];
}
