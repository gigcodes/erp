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
		$data['incoming_calls_yogesh'] = Setting::get('incoming_calls_yogesh');
		$data['incoming_calls_andy'] = Setting::get('incoming_calls_andy');
		$data['users_array'] = Helpers::getUserArray(User::all());
		$data['image_shortcut'] = Setting::get('image_shortcut');
		$data['price_shortcut'] = Setting::get('price_shortcut');
		$data['call_shortcut'] = Setting::get('call_shortcut');
		$data['screenshot_shortcut'] = Setting::get('screenshot_shortcut');
		$data['consignor_name'] = Setting::get('consignor_name');
		$data['consignor_address'] = Setting::get('consignor_address');
		$data['consignor_city'] = Setting::get('consignor_city');
		$data['consignor_country'] = Setting::get('consignor_country');
		$data['consignor_phone'] = Setting::get('consignor_phone');

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
		$incoming_calls_yogesh = $request->incoming_calls_yogesh ? 1 : 0;
		$incoming_calls_andy = $request->incoming_calls_andy ? 1 : 0;


//		Setting::add('euro_to_inr', $euro_to_inr, 'double');
//		Setting::add('special_price_discount', $special_price_discount, 'int');
		Setting::add('pagination', $pagination, 'int');
		Setting::add('incoming_calls_yogesh', $incoming_calls_yogesh, 'tinyint');
		Setting::add('incoming_calls_andy', $incoming_calls_andy, 'tinyint');
		Setting::add('image_shortcut', $request->image_shortcut, 'tinyint');
		Setting::add('price_shortcut', $request->price_shortcut, 'tinyint');
		Setting::add('call_shortcut', $request->call_shortcut, 'tinyint');
		Setting::add('screenshot_shortcut', $request->screenshot_shortcut, 'tinyint');
		Setting::add('consignor_name', $request->consignor_name, 'string');
		Setting::add('consignor_address', $request->consignor_address, 'string');
		Setting::add('consignor_city', $request->consignor_city, 'string');
		Setting::add('consignor_country', $request->consignor_country, 'string');
		Setting::add('consignor_phone', $request->consignor_phone, 'string');

		return redirect()->back()->with('status', 'Settings has been saved.');
	}
}
