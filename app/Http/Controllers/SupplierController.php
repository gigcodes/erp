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
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      // $suppliers = Supplier::with('agents')->paginate(Setting::get('pagination'));
      $solo_numbers = (new SoloNumbers)->all();

      $suppliers = DB::select('
									SELECT suppliers.id, suppliers.supplier, suppliers.phone, suppliers.email, suppliers.address, suppliers.social_handle, suppliers.gst,
                  (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
                  (SELECT mm2.created_at FROM chat_messages mm2 WHERE mm2.id = message_id) as message_created_at,
                  (SELECT mm3.id FROM purchases mm3 WHERE mm3.id = purchase_id) as purchase_id,
                  (SELECT mm4.created_at FROM purchases mm4 WHERE mm4.id = purchase_id) as purchase_created_at,
                  (SELECT mm5.message FROM emails mm5 WHERE mm5.id = email_id) as email_message,
                  (SELECT mm6.created_at FROM emails mm6 WHERE mm6.id = email_id) as email_created_at

                  FROM (SELECT * FROM suppliers

                  LEFT JOIN (SELECT MAX(id) as message_id, supplier_id, message, MAX(created_at) as message_created_At FROM chat_messages GROUP BY supplier_id ORDER BY created_at DESC) AS chat_messages
                  ON suppliers.id = chat_messages.supplier_id

                  LEFT JOIN (SELECT MAX(id) as purchase_id, supplier_id as purchase_supplier_id, created_at AS purchase_created_at FROM purchases GROUP BY purchase_supplier_id ORDER BY created_at DESC) AS purchases
                  ON suppliers.id = purchases.purchase_supplier_id

                  LEFT JOIN (SELECT MAX(id) as email_id, model_id as email_model_id, created_at AS email_created_at FROM emails WHERE model_type LIKE "%Supplier%" OR "%Purchase%" GROUP BY model_id ORDER BY created_at DESC) AS emails
                  ON suppliers.id = emails.email_model_id)

                  AS suppliers ORDER BY purchase_created_at DESC, message_created_at DESC, email_created_at DESC;
							');

              // dd($suppliers);

      $currentPage = LengthAwarePaginator::resolveCurrentPage();
  		$perPage = Setting::get('pagination');
  		$currentItems = array_slice($suppliers, $perPage * ($currentPage - 1), $perPage);

  		$suppliers = new LengthAwarePaginator($currentItems, count($suppliers), $perPage, $currentPage, [
  			'path'	=> LengthAwarePaginator::resolveCurrentPath()
  		]);

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
