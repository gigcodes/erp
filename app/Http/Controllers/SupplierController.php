<?php

namespace App\Http\Controllers;

use App\Supplier;
use App\Agent;
use App\Setting;
use App\ReplyCategory;
use App\User;
use App\Helpers;
use App\ReadOnly\SoloNumbers;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $suppliers = Supplier::with('agents')->paginate(Setting::get('pagination'));
      $solo_numbers = (new SoloNumbers)->all();

      return view('suppliers.index', [
        'suppliers' => $suppliers,
        'solo_numbers' => $solo_numbers
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        'supplier'        => 'required|string|max:255',
        'address'         => 'sometimes|nullable|string',
        'phone'           => 'sometimes|nullable|numeric',
        'default_phone'   => 'sometimes|nullable|numeric',
        'whatsapp_number' => 'sometimes|nullable|numeric',
        'email'           => 'sometimes|nullable|email',
        'social_handle'   => 'sometimes|nullable',
        'gst'             => 'sometimes|nullable|max:255'
      ]);

      $data = $request->except('_token');
      $data['default_phone'] = $request->phone ?? '';
      $data['default_email'] = $request->email ?? '';

      Supplier::create($data);

      return redirect()->route('supplier.index')->withSuccess('You have successfully saved a supplier!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $supplier = Supplier::find($id);
      $reply_categories = ReplyCategory::all();
      $users_array = Helpers::getUserArray(User::all());
      $emails = [];

      return view('suppliers.show', [
        'supplier'  => $supplier,
        'reply_categories'  => $reply_categories,
        'users_array'  => $users_array,
        'emails'  => $emails
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
      $this->validate($request, [
        'supplier'        => 'required|string|max:255',
        'address'         => 'sometimes|nullable|string',
        'phone'           => 'sometimes|nullable|numeric',
        'default_phone'   => 'sometimes|nullable|numeric',
        'whatsapp_number' => 'sometimes|nullable|numeric',
        'email'           => 'sometimes|nullable|email',
        'default_email'   => 'sometimes|nullable|email',
        'social_handle'   => 'sometimes|nullable',
        'gst'             => 'sometimes|nullable|max:255'
      ]);

      $data = $request->except('_token');
      $data['default_phone'] = $request->default_phone != '' ? $request->default_phone : $request->phone;
      $data['default_email'] = $request->default_email != '' ? $request->default_email : $request->email;

      Supplier::find($id)->update($data);

      return redirect()->route('supplier.index')->withSuccess('You have successfully updated a supplier!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $supplier = Supplier::find($id);

      $supplier->agents()->delete();

      $supplier->delete();

      return redirect()->route('supplier.index')->withSuccess('You have successfully deleted a supplier');
    }
}
