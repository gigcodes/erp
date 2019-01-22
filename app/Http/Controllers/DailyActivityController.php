<?php

namespace App\Http\Controllers;

use App\DailyActivity;
use Illuminate\Http\Request;

class DailyActivityController extends Controller
{
    function store(Request $request){

    	$data = json_decode(urldecode($request->input('activity_table_data')),true);

    	foreach ($data as $item){

    		if(!empty($item['id']))
    		    DailyActivity::updateOrCreate(['id' => $item['id']],$item);
    		else{
    			$item['for_date'] = date('Y-m-d');
    			$item['user_id'] = \Auth::id();
			    DailyActivity::create($item);
		    }
	    }
    }

    function get(Request $request){

    	$selected_user = $request->input('selected_user');
    	$user_id = $selected_user ??  \Auth::id();

    	$activities = DailyActivity::where('user_id',$user_id)
	                               ->where('for_date',$request->daily_activity_date)->get()->toArray();

    	return $activities;
    }
}
