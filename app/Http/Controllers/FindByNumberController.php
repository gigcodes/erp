<?php

namespace App\Http\Controllers;

use Twilio\Jwt\ClientToken;
use Twilio\Twiml;
use Twilio\Rest\Client;
use App\Category;
use App\Notification;
use App\Leads;
use App\Customer;
use App\Order;
use App\Status;
use App\Agent;
use App\Supplier;
use App\Setting;
use App\User;
use App\Brand;
use App\Product;
use App\Message;
use App\Purchase;
use App\Contact;
use App\Dubbizle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers;
use App\ChatMessage;


class FindByNumberController extends Controller
{
	protected function findLeadByNumber($number)
	{
		return Leads::where('contactno', '=', $number)->first();
	}
	protected function findDubbizleByNumber($number)
	{
		return Dubbizle::where('phone_number', $number)->first();
	}
	protected function findCustomerByNumber($number)
	{
		return Customer::where('phone', '=', $number)->first();
	}
    protected function findOrderByNumber($number)
	{
		return Order::where('contact_detail', '=', $number)->first();
	}

	protected function findSupplierByNumber($number)
	{
		if ($agent = Agent::where('phone', $number)->first()) {
			if ($agent->purchase && $agent->purchase->purchase_supplier) {
				return $agent->purchase->purchase_supplier;
			}

			if (preg_match("/supplier/i", $agent->model_type)) {
				return Supplier::find($agent->model_id);
			}
		}

		return Supplier::where('phone', $number)->first();
	}

	protected function findUserByNumber($number)
	{
		return User::where('phone', '=', $number)->first();
	}

	protected function findContactByNumber($number)
	{
		return Contact::where('phone', '=', $number)->first();
	}

  protected function findLeadOrOrderByNumber($number)
  {
      $lead = $this->findLeadByNumber($number);
      if($lead) {
          return array("leads", $lead);
      }
      $order = $this->findOrderByNumber($number);
      if ($order) {
          return array("orders", $order);
      }
      return array(FALSE, FALSE);
  }

	protected function findCustomerOrLeadOrOrderByNumber($number)
  {
		$customer = $this->findCustomerByNumber($number);
		if($customer) {
				return array("customers", $customer);
		}
    $lead = $this->findLeadByNumber($number);
    if($lead) {
        return array("leads", $lead);
    }
    $order = $this->findOrderByNumber($number);
    if ($order) {
        return array("orders", $order);
    }
    return array(FALSE, FALSE);
  }
}
