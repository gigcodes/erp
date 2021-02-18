<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
      /**
     * @var string
     * @SWG\Property(property="jobs",type="string")
     * @SWG\Property(property="queue",type="string")
     * @SWG\Property(property="payload",type="string")
     * @SWG\Property(property="attempts",type="string")
     */
    protected $table = 'jobs'; 
	
    protected $fillable = [
        'queue','payload','attempts'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    public $timestamps = false;

    protected $hidden = [
    ];
}
