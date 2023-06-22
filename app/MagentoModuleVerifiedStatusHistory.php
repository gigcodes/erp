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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
