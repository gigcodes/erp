<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Activity extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"subject_id","subject_type","causer_id","description"})
     */
    protected $fillable = ['subject_id','subject_type','causer_id','description'];
}
