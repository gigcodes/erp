<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;
use App\MarketingMessageCustomer;
use App\MessagingGroup;
use App\MessagingGroupCustomer;
use App\MarketingMessage;
use App\SmsService;
use App\StoreWebsite;
use App\Customer;
use Validator;

class TwillioMessageController extends Controller{
    public function index() {
        $data = MessagingGroup::orderBy('id', 'desc')->paginate(15);
        $websites = [''=>'Select Website'] + StoreWebsite::pluck('title', 'id')->toArray();
		$services = [''=>'Select Service'] + SmsService::pluck('name', 'id')->toArray();
        return view('twillio_sms.index', compact('data','websites', 'services'));
    }

    public function createMailinglist(Request $request) {
        $validator = Validator::make($request->all(), [
           'name' => 'required',
            'store_website_id' => 'required',
            'service_id' => 'required',
        ]);
		
		if ($validator->fails()) {  
			$errors = $validator->getMessageBag();
			$errors = $errors->toArray();
			$message = '';
			foreach($errors as $error) {
				$message .= $error[0].'<br>';
			}
            return response()->json(['status' => 'failed', 'statusCode'=>500,'message' => $message]);
        }

        $data = MessagingGroup::create([
            'name' => $request->name,
            'store_website_id' => $request->store_website_id,
            'service_id' => $request->service_id,
        ]);
        
      	return response()->json(['status' => 'success', 'statusCode'=>200,'message' => 'MessagingGroup Created successfully']);
    }

    public function destroy (Request $request) {
        Service::destroy($request->id);
        return new JsonResponse(['code' => 200, 'message' => 'Worker deleted successfully']);
    }

    public function update (Request $request) {

        $updated = Service::findOrFail($request->id);

        $updated->name = $request->name;
        $updated->description  = $request->description;
        $updated->save();

        $data = Service::findOrFail($request->id);

        return response()->json([
            $data
        ]);
    }

	public function showCustomerList($messageGroupId) {
		$customers = MessagingGroupCustomer::leftJoin('customers', 'customers.id', '=', 'messaging_group_customers.customer_id')
					->where('messaging_group_customers.message_group_id', $messageGroupId)->select('customers.*')->get();
		return view('twillio_sms.customer', compact('customers','messageGroupId'));
	}
	
	public function fetchCustomers(Request $request) {
		$q = $request->q;
		$customers = Customer::where('email', 'like', '%'.$q.'%')->select('id', 'name')->get();
		return json_encode($customers);
	}
	
	public function createMarketingGroup(Request $request){
		dd('sds');
		 $validator = Validator::make($request->all(), [
           'title' => 'required',
            'scheduled_at' => 'required',
        ]);
		
		if ($validator->fails()) {  
			$errors = $validator->getMessageBag();
			$errors = $errors->toArray();
			$message = '';
			foreach($errors as $error) {
				$message .= $error[0].'<br>';
			}
            return response()->json(['status' => 'failed', 'statusCode'=>500,'message' => $message]);
        }

        $data = MessagingGroup::create([
            'title' => $request->title,
            'scheduled_at' => $request->scheduled_at,
            'is_sent' => $request->is_sent,
            'message_group_id' => $request->message_group_id,
        ]);
        
      	return response()->json(['status' => 'success', 'statusCode'=>200,'message' => 'MessagingGroup Created successfully']);
	}
}
