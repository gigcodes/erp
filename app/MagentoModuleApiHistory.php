<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoModuleApiHistory extends Model
{
    protected $table = 'magento_module_api_histories';

    protected $fillable = ['magento_module_id', 'resources', 'frequency', 'user_id'];

    public function magento_module()
    {
        return $this->belongsTo(MagentoModule::class, 'magento_module_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
