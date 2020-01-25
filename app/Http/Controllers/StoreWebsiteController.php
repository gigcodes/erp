<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StoreWebsite;

class StoreWebsiteController extends Controller
{

	/**
	 * List Page
	 * @param  Request $request [description]
	 * @return 
	 */
	public function index(Request $request)
	{
		$title = "List | Store Website";

		return view("store-website.index",compact('title'));
	}

	/**
	 * records Page
	 * @param  Request $request [description]
	 * @return 
	 */
	public function records(Request $request)
	{
		$records = StoreWebsite::whereNull("deleted_at");
		$records = $records->get();

		return response()->json(["code" => 200 , "data" => $records]);
	}


	
}
