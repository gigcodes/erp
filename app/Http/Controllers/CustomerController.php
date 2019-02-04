<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\Setting;
use App\Leads;
use App\Order;
use App\Status;
use App\Brand;
use App\User;
use App\ChatMessage;
use App\Message;
use App\Helpers;
use App\Reply;
use App\Instruction;
use App\ReplyCategory;
use App\CallRecording;
use App\InstructionCategory;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $term = $request->input('term');
      $customers = (new Customer())->newQuery();

      if($request->input('orderby') == '')
  				$orderby = 'asc';
  		else
  				$orderby = 'desc';

  		switch ($request->input('sortby')) {
  			case 'name':
  					 $sortby = 'name';
  					break;
  			case 'email':
  					 $sortby = 'email';
  					break;
  			case 'phone':
  					 $sortby = 'phone';
  					break;
  			case 'instagram':
  					 $sortby = 'instahandler';
  					break;
        case 'lead_created':
  					 $sortby = 'lead_created';
  					break;
        case 'order_created':
  					 $sortby = 'order_created';
  					break;
        case 'rating':
  					 $sortby = 'rating';
  					break;
        case 'communication':
  					 $sortby = 'communication';
  					break;
  			default :
  					 $sortby = 'communication';
  		}

      if ($sortby != 'communication' && $sortby != 'rating' && $sortby != 'lead_created' && $sortby != 'order_created') {
  			$customers = $customers->orderBy($sortby, $orderby);
      }

      if(empty($term))
  			$customers = $customers->latest();
  		else{
  			$customers = $customers->latest()
  			               ->orWhere('name', 'LIKE', "%$term%")
  			               ->orWhere('phone', 'LIKE', "%$term%")
  			               ->orWhere('instahandler', 'LIKE', "%$term%");
  		}

      $customers = $customers->get()->toArray();

      if ($sortby == 'communication') {
  			if ($orderby == 'asc') {
          // usort($customers, 'sortByReply');
  				$customers = array_values(array_sort($customers, function ($value) {
            // if ($value['communication']['status'] == '5') {
            //   return '0';
            // }
            //
            // if ($value['communication']['status'] == null) {
            //   return '10';
            // }

						return $value['communication']['created_at'];
  				}));

  				$customers = array_reverse($customers);
  			} else {
  				$customers = array_values(array_sort($customers, function ($value) {
            // if ($value['communication']['status'] == '5') {
            //   return '0';
            // }
            //
            // if ($value['communication']['status'] == null) {
            //   return '10';
            // }

            return $value['communication']['created_at'];
  				}));
  			}
  		}

      if ($sortby == 'rating') {
  			if ($orderby == 'asc') {
  				$customers = array_values(array_sort($customers, function ($value) {
  						return $value['lead']['rating'];
  				}));

  				$customers = array_reverse($customers);
  			} else {
  				$customers = array_values(array_sort($customers, function ($value) {
  						return $value['lead']['rating'];
  				}));
  			}
  		}

      if ($sortby == 'lead_created') {
  			if ($orderby == 'asc') {
  				$customers = array_values(array_sort($customers, function ($value) {
  						return $value['lead']['created_at'];
  				}));

  				$customers = array_reverse($customers);
  			} else {
  				$customers = array_values(array_sort($customers, function ($value) {
  						return $value['lead']['created_at'];
  				}));
  			}
  		}

      if ($sortby == 'order_created') {
  			if ($orderby == 'asc') {
  				$customers = array_values(array_sort($customers, function ($value) {
  						return $value['order']['created_at'];
  				}));

  				$customers = array_reverse($customers);
  			} else {
  				$customers = array_values(array_sort($customers, function ($value) {
  						return $value['order']['created_at'];
  				}));
  			}
  		}

      $currentPage = LengthAwarePaginator::resolveCurrentPage();
  		$perPage = Setting::get('pagination');
  		$currentItems = array_slice($customers, $perPage * ($currentPage - 1), $perPage);

  		$customers = new LengthAwarePaginator($currentItems, count($customers), $perPage, $currentPage, [
  			'path'	=> LengthAwarePaginator::resolveCurrentPath()
  		]);

      $customers_all = Customer::all();

      return view('customers.index', [
        'customers' => $customers,
        'customers_all' => $customers_all,
        'term' => $term,
        'orderby' => $orderby,
      ]);
    }

    // public function sortByReply($a, $b)
    // {
    //   $a = $a['status'] == 0 || $a['status'] == 5 ? 0 : 1;
    //   if ($a['status'] == $b['status']) return 0;
    //
    //   if ($a['status'])
    //
    //   return ($a['status'])
    // }

    public function load(Request $request)
    {
      $first_customer = Customer::find($request->first_customer);
      $second_customer = Customer::find($request->second_customer);

      return response()->json([
        'first_customer'  => $first_customer,
        'second_customer'  => $second_customer
      ]);
    }

    public function merge(Request $request)
    {
      $this->validate($request, [
        'name'          => 'required|min:3|max:255',
        'email'         => 'required_without_all:phone,instahandler|nullable|email',
        'phone'         => 'required_without_all:email,instahandler|nullable|numeric|regex:/^[91]{2}/|digits:12|unique:customers,phone,' . $request->first_customer_id,
        'instahandler'  => 'required_without_all:email,phone|nullable|min:3|max:255',
        'rating'        => 'required|numeric',
        'address'       => 'sometimes|nullable|min:3|max:255',
        'city'          => 'sometimes|nullable|min:3|max:255',
        'country'       => 'sometimes|nullable|min:3|max:255'
      ]);

      $first_customer = Customer::find($request->first_customer_id);

      $first_customer->name = $request->name;
      $first_customer->email = $request->email;
      $first_customer->phone = $request->phone;
      $first_customer->whatsapp_number = $request->whatsapp_number;
      $first_customer->instahandler = $request->instahandler;
      $first_customer->rating = $request->rating;
      $first_customer->address = $request->address;
      $first_customer->city = $request->city;
      $first_customer->country = $request->country;

      $first_customer->save();

      $chat_messages = ChatMessage::where('customer_id', $request->second_customer_id)->get();

      foreach ($chat_messages as $chat) {
        $chat->customer_id = $first_customer->id;
        $chat->save();
      }

      $messages = Message::where('customer_id', $request->second_customer_id)->get();

      foreach ($messages as $message) {
        $message->customer_id = $first_customer->id;
        $message->save();
      }

      $leads = Leads::where('customer_id', $request->second_customer_id)->get();

      foreach ($leads as $lead) {
        $lead->customer_id = $first_customer->id;
        $lead->save();
      }

      $orders = Order::where('customer_id', $request->second_customer_id)->get();

      foreach ($orders as $order) {
        $order->customer_id = $first_customer->id;
        $order->save();
      }

      $instructions = Instruction::where('customer_id', $request->second_customer_id)->get();

      foreach ($instructions as $instruction) {
        $instruction->customer_id = $first_customer->id;
        $instruction->save();
      }

      $second_customer = Customer::find($request->second_customer_id);
      $second_customer->delete();

      return redirect()->route('customer.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'name'          => 'required|min:3|max:255',
        'email'         => 'required_without_all:phone,instahandler|nullable|email',
        'phone'         => 'required_without_all:email,instahandler|nullable|numeric|regex:/^[91]{2}/|digits:12|unique:customers',
        'instahandler'  => 'required_without_all:email,phone|nullable|min:3|max:255',
        'rating'        => 'required|numeric',
        'address'       => 'sometimes|nullable|min:3|max:255',
        'city'          => 'sometimes|nullable|min:3|max:255',
        'country'       => 'sometimes|nullable|min:3|max:255'
      ]);

      $customer = new Customer;

      $customer->name = $request->name;
      $customer->email = $request->email;
      $customer->phone = $request->phone;
      $customer->whatsapp_number = $request->whatsapp_number;
      $customer->instahandler = $request->instahandler;
      $customer->rating = $request->rating;
      $customer->address = $request->address;
      $customer->city = $request->city;
      $customer->country = $request->country;

      $customer->save();

      return redirect()->route('customer.index')->with('success', 'You have successfully added new customer!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $customer = Customer::find($id);
      $customers = Customer::all();

      if($customer->phone != ''){
        $phone_number = str_replace('+', '', $customer->phone);
        if (strlen($phone_number) > 10) {
         $customer_number = str_replace('91', '', $phone_number);
        }else{
          $customer_number = $phone_number;
        }

     $call_history = CallRecording::where('customer_number','LIKE', "%$customer_number%")->orderBy('created_at', 'DESC')->get()->toArray();
      }


      // $leads = Leads::find($id);
      $status = (New status)->all();
      $users = User::all()->toArray();
      $users_array = Helpers::getUserArray(User::all());
      $brands = Brand::all()->toArray();
      $reply_categories = ReplyCategory::all();
      $instruction_categories = InstructionCategory::all();

      return view('customers.show', [
        'customers'  => $customers,
        'customer'  => $customer,
        'status'    => $status,
        'brands'    => $brands,
        'users'     => $users,
        'users_array'     => $users_array,
        // 'approval_replies'     => $approval_replies,
        // 'internal_replies'     => $internal_replies,
        'reply_categories'  => $reply_categories,
        'call_history' =>  $call_history,
        'instruction_categories' =>  $instruction_categories
      ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $customer = Customer::find($id);

      return view('customers.edit')->withCustomer($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $customer = Customer::find($id);

      $this->validate($request, [
        'name'          => 'required|min:3|max:255',
        'email'         => 'required_without_all:phone,instahandler|nullable|email',
        'phone'         => 'required_without_all:email,instahandler|nullable|regex:/^[91]{2}/|digits:12|unique:customers,phone,' . $id,
        'instahandler'  => 'required_without_all:email,phone|nullable|min:3|max:255',
        'rating'        => 'required|numeric',
        'address'       => 'sometimes|nullable|min:3|max:255',
        'city'          => 'sometimes|nullable|min:3|max:255',
        'country'       => 'sometimes|nullable|min:3|max:255'
      ]);

      $customer->name = $request->name;
      $customer->email = $request->email;
      $customer->phone = $request->phone;
      $customer->whatsapp_number = $request->whatsapp_number;
      $customer->instahandler = $request->instahandler;
      $customer->rating = $request->rating;
      $customer->address = $request->address;
      $customer->city = $request->city;
      $customer->country = $request->country;

      $customer->save();

      return redirect()->route('customer.show', $id)->with('success', 'You have successfully updated the customer!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $customer = Customer::find($id);

      if (count($customer->leads) > 0 || count($customer->orders) > 0) {
        return redirect()->route('customer.index')->with('warning', 'You have related leads or orders to this customer');
      }

      $customer->delete();

      return redirect()->route('customer.index')->with('success', 'You have successfully deleted a customer');
    }
}
