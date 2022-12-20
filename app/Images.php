<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class Images extends Model
{
    use Mediable;
    use SoftDeletes;

    public function tags()
    {
        return $this->belongsToMany(\App\Tag::class, 'image_tags', 'image_id', 'tag_id');
    }

    public function saveFromSearchQueues($path, $link, $filename)
    {
        if (copy($link, $path.'/'.$filename)) {
            return true;
        } else {
            return false;
        }
    }

    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }

    public function productImg($id, $notId)
    {
        return $this->where('product_id', $id)->whereNotNull('product_id')->orderBy('id', 'desc')->get();
    }
}
