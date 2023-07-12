<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoModuleHistory extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'magento_module_id',
        'module_category_id',
        'store_website_id',
        'module',
        'module_description',
        'current_version',
        'task_status',
        'last_message',
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
        'user_id',
        'used_at',
    ];

    public function magento_module()
    {
        return $this->belongsTo(MagentoModule::class, 'magento_module_id');
    }

    public function developer_name_data()
    {
        return $this->belongsTo(User::class, 'developer_name');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function module_category()
    {
        return $this->belongsTo(MagentoModuleCategory::class, 'module_category_id');
    }

    public function store_website()
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id');
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
