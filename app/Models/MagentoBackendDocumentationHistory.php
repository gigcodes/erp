<?php

namespace App\Models;

use App\User;
use App\MagentoModule;
use App\PostmanRequestCreate;
use App\SiteDevelopmentCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoBackendDocumentationHistory extends Model
{
    use HasFactory;

    public $table = 'magento_backend_docs_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function siteDevelopementOldCategory()
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'old_id');
    }

    public function siteDevelopementNewCategory()
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'new_id');
    }

    public function postmanoldrequestapi()
    {
        return $this->belongsTo(PostmanRequestCreate::class, 'old_id');
    }

    public function postmannewrequestapi()
    {
        return $this->belongsTo(PostmanRequestCreate::class, 'new_id');
    }

    public function magneteoldmodule()
    {
        return $this->belongsTo(MagentoModule::class, 'old_id');
    }

    public function magnetenewmodule()
    {
        return $this->belongsTo(MagentoModule::class, 'new_id');
    }
}
