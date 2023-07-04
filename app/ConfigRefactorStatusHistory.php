<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class ConfigRefactorStatusHistory extends Model
{
    public function configRefactor()
    {
        return $this->belongsTo(ConfigRefactor::class);
    }

    public function newStatus()
    {
        return $this->belongsTo(ConfigRefactorStatus::class, 'new_status_id');
    }

    public function oldStatus()
    {
        return $this->belongsTo(ConfigRefactorStatus::class, 'old_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}