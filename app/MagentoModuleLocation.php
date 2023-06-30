<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoModuleLocation extends Model
{
    protected $table = 'magento_module_locations';

    protected $guarded = ['id'];

    protected $fillable = [
        'magento_module_locations',
    ];
}
