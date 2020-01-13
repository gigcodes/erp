<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use \Carbon\Carbon;
use Response;

class ScrapLogsController extends Controller
{
    public function index(Request $Request) 
    {	
		$name = "";
		return view('scrap-logs.index',compact('name'));
    }

	public function filter($searchVal, $dateVal) 
    {
    	$file_list = [];
    	$searchVal = $searchVal != "null" ? $searchVal : "";
    	$dateVal = $dateVal != "null" ? $dateVal : "";
		$file_list = [];
		//$files = File::allFiles(public_path(env('SCRAP_LOGS_FOLDER'))); 
		$files = File::allFiles(base_path(env('SCRAP_LOGS_FOLDER'))); 
		$date = empty($dateVal )? Carbon::now()->format('d') : sprintf("%02d", $dateVal);
		if($date == 01) 
		{
			$date = 32;
		}
		foreach ($files as $key => $val) {
			$day_of_file = explode('-', $val->getFilename());
			if(str_contains(end($day_of_file), sprintf("%02d", $date-1)) && (str_contains($val->getFilename(), $searchVal) || empty($searchVal))) {
				array_push($file_list, array(
						"filename" => $val->getFilename(),
	        			"foldername" => $val->getRelativepath()
	    			)
	    		);
			}
		}
		return  response()->json(["file_list" => $file_list]);
    }

    public function fileView($filename, $foldername) {
		$path = base_path(env('SCRAP_LOGS_FOLDER') . '/' . $foldername . '/' . $filename);
    	//$path = storage_path('scrap-logs/' .$foldername.'/'.$filename);
    	return response()->file($path);
    }
    
    public function indexByName($name) {
    	$name =  strtolower(str_replace(' ', '', $name));
    	return view('scrap-logs.index',compact('name'));
    }

}
