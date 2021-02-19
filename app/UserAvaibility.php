<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class UserAvaibility extends Model
{
	     /**
     * @var string
      * @SWG\Property(property="user_id",type="integer")
      * @SWG\Property(property="from",type="string")
      * @SWG\Property(property="to",type="string")
      * @SWG\Property(property="status",type="string")
      * @SWG\Property(property="note",type="string")
      * @SWG\Property(property="date",type="datetime")
     */
    protected $fillable = ['user_id','from','to','status','note','date'];
}
