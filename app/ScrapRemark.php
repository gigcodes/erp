<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Loggers\LogScraper;

class ScrapRemark extends Model
{
    protected $fillable = [
    'scrap_id', 'module_type', 'scraper_name', 'remark'
  ];

  public function scraps()
  {
  	return $this->belongsTo(LogScraper::class);
  }
}
