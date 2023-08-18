<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VirtualminDomain extends Model
{
    use SoftDeletes;

    protected $table = 'virtualmin_domains';

    protected $fillable = [
        'name',
        'is_enabled',
    ];

}
