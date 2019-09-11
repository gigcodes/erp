<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Loggers\LogScraper;

class ScrapRemark extends Model
{
    protected $fillable = [
    'user_name', 'scraper_name', 'remark'
  ];

  public function scraps()
  {
  	return $this->belongsTo(LogScraper::class);
  }
}
