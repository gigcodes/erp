<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class Sop extends Model
{
    protected $table ="sops";
     protected $fillable = ['name','content'];
 
}
