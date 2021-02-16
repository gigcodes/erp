<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AdAccount extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"account_name", "note", "config_file", "status"})
     */
    protected $fillable = [
        'account_name', 'note', 'config_file', 'status',
    ];
}
