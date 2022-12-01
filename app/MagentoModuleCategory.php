<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoModuleCategory extends Model
{
    protected $table = 'magento_module_categories';

    protected $guarded = ['id'];

    protected $fillable = [
        'category_name',
        'status',
    ];
}
