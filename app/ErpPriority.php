<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ErpPriority extends Model
{
    /**
     * Fillables for the database
     *
     * @access protected
     *
     * @var array $fillable
     */
    protected $fillable = array(
        'model_id', 'model_type', 'user_id'
    );

    public function detail()
    {
        if($this->model_type == \App\Task::class) {
            return (new $this->model_type)->where("id",$this->model_id)->select(["*","task_subject as task",\DB::raw("concat('Task','#',id) as task_id")])->first();
        }else{
            return (new $this->model_type)->where("id",$this->model_id)->select(["*",\DB::raw("concat('DEVTASK','#',id) as task_id")])->first();
        }
    }

}
