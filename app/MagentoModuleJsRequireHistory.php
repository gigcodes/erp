<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoModuleJsRequireHistory extends Model
{
    protected $table = 'magento_module_js_require_histories';

    protected $fillable = ['magento_module_id', 'files_include', 'native_functionality', 'user_id'];

    public function magento_module()
    {
        return $this->belongsTo(MagentoModule::class, 'magento_module_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
