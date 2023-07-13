<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\MagentoModuleVerifiedStatus;
use App\User;

class MagentoModule extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['row_bg_colour'];

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
        'site_impact',
        'dev_verified_by',
        'dev_verified_status_id',
        'lead_verified_by',
        'lead_verified_status_id',
        'dev_last_remark',
        'lead_last_remark',
        'dependency',
        'composer',
        'module_review_standard',
        'magneto_location_id',
        'used_at',
    ];

    public function module_category()
    {
        return $this->belongsTo(MagentoModuleCategory::class, 'module_category_id');
    }

    public function module_location()
    {
        return $this->belongsTo(MagentoModulLocation::class, 'magneto_location_id');
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

    public function dev_verified()
    {
        return $this->belongsTo(User::class, 'dev_verified_by', 'id');
    }
    public function dev_verified_status()
    {
        return $this->belongsTo(MagentoModuleVerifiedStatus::class, 'dev_verified_status_id', 'id');
    }

    public function lead_verified()
    {
        return $this->belongsTo(User::class, 'lead_verified_by', 'id');
    }

    public function lead_verified_status()
    {
        return $this->belongsTo(MagentoModuleVerifiedStatus::class, 'lead_verified_status_id', 'id');
    }

    public function getRowBgColourAttribute()
    {
        $colour = "";
        if ($this->lead_verified_status) {
            $colour = @$this->lead_verified_status->color;
        } elseif ($this->dev_verified_status) {
            $colour = @$this->dev_verified_status->color;
        }

        return $colour;
    }
}
