<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;

class FlowLogMessages extends Model
{
    protected $guarded = [];  

    public function log($result)
    {
        // Log result to database
        $flowLogMessages = new FlowLogMessages();
        $flowLogMessages->messages = $result["messages"];
        $flowLogMessages->flow_action =  $result["flow_action"];
        $flowLogMessages->modalType =  $result["modalType"];
        $flowLogMessages->leads =  $result["leads"];
        $flowLogMessages->store_website_id =  $result["store_website_id"];
        $flowLogMessages->save();

        // Return
        return;
    }
    public function flowlog()
    {
        return $this->belongsTo('App\Loggers\FlowLogMessages');
    }

}
