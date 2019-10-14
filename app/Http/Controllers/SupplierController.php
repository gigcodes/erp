<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\SupplierCategoryCount;
use App\Brand;
use App\SupplierBrandCount;
use App\Supplier;
use App\Agent;
use App\Setting;
use App\ReplyCategory;
use App\User;
use App\Helpers;
use App\Email;
use App\SupplierCategory;
use App\SupplierStatus;
use App\Mail\PurchaseEmail;
use App\ReadOnly\SoloNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\LengthAwarePaginator;
use App\ProductQuicksellGroup;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use App\SupplierBrandCountHistory;

class SupplierController extends Controller
{
    /**
     * Add/Edit Remainder functionality
     */
    public function updateReminder(Request $request)
    {
        $supplier = Supplier::find($request->get('supplier_id'));
        $supplier->frequency = $request->get('frequency');
        $supplier->reminder_message = $request->get('message');
        $supplier->save();

        return response()->json([
            'success'
        ]);
    }

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
        //$status = $request->status ?? '';
        $supplier_category_id = $request->supplier_category_id ?? '';
        $supplier_status_id = $request->supplier_status_id ?? '';
        $source = $request->get('source') ?? '';
        $typeWhereClause = '';

        if ($type != '' && $type == 'has_error') {
            $typeWhereClause = ' AND has_error = 1';
        }
        if ($type != '' && $type == 'not_updated') {
            $typeWhereClause = ' AND is_updated = 0';
        }
        if ($type != '' && $type == 'updated') {
            $typeWhereClause = ' AND is_updated = 1';
        }

        /*if ( $status != '' ) {
          $typeWhereClause .= ' AND status=1';
        }*/

        if ($supplier_category_id != '') {
            $typeWhereClause .= ' AND supplier_category_id=' . $supplier_category_id;
        }
        if ($supplier_status_id != '') {
            $typeWhereClause .= ' AND supplier_status_id=' . $supplier_status_id;
        }

