<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsiteCategory;

class MagentoFrontendDocumentation extends Model
{
    use HasFactory;

    public $table = 'magento_frontend_docs';


    public function storeWebsiteCategory()
    {
        return $this->belongsTo(StoreWebsiteCategory::class, 'store_website_category_id', 'id');
    }
}
