<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BarcodeMedia extends Model
{
    use Mediable;
    /**
     * @var string
     * @SWG\Property(enum={"media_id", "type", "type_id", "name", "price", "extra", "created_at", "updated_at"})
     */
    protected $fillable = ['media_id', 'type', 'type_id', 'name', 'price', 'extra', 'created_at', 'updated_at'];
}
