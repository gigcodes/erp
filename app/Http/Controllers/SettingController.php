<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{

	public function __construct() {

		$this->middleware('permission:setting-list',['only' => ['index']]);
		$this->middleware('permission:setting-create',['only' => ['store']]);
	}

	public function index()
	{
		$data = [];
//		$data['euro_to_inr'] = Setting::get('euro_to_inr');
//		$data['special_price_discount'] = Setting::get('special_price_discount');
		$data['pagination'] = Setting::get('pagination');

		return view('setting.index',$data);
	}

	public function store(Request $request)
	{
		/*$data = $this->validate($request, [
			'euro_to_inr' => 'required'
		]);*/

		$euro_to_inr = $request->input('euro_to_inr');
//		$special_price_discount = $request->input('special_price_discount');
		$pagination = $request->input('pagination');

//		Setting::add('euro_to_inr', $euro_to_inr, 'double');
//		Setting::add('special_price_discount', $special_price_discount, 'int');
		Setting::add('pagination', $pagination, 'int');

		return redirect()->back()->with('status', 'Settings has been saved.');
	}
}
