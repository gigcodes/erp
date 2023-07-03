<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class MagentoModuleVerifiedBy extends Model
{
    protected $table = 'magento_module_verified_by_histories';

    
    public function magentoModule()
    {
        return $this->belongsTo(\App\MagentoModule::class);
    }

    public function newVerifiedBy()
    {
        return $this->belongsTo(user::class, 'new_verified_by_id');
    }

    public function oldVerifiedBy()
    {
        return $this->belongsTo(user::class, 'old_verified_by_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
