<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Schema;

class MagentoPageBuilder extends Model
{
    use HasFactory;

    protected $fillable = ['*'];
    public $timestamps = false;

    const ID = 'id';

    protected $table = 'magento_pagebuilder';


    public function getColumns()
    {
        return Schema::getColumnListing($this->table);
    }

    public static function getPath()
    {
        return rtrim(env('MAGENTO_PATH_TO_ADMIN', 'http://localhost:8001/backend'), '/');
    }
}
