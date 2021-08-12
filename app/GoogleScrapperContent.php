<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class GoogleScrapperContent extends Model
{
    protected $fillable = [
        'title',
        'date', 
        'image',
        'url',
        'email',
        'number',
        'about_us',
        'facebook',
        'instagram'
    ];

    

}
