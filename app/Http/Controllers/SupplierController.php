<?php

namespace App\Http\Controllers;

use App\Supplier;
use App\Agent;
use App\Setting;
use App\ReplyCategory;
use App\User;
use App\Helpers;
use App\Email;
use App\Mail\PurchaseEmail;
use App\ReadOnly\SoloNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $suppliers = Supplier::with('agents')->paginate(Setting::get('pagination'));
      $solo_numbers = (new SoloNumbers)->all();
      $term = $request->term ?? '';
      $type = $request->type ?? '';
      $source = $request->get('source') ?? '';
      $typeWhereClause = '';

      if ($type != '') {
        $typeWhereClause = ' AND has_error = 1';
      }

      $suppliers = DB::select('
									SELECT suppliers.id, suppliers.supplier, suppliers.phone, suppliers.source, suppliers.brands, suppliers.email, suppliers.default_email, suppliers.address, suppliers.social_handle, suppliers.gst, suppliers.is_flagged, suppliers.has_error, suppliers.status, 
                  (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
                  (SELECT mm2.created_at FROM chat_messages mm2 WHERE mm2.id = message_id) as message_created_at,
                  (SELECT mm3.id FROM purchases mm3 WHERE mm3.id = purchase_id) as purchase_id,
                  (SELECT mm4.created_at FROM purchases mm4 WHERE mm4.id = purchase_id) as purchase_created_at,
                  (SELECT mm5.message FROM emails mm5 WHERE mm5.id = email_id) as email_message,
                  (SELECT mm6.seen FROM emails mm6 WHERE mm6.id = email_id) as email_seen,
                  (SELECT mm7.created_at FROM emails mm7 WHERE mm7.id = email_id) as email_created_at,
                  CASE WHEN IFNULL(message_created_at, "1990-01-01 00:00") > IFNULL(email_created_at, "1990-01-01 00:00") THEN "message" WHEN IFNULL(message_created_at, "1990-01-01 00:00") < IFNULL(email_created_at, "1990-01-01 00:00") THEN "email" ELSE "none" END as last_type,
                  CASE WHEN IFNULL(message_created_at, "1990-01-01 00:00") > IFNULL(email_created_at, "1990-01-01 00:00") THEN message_created_at WHEN IFNULL(message_created_at, "1990-01-01 00:00") < IFNULL(email_created_at, "1990-01-01 00:00") THEN email_created_at ELSE "1990-01-01 00:00" END as last_communicated_at

                  FROM (SELECT * FROM suppliers

                  LEFT JOIN (SELECT MAX(id) as message_id, supplier_id, message, MAX(created_at) as message_created_at FROM chat_messages GROUP BY supplier_id ORDER BY created_at DESC) AS chat_messages
                  ON suppliers.id = chat_messages.supplier_id

                  LEFT JOIN (SELECT MAX(id) as purchase_id, supplier_id as purchase_supplier_id, created_at AS purchase_created_at FROM purchases GROUP BY purchase_supplier_id ORDER BY created_at DESC) AS purchases
                  ON suppliers.id = purchases.purchase_supplier_id

                  LEFT JOIN (SELECT MAX(id) as email_id, model_id as email_model_id, MAX(created_at) AS email_created_at FROM emails WHERE model_type LIKE "%Supplier%" OR "%Purchase%" GROUP BY model_id ORDER BY created_at DESC) AS emails
                  ON suppliers.id = emails.email_model_id)

                  AS suppliers

                  WHERE (source LIKE "%'.$source.'%" AND (supplier LIKE "%' . $term . '%" OR 
                  phone LIKE "%' . $term . '%" OR 
                  email LIKE "%' . $term . '%" OR 
                  address LIKE "%' . $term . '%" OR 
                  social_handle LIKE "%' . $term . '%" OR
                   id IN (SELECT model_id FROM agents WHERE model_type LIKE "%Supplier%" AND (name LIKE "%' . $term . '%" OR phone LIKE "%' . $term . '%" OR email LIKE "%' . $term . '%"))))' . $typeWhereClause . '
                  ORDER BY status DESC, is_flagged DESC, last_communicated_at DESC;
							');

      $suppliers_all = Supplier::where(function ($query) {
        $query->whereNotNull('email')->orWhereNotNull('default_email');
      })->get();

              // dd($suppliers);

      $currentPage = LengthAwarePaginator::resolveCurrentPage();
  		$perPage = Setting::get('pagination');
  		$currentItems = array_slice($suppliers, $perPage * ($currentPage - 1), $perPage);

  		$suppliers = new LengthAwarePaginator($currentItems, count($suppliers), $perPage, $currentPage, [
  			'path'	=> LengthAwarePaginator::resolveCurrentPath()
  		]);



      return view('suppliers.index', [
        'suppliers'     => $suppliers,
        'suppliers_all' => $suppliers_all,
        'solo_numbers'  => $solo_numbers,
        'term'          => $term,
        'type'          => $type,
          'source' => $source
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
        'gst'             => 'sometimes|nullable|max:255',
        'status' => 'required'
      ]);

      $data = $request->except('_token');
      $data['default_phone'] = $request->default_phone != '' ? $request->default_phone : $request->phone;
      $data['default_email'] = $request->default_email != '' ? $request->default_email : $request->email;

      Supplier::find($id)->update($data);

      return redirect()->back()->withSuccess('You have successfully updated a supplier!');
    }

    public function loadMoreMessages(Request $request, $id)
    {
      $supplier = Supplier::find($id);

      $chat_messages = $supplier->whatsapps()->skip(1)->take(3)->pluck('message');

      return response()->json([
        'messages'  => $chat_messages
      ]);
    }

    public function flag(Request $request)
    {
      $supplier = Supplier::find($request->supplier_id);

      if ($supplier->is_flagged == 0) {
        $supplier->is_flagged = 1;
      } else {
        $supplier->is_flagged = 0;
      }

      $supplier->save();

      return response()->json(['is_flagged' => $supplier->is_flagged]);
    }

    public function sendEmailBulk(Request $request)
    {
      $this->validate($request, [
        'subject' => 'required|min:3|max:255',
        'message' => 'required'
      ]);

      if ($request->suppliers) {
        $suppliers = Supplier::whereIn('id', $request->suppliers)->where(function ($query) {
          $query->whereNotNull('default_email')->orWhereNotNull('email');
        })->get();
      } else {
        if ($request->not_received != 'on' && $request->received != 'on') {
          return redirect()->route('supplier.index')->withErrors(['Please select either suppliers or option']);
        }
      }

      if ($request->not_received == 'on') {
        $suppliers = Supplier::doesnthave('emails')->where(function ($query) {
          $query->whereNotNull('default_email')->orWhereNotNull('email');
        })->get();
      }

      if ($request->received == 'on') {
        $suppliers = Supplier::whereDoesntHave('emails', function ($query) {
          $query->where('type', 'incoming');
        })->where(function ($query) {
          $query->whereNotNull('default_email')->orWhereNotNull('email');
        })->where('has_error', 0)->get();
      }

      // foreach ($suppliers as $supplier) {
      //   if ($supplier->email == '' && $supplier->default_email == '') {
      //     dump($supplier->id);
      //   }
      // }
      // dd('stop');

      // $first_email = '';
      // $bcc_emails = [];
      // foreach ($suppliers as $key => $supplier) {
      //   if ($key == 0) {
      //     $first_email = $supplier->default_email ?? $supplier->email;
      //   } else {
      //     $bcc_emails[] = $supplier->default_email ?? $supplier->email;
      //   }
      // }

      $file_paths = [];

      if ($request->hasFile('file')) {
        foreach ($request->file('file') as $file) {
          $filename = $file->getClientOriginalName();

          $file->storeAs("documents", $filename, 'files');

          $file_paths[] = "documents/$filename";
        }
      }


      foreach ($suppliers as $supplier) {
        Mail::to($supplier->default_email ?? $supplier->email)->send(new PurchaseEmail($request->subject, $request->message, $file_paths));

        $params = [
          'model_id'        => $supplier->id,
          'model_type'      => Supplier::class,
          'from'            => 'buying@amourint.com',
          'seen'            => 1,
          'to'              => $supplier->default_email ?? $supplier->email,
          'subject'         => $request->subject,
          'message'         => $request->message,
          'template'				=> 'customer-simple',
  				'additional_data'	=> json_encode(['attachment' => $file_paths])
        ];

        Email::create($params);
      }

      return redirect()->route('supplier.index')->withSuccess('You have successfully sent emails in bulk!');
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
      $supplier->whatsapps()->delete();

      $supplier->delete();

      return redirect()->route('supplier.index')->withSuccess('You have successfully deleted a supplier');
    }
}
