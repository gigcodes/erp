<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'checklist';

    protected $fillable = [
        'category_name', 
        'sub_category_name',
        'subjects'
    ];
    protected $primaryKey = 'id';

}
