<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoSetting extends Model
{
    protected $fillable = ['scope', 'scope_id', 'store_website_id', 'website_store_id', 'website_store_view_id', 'data_type', 'name', 'path', 'value', 'value_on_magento', 'created_by', 'status'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($magentoSetting) {
            $magentoSetting->created_by = auth()->id();
        });
    }

    /** Stores */
    public function storeview()
    {
        return $this->hasOne(WebsiteStoreView::class, 'id', 'scope_id');
    }

    /** Default */
    public function website()
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'scope_id');
    }

    /** Websites */
    public function store()
    {
        return $this->hasOne(WebsiteStore::class, 'id', 'scope_id');
    }

    /** Websites */
    public function fromStoreId()
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public function fromStoreIdwebsite()
    {
        return $this->hasOne(WebsiteStore::class, 'id', 'website_store_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'magento_setting_user');
    }

    /**
     * Model accrssor and mutator
     */
    public function getStatusColorAttribute()
    {
        if ($this->status) {
            $status = MagentoSettingStatus::where('name', $this->status)->first();
            if ($status) {
                return $status->color;
            }
        }

        return '';
    }
}
