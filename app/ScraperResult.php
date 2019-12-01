<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScraperResult extends Model
{
    protected $fillable = ['date','scraper_name','total_urls','existing_urls','new_urls'];

}
