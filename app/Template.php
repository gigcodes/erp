<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class Template extends Model
{
    use Mediable;
    protected $fillable = [
        'name',
        'no_of_images',
        'auto_generate_product',
        'uid',
        'available_modifications',
        
    ];

    public function modifications()
    {
    	return $this->hasMany('App\TemplateModification','template_id','id');
    }
}
