<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrapRemark extends Model
{
    protected $fillable = [
    'scrap_id', 'module_type', 'scraper_name', 'remark'
  ];
}
