<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ChatbotCategory extends Model
{
	/**
     * @var string
     * @SWG\Property(enum={"name"})
     */
    protected $fillable = [
        'name'
    ];
}
