<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class MagentoModuleM2ErrorAssigneeHistory extends Model
{
    protected $table = 'magento_module_m2_error_assignee_histories';

    public function magentoModule()
    {
        return $this->belongsTo(\App\MagentoModule::class);
    }

    public function newAssignee()
    {
        return $this->belongsTo(User::class, 'new_assignee_id');
    }

    public function oldAssignee()
    {
        return $this->belongsTo(User::class, 'old_assignee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
