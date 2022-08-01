<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Position;
use App\Criteria;
class PositionController extends Controller
{
    public function store(Request $request){
        $request_object = $request->all();
        $position = array(
            "user_id" => \Auth::id(),
            "title" => $request_object['title']
        );
        $position_id = Position::create($position);
        if(!empty($request_object['criteria'])){
            $request_object['criteria'] = explode(",",$request_object['criteria']);
            foreach($request_object['criteria'] as $criteria){
                $criteria_array = array(
                    "title" => $criteria,
                    "position_id" => $position_id->id
                );
                Criteria::create($criteria_array);
            }
        }
        return redirect()->route('vendors.index')->withSuccess('Position added successfully');
    }

    public function list($id=null){
        $criterias = Criteria::where("position_id",$id)->get();
        return response()->json(["code" => 200, "data" => $criterias, "message" => 'Success']);       
    }
}