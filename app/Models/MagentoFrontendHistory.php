<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\SiteDevelopmentCategory;

class MagentoFrontendHistory extends Model
{
    use HasFactory;

    public $table = 'magento_frontend_histories';


    protected $fillable = ['magento_frontend_docs_id','store_website_category_id', 'location',  'admin_configuration','frontend_configuration','updated_by'];

    public function storeWebsiteCategory()
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'store_website_category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
