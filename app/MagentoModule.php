<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoModule extends Model
{

    protected $guarded = ['id'];

    protected $fillable = [
        'module_category_id',
        'store_website_id',
        'module',
        'module_description',
        'current_version',
        'task_status',
        'last_message',
        'cron_time',
        'module_type',
        'status',
        'is_sql',
        'api',
        'cron_job',
        'is_third_party_plugin',
        'is_third_party_js',
        'is_js_css',
        'payment_status',
        'developer_name',
        'is_customized',
        'site_impact'
    ];

    public function module_category()
    {
        return $this->belongsTo(MagentoModuleCategory::class, 'module_category_id');
    }

    public function store_website()
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id');
    }

    public function lastRemark()
    {
        return $this->hasOne(MagentoModuleRemark::class, 'magento_module_id', 'id')->orderBy('created_at', 'desc')->latest();
    }

    public function remarks()
    {
        return $this->hasMany(MagentoModuleRemark::class, 'magento_module_id', 'id')->orderBy('created_at', 'desc');
    }

    public function task_status_data()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status', 'id');
    }

    public function module_type_data()
    {
        return $this->belongsTo(MagentoModuleType::class, 'module_type', 'id');
    }
}
