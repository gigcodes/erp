<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Helpers;
use App\User;
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
		$data['incoming_calls'] = Setting::get('incoming_calls');
		$data['users_array'] = Helpers::getUserArray(User::all());
		$data['image_shortcut'] = Setting::get('image_shortcut');
		$data['price_shortcut'] = Setting::get('price_shortcut');
		$data['call_shortcut'] = Setting::get('call_shortcut');

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
		$incoming_calls = $request->incoming_calls ? 1 : 0;


//		Setting::add('euro_to_inr', $euro_to_inr, 'double');
//		Setting::add('special_price_discount', $special_price_discount, 'int');
		Setting::add('pagination', $pagination, 'int');
		Setting::add('incoming_calls', $incoming_calls, 'tinyint');
		Setting::add('image_shortcut', $request->image_shortcut, 'tinyint');
		Setting::add('price_shortcut', $request->price_shortcut, 'tinyint');
		Setting::add('call_shortcut', $request->call_shortcut, 'tinyint');

		return redirect()->back()->with('status', 'Settings has been saved.');
	}
}
