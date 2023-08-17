<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class VirtualminDomain extends Model
{
    protected $table = 'virtualmin_domains';

    protected $fillable = [
        'name',
        'is_enabled',
    ];

}
