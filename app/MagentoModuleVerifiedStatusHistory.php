<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class MagentoModuleVerifiedStatusHistory extends Model
{
    public function magentoModule()
    {
        return $this->belongsTo(\App\MagentoModule::class);
    }

    public function newStatus()
    {
        return $this->belongsTo(MagentoModuleVerifiedStatus::class, 'new_status_id');
    }

    public function oldStatus()
    {
        return $this->belongsTo(MagentoModuleVerifiedStatus::class, 'old_status_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
