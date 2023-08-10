<?php

namespace App\Models;

use App\User;
use App\SiteDevelopmentCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoFrontendDocumentation extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'magento_frontend_docs';

    public function storeWebsiteCategory()
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'store_website_category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
