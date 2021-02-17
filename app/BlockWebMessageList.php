<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BlockWebMessageList extends Model
{
    /**
     * @var string
     * @SWG\Property(enum={"object_id", "object_type", "created_at", "updated_at"})
     */
    protected $fillable = [
        'object_id', 'object_type', 'created_at', 'updated_at'
    ];

    public function reviews()
    {
        return $this->hasMany('App\Review');
    }

    public function has_posted_reviews()
    {
        $count = $this->hasMany('App\Review')->where('status', 'posted')->count();

        return $count > 0;
    }
}
