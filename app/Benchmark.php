<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Benchmark extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"selections", "searches", "attributes", "supervisor", "imagecropper", "lister", "approver", "inventory", "for_date"})
     */
    protected $fillable = [
    	'selections',
	    'searches',
	    'attributes',
	    'supervisor',
	    'imagecropper',
	    'lister',
	    'approver',
	    'inventory',
	    'for_date'
    ];
}
