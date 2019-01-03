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
        case 'rating':
  					 $sortby = 'rating';
  					break;
        case 'communication':
  					 $sortby = 'communication';
  					break;
  			default :
  					 $sortby = 'communication';
  		}

      if ($sortby != 'communication' && $sortby != 'rating') {
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
  				$customers = array_values(array_sort($customers, function ($value) {
  						return $value['communication']['created_at'];
  				}));

  				$customers = array_reverse($customers);
  			} else {
  				$customers = array_values(array_sort($customers, function ($value) {
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
      // $leads = Leads::whereNotNull('contactno')->get()->groupBy('contactno');
      //
      // foreach ($leads as $number => $lead) {
      //   if ($customer = Customer::where('phone', $number)->first()) {
      //     foreach ($lead as $key => $item) {
      //       if ($item->address) {
      //         $customer->address = $item->address;
      //       }
      //
      //       if ($item->city) {
      //         $customer->city = $item->city;
      //       }
      //
      //       if ($item->rating) {
      //         $customer->rating = $item->rating;
      //       }
      //
      //       $customer->save();
      //     }
      //   }
      //
      // }
      //
      // $orders = Order::whereNotNull('contact_detail')->get()->groupBy('contact_detail');
      //
      // foreach ($orders as $number => $order) {
      //   if ($customer = Customer::where('phone', $number)->first()) {
      //     foreach ($order as $item) {
      //       if ($item->city) {
      //         $customer->city = $item->city;
      //       }
      //
      //       $customer->save();
      //     }
      //   }
      // }

      // $chat_messages_leads = Message::where('moduletype', 'leads')->get();
      // $chat_messages_orders = Message::where('moduletype', 'order')->get();
      //
      // foreach ($chat_messages_leads as $chat) {
      //   $lead = Leads::withTrashed()->whereNotNull('contactno')->where('id', $chat->moduleid)->first();
      //   if ($lead) {
      //     if ($customer = Customer::where('phone', $lead->contactno)->first()) {
      //       $chat->customer_id = $customer->id;
      //       $chat->save();
      //     } else {
      //       // dd($lead->contactno, 'no lead customer');
      //     }
      //
      //
      //   } else {
      //     // dd('no lead');
      //   }
      // }
      //
      // foreach ($chat_messages_orders as $chat) {
      //   $order = Order::withTrashed()->whereNotNull('contact_detail')->where('id', $chat->moduleid)->first();
      //   if ($order) {
      //     if ($customer = Customer::where('phone', $order->contact_detail)->first()) {
      //       $chat->customer_id = $customer->id;
      //       $chat->save();
      //     } else {
      //       // dd($order->contact_detail, 'no order customer');
      //     }
      //
      //
      //   } else {
      //     // dd('no lead');
      //   }
      // }

      $currentPage = LengthAwarePaginator::resolveCurrentPage();
  		$perPage = Setting::get('pagination');
  		$currentItems = array_slice($customers, $perPage * ($currentPage - 1), $perPage);

  		$customers = new LengthAwarePaginator($currentItems, count($customers), $perPage, $currentPage, [
  			'path'	=> LengthAwarePaginator::resolveCurrentPath()
  		]);

      return view('customers.index', [
        'customers' => $customers,
        'term' => $term,
        'orderby' => $orderby,
      ]);
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
        'email'         => 'sometimes|email',
        'phone'         => 'required|unique:customers|numeric',
        'instahandler'  => 'sometimes|min:3|max:255',
        'rating'        => 'required|numeric',
        'address'       => 'sometimes|min:3|max:255',
        'city'          => 'sometimes|min:3|max:255',
        'country'       => 'sometimes|min:3|max:255'
      ]);

      $customer = new Customer;

      $customer->name = $request->name;
      $customer->email = $request->email;
      $customer->phone = $request->phone;
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

      // $leads = Leads::find($id);
      $status = (New status)->all();
      // $data = $status->all();
      // $sales_persons = Helpers::getUsersArrayByRole( 'Sales' );
      // $leads['statusid'] = $data;
      $users = User::all()->toArray();
      $users_array = Helpers::getUserArray(User::all());
      // $leads['users']  = $users;
      $brands = Brand::all()->toArray();
      $approval_replies = Reply::where('model', 'Approval Lead')->get();
      $internal_replies = Reply::where('model', 'Internal Lead')->get();
      // $leads['brands']  = $brands;
      // $leads['selected_products_array'] = json_decode( $leads['selected_product'] );
      // $leads['products_array'] = [];
      // $leads['recordings'] = CallRecording::where('lead_id', $leads->id)->get()->toArray();
      // $tasks = Task::where('model_type', 'leads')->where('model_id', $id)->get()->toArray();
      // $approval_replies = Reply::where('model', 'Approval Lead')->get();
      // $internal_replies = Reply::where('model', 'Internal Lead')->get();

    // $leads['multi_brand'] = is_array(json_decode($leads['multi_brand'],true) ) ? json_decode($leads['multi_brand'],true) : [];

    // $leads['remark'] = $leads->remark;

      // $messages = Message::all()->where('moduleid','=', $leads['id'])->where('moduletype','=', 'leads')->sortByDesc("created_at")->take(10)->toArray();
      // $leads['messages'] = $messages;

      // if ( ! empty( $leads['selected_products_array']  ) ) {
      //     foreach ( $leads['selected_products_array']  as $product_id ) {
      //         $skuOrName                             = $this->getProductNameSkuById( $product_id );
      //
      //        $data['products_array'][$product_id] = $skuOrName;
      //     }
      // }

      // $users_array = Helpers::getUserArray(User::all());

      // $selected_categories = $leads['multi_category'];

      return view('customers.show', [
        'customer'  => $customer,
        'status'    => $status,
        'brands'    => $brands,
        'users'     => $users,
        'users_array'     => $users_array,
        'approval_replies'     => $approval_replies,
        'internal_replies'     => $internal_replies,
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
        //
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
        //
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
