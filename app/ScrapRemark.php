<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Loggers\LogScraper;
use Plank\Mediable\Mediable;

class ScrapRemark extends Model
{
    use Mediable;
    protected $fillable = [
        'user_name',
        'scraper_name',
        'remark',
        'scrap_id',
        'module_type'
    ];

    public function scraps()
    {
        return $this->belongsTo(LogScraper::class);
    }
}