        $suppliers = DB::select('
									SELECT suppliers.frequency, suppliers.reminder_message, suppliers.id, suppliers.supplier, suppliers.phone, suppliers.source, suppliers.brands, suppliers.email, suppliers.default_email, suppliers.address, suppliers.social_handle, suppliers.gst, suppliers.is_flagged, suppliers.has_error, suppliers.status, suppliers.scraper_name, suppliers.supplier_category_id, suppliers.supplier_status_id, suppliers.inventory_lifetime,
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
                  WHERE (source LIKE "%' . $source . '%" AND (supplier LIKE "%' . $term . '%" OR 
                  phone LIKE "%' . $term . '%" OR 
                  email LIKE "%' . $term . '%" OR 
                  address LIKE "%' . $term . '%" OR 
                  social_handle LIKE "%' . $term . '%" OR
                  scraper_name LIKE "%' . $term . '%" OR
                  brands LIKE "%' . $term . '%" OR
                   suppliers.id IN (SELECT model_id FROM agents WHERE model_type LIKE "%Supplier%" AND (name LIKE "%' . $term . '%" OR phone LIKE "%' . $term . '%" OR email LIKE "%' . $term . '%"))))' . $typeWhereClause . '
                  ORDER BY last_communicated_at DESC, status DESC
							');

        $suppliers_all = Supplier::where(function ($query) {
            $query->whereNotNull('email')->orWhereNotNull('default_email');
        })->get();

        // print_r($suppliers_all);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        $currentItems = array_slice($suppliers, $perPage * ($currentPage - 1), $perPage);

        $supplierscnt = count($suppliers);
        $suppliers = new LengthAwarePaginator($currentItems, count($suppliers), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);

        $suppliercategory = SupplierCategory::pluck('name', 'id')->toArray();
        $supplierstatus = SupplierStatus::pluck('name', 'id')->toArray();

        //SELECT supplier_status_id, COUNT(*) AS number_of_products FROM suppliers WHERE supplier_status_id IN (SELECT id from supplier_status) GROUP BY supplier_status_id
        $statistics = DB::select('SELECT supplier_status_id, ss.name, COUNT(*) AS number_of_products FROM suppliers s LEFT join supplier_status ss on ss.id = s.supplier_status_id WHERE supplier_status_id IN (SELECT id from supplier_status) GROUP BY supplier_status_id');

        return view('suppliers.index', [
            'suppliers' => $suppliers,
            'suppliers_all' => $suppliers_all,
            'solo_numbers' => $solo_numbers,
            'term' => $term,
            'type' => $type,
            'source' => $source,
            'suppliercategory' => $suppliercategory,
            'supplierstatus' => $supplierstatus,
            'supplier_category_id' => $supplier_category_id,
            'supplier_status_id' => $supplier_status_id,
            'count' => $supplierscnt,
            'statistics' => $statistics,
            'total' => 0
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            //'supplier_category_id' => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
            'address' => 'sometimes|nullable|string',
            'phone' => 'sometimes|nullable|numeric',
            'default_phone' => 'sometimes|nullable|numeric',
            'whatsapp_number' => 'sometimes|nullable|numeric',
            'email' => 'sometimes|nullable|email',
            'social_handle' => 'sometimes|nullable',
            'scraper_name' => 'sometimes|nullable',
            'inventory_lifetime' => 'sometimes|nullable',
            'gst' => 'sometimes|nullable|max:255',
            //'supplier_status_id' => 'required'
        ]);

        $data = $request->except('_token');
        $data[ 'default_phone' ] = $request->phone ?? '';
        $data[ 'default_email' ] = $request->email ?? '';

        Supplier::create($data);

        return redirect()->route('supplier.index')->withSuccess('You have successfully saved a supplier!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supplier = Supplier::find($id);
        $reply_categories = ReplyCategory::all();
        $users_array = Helpers::getUserArray(User::all());
        $emails = [];
        $suppliercategory = SupplierCategory::pluck('name', 'id');
        $supplierstatus = SupplierStatus::pluck('name', 'id');

        return view('suppliers.show', [
            'supplier' => $supplier,
            'reply_categories' => $reply_categories,
            'users_array' => $users_array,
            'emails' => $emails,
            'suppliercategory' => $suppliercategory,
            'supplierstatus' => $supplierstatus,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            //'supplier_category_id'        => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
            'address' => 'sometimes|nullable|string',
            'phone' => 'sometimes|nullable|numeric',
            'default_phone' => 'sometimes|nullable|numeric',
            'whatsapp_number' => 'sometimes|nullable|numeric',
            'email' => 'sometimes|nullable|email',
            'default_email' => 'sometimes|nullable|email',
            'social_handle' => 'sometimes|nullable',
            'scraper_name' => 'sometimes|nullable',
            'inventory_lifetime' => 'sometimes|nullable',
            'gst' => 'sometimes|nullable|max:255',
            //'supplier_status_id' => 'required'
            //'status' => 'required'
        ]);

        $data = $request->except('_token');
        $data[ 'default_phone' ] = $request->default_phone != '' ? $request->default_phone : $request->phone;
        $data[ 'default_email' ] = $request->default_email != '' ? $request->default_email : $request->email;
        $data[ 'is_updated' ] = 1;
        Supplier::find($id)->update($data);

        return redirect()->back()->withSuccess('You have successfully updated a supplier!');
    }

    /**
     * Ajax Load More message method
     */
    public function loadMoreMessages(Request $request, $id)
    {
        $supplier = Supplier::find($id);

        $chat_messages = $supplier->whatsapps()->skip(1)->take(3)->pluck('message');

        return response()->json([
            'messages' => $chat_messages
        ]);
    }

    /**
     * Ajax Flag Update method
     */
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

    /**
     * Send Bulk email to supplier
     */
    public function sendEmailBulk(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required|min:3|max:255',
            'message' => 'required',
            'cc.*' => 'nullable|email',
            'bcc.*' => 'nullable|email'
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

        $cc = $bcc = [];
        if ($request->has('cc')) {
            $cc = array_values(array_filter($request->cc));
        }
        if ($request->has('bcc')) {
            $bcc = array_values(array_filter($request->bcc));
        }

        foreach ($suppliers as $supplier) {
            $mail = Mail::to($supplier->default_email ?? $supplier->email);

            if ($cc) {
                $mail->cc($cc);
            }
            if ($bcc) {
                $mail->bcc($bcc);
            }

            $mail->send(new PurchaseEmail($request->subject, $request->message, $file_paths));

            $params = [
                'model_id' => $supplier->id,
                'model_type' => Supplier::class,
                'from' => 'buying@amourint.com',
                'seen' => 1,
                'to' => $supplier->default_email ?? $supplier->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'template' => 'customer-simple',
                'additional_data' => json_encode(['attachment' => $file_paths]),
                'cc' => $cc ? : null,
                'bcc' => $bcc ? : null,
            ];

            Email::create($params);
        }

        return redirect()->route('supplier.index')->withSuccess('You have successfully sent emails in bulk!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supplier = Supplier::find($id);

//      $supplier->agents()->delete();
//      $supplier->whatsapps()->delete();

        $supplier->delete();

        return redirect()->route('supplier.index')->withSuccess('You have successfully deleted a supplier');
    }

    /**
     * Add Notes method
     */
    public function addNote($id, Request $request)
    {
        $supplier = Supplier::findOrFail($id);
        $notes = $supplier->notes;
        if (!is_array($notes)) {
            $notes = [];
        }

        $notes[] = $request->get('note');
        $supplier->notes = $notes;
        $supplier->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function supplierupdate(Request $request)
    {
        $supplier = Supplier::find($request->get('supplier_id'));
        $supplier->frequency = $request->get('id');
        $type = $request->get('type');
        if ($type == 'category') {
            $supplier->supplier_category_id = $request->get('id');
        }
        if ($type == 'status') {
            $supplier->supplier_status_id = $request->get('id');
        }
        $supplier->save();
        return response()->json([
            'success'
        ]);
    }

    public function getsuppliers(Request $request)
    {

        $input = $request->all();

        $supplier_category_id = $input[ 'supplier_category_id' ];

        $supplier_status_id = $input[ 'supplier_status_id' ];

        $filter = $input[ 'filter' ];

        $data = '';
        $typeWhereClause = '';
        $suppliers_all = array();
        if ($supplier_category_id == '' && $supplier_status_id == '') {
            /* $suppliers_all = Supplier::where(function ($query) {
             $query->whereNotNull('email')->orWhereNotNull('default_email');
           })->get();*/
        } else {
            if ($supplier_category_id != '') {
                $typeWhereClause .= ' AND supplier_category_id=' . $supplier_category_id;
            }
            if ($supplier_status_id != '') {
                $typeWhereClause .= ' AND supplier_status_id=' . $supplier_status_id;
            }

            if ($filter != '') {
                $typeWhereClause .= ' AND supplier like "' . $filter . '%"';
            }
            $suppliers_all = DB::select('SELECT suppliers.id, suppliers.supplier, suppliers.email, suppliers.default_email from suppliers WHERE email != "" ' . $typeWhereClause . '');
        }

        if (count($suppliers_all) > 0) {

            foreach ($suppliers_all as $supplier) {
                $data .= '<option value="' . $supplier->id . '">' . $supplier->supplier . ' - ' . $supplier->default_email . ' / ' . $supplier->email . '</option>';
            }
        }
        return $data;
    }

    public function addSupplierCategoryCount()
    {

        $suppliercount = SupplierCategoryCount::all();
        $category_parent = Category::where('parent_id', 0)->get();
        $category_child = Category::where('parent_id', '!=', 0)->get();
        $supplier = Supplier::where('supplier_status_id', 1)->orderby('supplier','asc')->get();

        return view('suppliers.supplier_category_count', compact('supplier', 'suppliercount', 'category_parent', 'category_child'));
    }

    public function saveSupplierCategoryCount(Request $request)
    {
        $category_id = $request->category_id;
        $supplier_id = $request->supplier_id;
        $count = $request->count;

        $data[ 'category_id' ] = $category_id;
        $data[ 'supplier_id' ] = $supplier_id;
        $data[ 'cnt' ] = $count;
        SupplierCategoryCount::create($data);

        return 'Saved SucessFully';
    }

    public function getSupplierCategoryCount()
    {

        $suppliercount = SupplierCategoryCount::all();
        $supplier_list = Supplier::where('supplier_status_id', 1)->orderby('supplier','asc')->get();
        $category_parent = Category::where('parent_id', 0)->get();
        $category_child = Category::where('parent_id', '!=', 0)->get();


        foreach ($suppliercount as $supplier) {
            $sup = "";
            foreach ($supplier_list as $v) {
                if ($v->id == $supplier->supplier_id) {
                    $sup .= '<option value="' . $v->id . '" selected>' . $v->supplier . '</option>';
                } else {
                    $sup .= '<option value="' . $v->id . '">' . $v->supplier . '</option>';
                }
            }

            $cat = "";
            foreach ($category_parent as $c) {
                if ($c->id == $supplier->category_id) {
                    $cat .= '<option value="' . $c->id . '" selected>' . $c->title . '</option>';
                } else {
                    $cat .= '<option value="' . $c->id . '">' . $c->title . '</option>';
                    if ($c->childs) {
                        foreach ($c->childs as $categ) {
                            $cat .= '<option value="' . $categ->id . '">-&nbsp;' . $categ->title . '</option>';
                        }
                    }
                }
            }
            foreach ($category_child as $c) {
                if ($c->id == $supplier->category_id) {
                    $cat .= '<option value="' . $c->id . '" selected>' . $c->title . '</option>';
                } else {
                    $cat .= '<option value="' . $c->id . '">' . $c->title . '</option>';
                    if ($c->childs) {
                        foreach ($c->childs as $categ) {
                            $cat .= '<option value="' . $categ->id . '">-&nbsp;' . $categ->title . '</option>';
                        }
                    }
                }
            }


            $sub_array = array();
            $sub_array[] = '<select class="form-control update" data-column="supplier_id" data-id="' . $supplier[ "id" ] . '">' . $sup . '</select>';
            $sub_array[] = '<select class="form-control update" data-id="' . $supplier[ "id" ] . '" data-column="category_id">' . $cat . '</select>';
            $sub_array[] = '<input type="number"  data-id="' . $supplier[ "id" ] . '" data-column="cnt" value="' . $supplier[ "cnt" ] . '"  class="form-control update">';
            $sub_array[] = '<button type="button" name="delete" class="btn btn-danger btn-xs delete" id="' . $supplier[ "id" ] . '">Delete</button>';
            $data[] = $sub_array;
        }
        if (!empty($data)) {
            $output = array(
                "draw" => 0,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => $data
            );
        } else {
            $output = array(
                "draw" => 0,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            );
        }


        return json_encode($output);

    }

    public function updateSupplierCategoryCount(Request $request)
    {
        $id = $request->id;
        $column_name = $request->column_name;
        $value = $request->value;
        $suppliercount = SupplierCategoryCount::findorfail($request->id);
        $suppliercount->$column_name = $value;
        $suppliercount->update();
        return 'Data Updated';

    }

    public function deleteSupplierCategoryCount(Request $request)
    {
        $id = $request->id;
        $suppliercpunt = SupplierCategoryCount::findorfail($id);
        if ($suppliercpunt) {
            SupplierCategoryCount::destroy($id);
        }
        return 'Data Deleted';
    }

    public function addSupplierBrandCount()
    {

        $suppliercount = SupplierBrandCount::all();
        $brand = Brand::orderby('name', 'asc')->get();
        $supplier = Supplier::where('supplier_status_id', 1)->orderby('supplier','asc')->get();
        $category_parent = Category::where('parent_id', 0)->get();
        $category_child = Category::where('parent_id', '!=', 0)->get();

        return view('suppliers.supplier_brand_count', compact('supplier', 'suppliercount', 'brand','category_parent','category_child'));
    }

    public function saveSupplierBrandCount(Request $request)
    {
        $brand_id = $request->brand_id;
        $supplier_id = $request->supplier_id;
        $count = $request->count;
        $url = $request->url;
        $category_id = $request->category_id;

        $data[ 'brand_id' ] = $brand_id;
        $data[ 'supplier_id' ] = $supplier_id;
        $data[ 'cnt' ] = $count;
        $data[ 'url' ] = $url;
        $data[ 'category_id' ] = $category_id;

        SupplierBrandCount::create($data);

        return 'Saved SucessFully';
    }

    public function getSupplierBrandCount()
    {
        $suppliercount = SupplierBrandCount::all();
        $supplier_list = Supplier::where('supplier_status_id', 1)->orderby('supplier','asc')->get();
        $brand_list = Brand::orderby('name', 'asc')->get();
        $category_parent = Category::where('parent_id', 0)->orderby('title','asc')->get();
        $category_child = Category::where('parent_id', '!=', 0)->orderby('title','asc')->get();


        foreach ($suppliercount as $supplier) {
            $sup = "";

            foreach ($supplier_list as $v) {

                if ($v->id == $supplier->supplier_id) {
                    $sup .= '<option value="' . $v->id . '" selected>' . $v->supplier . '</option>';
                } else {
                    $sup .= '<option value="' . $v->id . '">' . $v->supplier . '</option>';
                }
            }

            $brands = "";
            foreach ($brand_list as $v) {
                if ($v->id == $supplier->brand_id) {
                    $brands .= '<option value="' . $v->id . '" selected>' . $v->name . '</option>';
                } else {
                    $brands .= '<option value="' . $v->id . '">' . $v->name . '</option>';
                }
            }

            $cat = "";
            $cat .=  '<option>Select Category</option>';
            foreach ($category_parent as $c) {
                if ($c->id == $supplier->category_id) {
                    $cat .= '<option value="' . $c->id . '" selected>' . $c->title . '</option>';
                } else {
                    $cat .= '<option value="' . $c->id . '">' . $c->title . '</option>';
                    if ($c->childs) {
                        foreach ($c->childs as $categ) {
                            $cat .= '<option value="' . $categ->id . '">-&nbsp;' . $categ->title . '</option>';
                        }
                    }
                }
            }
            foreach ($category_child as $c) {
                if ($c->id == $supplier->category_id) {
                    $cat .= '<option value="' . $c->id . '" selected>' . $c->title . '</option>';
                } else {
                    $cat .= '<option value="' . $c->id . '">' . $c->title . '</option>';
                    if ($c->childs) {
                        foreach ($c->childs as $categ) {
                            $cat .= '<option value="' . $categ->id . '">-&nbsp;' . $categ->title . '</option>';
                        }
                    }
                }
            }


            $sub_array = array();
            $sub_array[] = '<select disabled class="form-control">' . $sup . '</select>';
            $sub_array[] = '<select class="form-control" disabled>' . $cat . '</select>';
            $sub_array[] = '<select disabled class="form-control">' . $brands . '</select>';
            $sub_array[] = '<input type="number"  data-id="' . $supplier[ "id" ] . '" data-column="cnt" value="' . $supplier[ "cnt" ] . '"  class="form-control update">';
            $sub_array[] = $supplier[ "url" ];
            $sub_array[] = '<button type="button" name="delete" class="btn btn-danger btn-xs delete" id="' . $supplier[ "id" ] . '">Delete</button>';
            $data[] = $sub_array;
        }
        if (!empty($data)) {
            $output = array(
                "draw" => 0,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => $data
            );
        } else {
            $output = array(
                "draw" => 0,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            );
        }


        return json_encode($output);

    }

    public function updateSupplierBrandCount(Request $request)
    {
        $id = $request->id;
        $column_name = $request->column_name;
        $value = $request->value;
        $suppliercount = SupplierBrandCount::findorfail($request->id);

        // Update in history
        $history = new SupplierBrandCountHistory();
        $history->supplier_brand_count_id = $suppliercount->id;
        $history->supplier_id = $suppliercount->supplier_id;
        $history->brand_id = $suppliercount->brand_id;
        $history->cnt = $suppliercount->cnt;
        $history->url = $suppliercount->url;
        $history->category_id = $suppliercount->category_id;
        $history->save();
        //Update the value
        $suppliercount->$column_name = $value;
        $suppliercount->update();
        return 'Data Updated';

    }

    public function deleteSupplierBrandCount(Request $request)
    {
        $id = $request->id;
        $suppliercount = SupplierBrandCount::findorfail($id);
        if ($suppliercount) {
            // Update in history
            $history = new SupplierBrandCountHistory();
            $history->supplier_brand_count_id = $suppliercount->id;
            $history->supplier_id = $suppliercount->supplier_id;
            $history->brand_id = $suppliercount->brand_id;
            $history->cnt = $suppliercount->cnt;
            $history->url = $suppliercount->url;
            $history->category_id = $suppliercount->category_id;
            $history->save();
            SupplierBrandCount::destroy($id);
        }
        return 'Data Deleted';
    }

    public function saveImage(Request $request)
    {
        // Only create Product
        if ($request->type == 1) {
            $images = $request->checkbox;
            if ($images) {
                foreach ($images as $image) {
                    //getting prodct
                    $product = Product::select('sku')->where('sku', 'LIKE', '%QuickSell%')->orderBy('id', 'desc')->first();
                    if ($product) {
                        @$number = 'QUICKSELL' . (int) str_replace('QUICKSELL', '', $product->sku) + 1;
                    } else {
                        $number = date('yz') . sprintf('%02d', 1);
                    }

                    $product = new Product;

                    $product->name = 'QUICKSELL';
                    $product->sku = 'QuickSell' . $number;
                    $product->size = '';
                    $product->brand = 3;
                    $product->color = '';
                    $product->location = '';
                    $product->category = '';
                    $product->supplier = 'QUICKSELL';
                    $product->price = 0;
                    $product->stock = 1;
                    $product->quick_product = 1;
                    $product->isPending = 1;
                    $product->save();
                    return redirect()->back()->withSuccess('You have successfully saved product(s)!');

                }
                return redirect()->back()->withSuccess('You have successfully saved product(s)!');
            }
            return redirect()->back()->withSuccess('Please Select Image');
        } else {
            // Create Group ID with Product
            $images = $request->checkbox;
            if ($images) {
                // Loop Over Images
                foreach ($images as $image) {
                    //Getting the last created QUICKSELL
                    // MariaDB 10.0.5 and higher: $product = Product::select('sku')->where('sku', 'LIKE', '%QuickSell%')->whereRaw("REGEXP_REPLACE(products.sku, '[a-zA-Z]+', '') > 0")->orderBy('id', 'desc')->first();
                    $product = Product::select('sku')->where('sku', 'LIKE', '%QUICKSELL' . date('yz') . '%')->orderBy('id', 'desc')->first();
                    if ($product) {
                        @$number = 'QUICKSELL' . (int) str_replace('QUICKSELL', '', $product->sku) + 1;
                    } else {
                        $number = date('yz') . sprintf('%02d', 1);
                    }
                    $product = new Product;

                    $product->name = 'QUICKSELL';
                    $product->sku = 'QuickSell' . $number;
                    $product->size = '';
                    $product->brand = 3;
                    $product->color = '';
                    $product->location = '';
                    $product->category = '';
                    $product->supplier = 'QUICKSELL';
                    $product->price = 0;
                    $product->stock = 1;
                    $product->quick_product = 1;
                    $product->isPending = 1;
                    $product->save();
                    // if Product is true
                    if ($product == true) {
                        //Finding last created Product using sku
                        $product_id = Product::where('sku', $product->sku)->first();
                        if ($product_id != null) {
                            $id = $product_id->id;
                            //getting last group id
                            $group_id = ProductQuicksellGroup::select('quicksell_group_id')->orderBy('id', 'desc')->first();
                            if ($group_id != null) {
                                $number = $group_id->quicksell_group_id;
                                //Increment Group id
                                $number++;
                            } else {
                                $number = 1;
                            }
                            $group = new ProductQuicksellGroup();
                            $group->product_id = $id;
                            $group->quicksell_group_id = $number;
                            $group->save();

                        }
                    }
                }
                return redirect()->back()->withSuccess('You have successfully saved product(s)!');
            }
            return redirect()->back()->withSuccess('Please Select Image');
        }
    }
}
