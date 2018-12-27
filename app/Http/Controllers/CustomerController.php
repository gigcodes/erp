<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\Setting;
use App\Leads;
use App\Order;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $customers = Customer::paginate(Setting::get('pagination'));

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

      return view('customers.index')->withCustomers($customers);
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
        'instahandler'  => 'sometimes|min:3|max:255'
      ]);

      $customer = new Customer;

      $customer->name = $request->name;
      $customer->email = $request->email;
      $customer->phone = $request->phone;
      $customer->instahandler = $request->instahandler;

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

      return view('customers.show')->withCustomer($customer);
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
