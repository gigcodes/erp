<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HubstaffTaskEfficiency extends Model
{
 
  protected $table = 'hubstaff_task_efficiency';	
  protected $fillable = ['user_id','admin_input','user_input','date','time'];
  
}
