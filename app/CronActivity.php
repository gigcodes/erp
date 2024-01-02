<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class CronActivity extends Model
{
    protected $table = 'cron_activities';

    protected $fillable = [
        'id',
        'assign_by_id',
        'cron_id',
        'assign_to_id',
    ];

    public function assignBy()
    {
        return $this->belongsTo(User::class, 'assign_by_id');
    }

    public function assignTo()
    {
        return $this->belongsTo(User::class, 'assign_to_id');
    }
}
