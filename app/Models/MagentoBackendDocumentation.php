<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\SiteDevelopmentCategory;
use App\PostmanRequestCreate;
use App\MagentoModule;

class MagentoBackendDocumentation extends Model
{
    use HasFactory;

    public $table = 'magento_backend_docs';

    public function siteDevelopementCategory()
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'site_development_category_id', 'id');
    }

    public function postmamRequest()
    {
        return $this->belongsTo(PostmanRequestCreate::class, 'post_man_api_id', 'id');
    }

    public function magentoModule()
    {
        return $this->belongsTo(MagentoModule::class, 'mageneto_module_id', 'id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
}
