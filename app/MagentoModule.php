<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoModule extends Model
{

    protected $guarded = ['id'];

    protected $fillable = [
        'module_category_id',
        'module',
        'module_description',
        'current_version',
        'module_type',
        'status',
        'payment_status',
        'developer_name',
        'is_customized',
    ];

    public function module_category()
    {
        return $this->belongsTo(ModuleCategory::class, 'module_category_id');
    }
}
