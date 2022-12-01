<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoModuleCronJobHistory extends Model
{
    protected $table = 'magento_module_cron_job_histories';

    protected $fillable = ['magento_module_id', 'cron_time', 'frequency', 'cpu_memory', 'comments', 'user_id'];

    public function magento_module()
    {
        return $this->belongsTo(MagentoModule::class, 'magento_module_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
