<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BloggerProductImage extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"file_name","blogger_product_id","other"})
     */
    protected $fillable = ['file_name','blogger_product_id','other'];
}
