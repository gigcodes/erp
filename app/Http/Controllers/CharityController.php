<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Helpers;
use App\CustomerOrderCharities;
use App\User;
use App\Charity;
use DB;
use Session;

class CharityController extends Controller
{
    //
	public function index(Request $request)
	{
		$query = Charity::query();
		if($request->search){
			$query = $query->where('name', 'LIKE','%'.$request->search.'%')->orWhere('email', 'LIKE', '%'.$request->search.'%');
		}
		$charityData = $query->orderBy('id', 'asc')->paginate(25)->appends(request()->except(['page']));
		return view('charity.index', compact('charityData'))->with('i', ($request->input('page', 1) - 1) * 5);
	}
	
	
	public function store(Request $request)
	{
		$this->validate($request, [
            'email' => 'required|email',
            'contact_no' => 'required|integer',
            'name' => 'required|string'
        ]);
		
		$charity = new Charity;
		$charity->name = $request->name;
		$charity->contact_no = $request->contact_no;
		$charity->email = $request->email;
		$charity->save();
		return redirect()->route('charity')->with('flash_type','success')->with('message','Data successfully saved');
		
	}
	
	public function update(Request $request, $id = null)
	{
		$charityData = Charity::find($id);
		if($request->post('name') && $request->post('email') && $request->post('contact_no') )
		{
			$charityId = $request->post('id');	
			$charityObj = Charity::find($charityId);
			$updateData = array('name'=>$request->name, 'email'=>$request->email, 'contact_no'=>$request->contact_no);
			//Charity::where($charityId)->update($updateData);
			$charityObj->fill($updateData);
			$charityObj->save();
			return redirect()->route('charity')
		                 ->with('flash_type','success')->with('message','Data updated successfully');
		}
		
		return view('charity.edit', compact('charityData'));
		
	}
	
	
	public function charityOrder(Request $request, $charity_id)
	{
		$charityData = Charity::find($charity_id);
		
		$orderCharityData = CustomerOrderCharities::where('charity_id',$charity_id)->get();
		
		$charityOrder = [];
		$i = 0;
		foreach($orderCharityData as $data)
		{
			$userDetails = User::where('id',$data->customer_id)->get()->first()->toArray();	
			$charityOrder[$i]['orderData']['id'] = $data->id;
			$charityOrder[$i]['orderData']['customer_id'] = $data->customer_id;
			$charityOrder[$i]['orderData']['order_id'] = $data->order_id;
			$charityOrder[$i]['orderData']['amount'] = $data->amount;
			$charityOrder[$i]['userData'] = $userDetails;
			$i++;
		}
		$query = CustomerOrderCharities::query();
		$query->where('charity_id',$charity_id);
		$charityoOrderPagination = $query->orderBy('id', 'asc')->paginate(25)->appends(request()->except(['page']));
		return view('charity.charity_order', compact('charityOrder','orderCharityData','charityoOrderPagination'));
		
	}
}
