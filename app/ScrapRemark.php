<?php
namespace App;

use App\Loggers\LogScraper;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class ScrapRemark extends Model
{

    use Mediable;
    protected $fillable = [
        'user_name',
        'scraper_name',
        'remark',
        'scrap_id',
        'module_type',
        'scrap_field',
        'old_value',
        'new_value',
    ];

    public function scraps()
    {
        return $this->belongsTo(LogScraper::class);
    }
}
