<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class SopCategory extends Model
{
    protected $table = 'sops_category';

    protected $fillable = ['category_name'];
}
