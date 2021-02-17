<?php

namespace App;

use App\LinksToPost;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ArticleCategory extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"name"})
     */
    protected $fillable = array('name');

    public function listToPostTo()
    {
        return $this->belongsTo(LinksToPost::class, 'id', 'category_id');
    }
}