<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\SiteDevelopmentCategory;
use App\User;

class MagentoFrontendCategoryHistory extends Model
{
    use HasFactory;

    protected $table = 'magento_frontend_category_histories';

    public function newCategory()
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'new_category_id');
    }

    public function oldCategory()
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'old_category_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
