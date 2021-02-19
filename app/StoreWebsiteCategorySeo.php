<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class StoreWebsiteCategorySeo extends Model
{
			/**
     * @var string
      * @SWG\Property(property="category_id",type="integer")
      * @SWG\Property(property="language_id",type="integer")
     * @SWG\Property(property="meta_title",type="string")
     * @SWG\Property(property="meta_description",type="string")
     * @SWG\Property(property="meta_keyword",type="integer")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    protected $fillable = [
        'category_id','store_website_id', 'meta_title', 'meta_description', 'meta_keyword', 'created_at', 'updated_at','language_id'
    ];
}
