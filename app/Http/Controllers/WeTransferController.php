<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Wetransfer;

class WeTransferController extends Controller
{
    public function index()
    {
    	return view('wetransfer.index');
    }

    public function getLink()
    {
    	$wetransfer = Wetransfer::where('is_processed',0)->first();
    	if($wetransfer == null){
    		return json_encode(['error' => 'Nothing to process now']);
    	}
    	return json_encode($wetransfer);
    }

    public function recieveFile(Request $request)
    {
    	
    }
}
