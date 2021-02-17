<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BloggerEmailTemplate extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"from","subject","message","cc","bcc","other","type"})
     */
    protected $fillable = ['from','subject','message','cc','bcc','other','type'];
}
