<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $data = MessagingGroup::leftJoin('sms_service', 'sms_service.id', '=', 'messaging_groups.service_id')
		->leftJoin('store_websites', 'store_websites.id', '=', 'messaging_groups.store_website_id')->select('messaging_groups.*', 'sms_service.name as service', 'store_websites.title as website')->orderBy('messaging_groups.id', 'desc')->paginate(15);
        $websites = [''=>'Select Website'] + StoreWebsite::pluck('title', 'id')->toArray();
		$services = [''=>'Select Service'] + SmsService::pluck('name', 'id')->toArray();
        return view('twillio_sms.index', compact('data','websites', 'services'));
    }

    public function createMessagingGroup(Request $request) {
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

   
    public function createService (Request $request) {
		$validator = Validator::make($request->all(), [
           'name' => 'required'
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
		$input = $request->input();
        $data = SmsService::firstOrCreate(['name'=>$input['name']], ['name'=>$input['name']]);
		return response()->json(['status' => 'success', 'statusCode'=>200,'message' => 'Service Created successfully']);
    }

	public function showCustomerList($messageGroupId) {
		$customers = MessagingGroupCustomer::leftJoin('customers', 'customers.id', '=', 'messaging_group_customers.customer_id')
					->where('messaging_group_customers.message_group_id', $messageGroupId)->select('customers.*', 'messaging_group_customers.id as groupCustomerId')->get();
		return view('twillio_sms.customer', compact('customers','messageGroupId'));
	}
	
	public function removeCustomer(Request $request) {
		$customerId = $request->id;
		$messageGroupCustomer = MessagingGroupCustomer::where('id', $customerId)->first();
		if($messageGroupCustomer != null) {
			$marketing_message = MarketingMessage::where('message_group_id', $messageGroupCustomer['message_group_id'])->first();
			MarketingMessageCustomer::where(['marketing_message_id'=> $marketing_message->id, 'customer_id'=>$customerId])->delete();
			$messageGroupCustomer->delete();
		}
		return response()->json(['code' => 200, 'message' => 'Customer removed from message group successfully']);
	}
	
	public function deleteMessageGroup(Request $request) {
		$messageGroupId = $request->id;
		$messageGroup = MessagingGroup::where('id', $request->id)->first();
		if($messageGroup != null) {
			$customers = MessagingGroupCustomer::where('message_group_id', $request->id)->delete();
			$marketing_message = MarketingMessage::where('message_group_id', $messageGroupId)->first();
			MarketingMessageCustomer::where('marketing_message_id', $marketing_message->id)->delete();
			$marketing_message->delete();
		}
		$messageGroup->delete();
		return response()->json(['code' => 200, 'message' => 'Message group deleted successfully']);
	}
	
	public function fetchCustomers(Request $request) {
		$q = $request->q;
		$customers = Customer::where('email', 'like', '%'.$q.'%')->select('id', 'email')->get();
		return json_encode($customers);
	}
	
	public function addCustomer(Request $request) {
		$validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'message_group_id' => 'required'
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
		MessagingGroupCustomer::updateOrCreate(['message_group_id'=>$request->message_group_id, 'customer_id'=>$request->customer_id], ['message_group_id'=>$request->message_group_id, 'customer_id'=>$request->customer_id]);
		
      	return response()->json(['status' => 'success', 'statusCode'=>200,'message' => 'Customer added successfully']);
	}
	
	public function createMarketingMessage(Request $request){
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

        $data = MarketingMessage::updateOrCreate(['id' => $request->id], [
            'title' => $request->title,
            'scheduled_at' => $request->scheduled_at,
            'is_sent' => 0,
            'message_group_id' => $request->message_group_id,
        ]);
		if($request->id != null) {
			$customers = MessagingGroupCustomer::where('message_group_id', $request->message_group_id)->get();
			foreach($customers as $customer) {
				MarketingMessageCustomer::firstOrCreate(['marketing_message_id'=>$data['id'], 'customer_id'=>$customer['customer_id']],
				['marketing_message_id'=>$data['id'], 'customer_id'=>$customer['customer_id']]);
			}
		}
      	return response()->json(['status' => 'success', 'statusCode'=>200,'message' => 'Message Created successfully']);
	}
	
	public function messageTitle($messageGroupId) {
		$details = MarketingMessage::where('message_group_id', $messageGroupId)->first();
		if($details == null) {
			$details['id'] = null;
			$details['title'] = null;
			$details['scheduled_at'] = null;
			$details['message_group_id'] = $messageGroupId;
		}
		return view('twillio_sms.partials.message_title', compact('details'))->render();
	}
}
