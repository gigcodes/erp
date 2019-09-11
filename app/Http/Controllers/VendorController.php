<?php

namespace App\Http\Controllers;

use App\Vendor;
use App\VendorProduct;
use App\VendorCategory;
use App\Setting;
use App\ReplyCategory;
use App\Helpers;
use App\User;
use Illuminate\Http\Request;
use Plank\Mediable\Media;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Illuminate\Pagination\LengthAwarePaginator;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() {
    //  $this->middleware('permission:vendor-all');
    }

    public function updateReminder(Request $request) {
        $vendor = Vendor::find($request->get('vendor_id'));
        $vendor->frequency = $request->get('frequency');
        $vendor->reminder_message = $request->get('message');
        $vendor->save();

        return response()->json([
            'success'
        ]);
    }

    public function index(Request $request)
    {
      // $vendors = Vendor::with('agents')->latest()->paginate(Setting::get('pagination'));

      $term = $request->term ?? '';
      $sortByClause = '';
      $orderby = 'DESC';

      if ($request->orderby == '') {
         $orderby = 'ASC';
      }

      if ($request->sortby == 'category') {
        $sortByClause = "category_name $orderby,";
      }
        $whereArchived = ' `deleted_at` IS NULL ';

      if ($request->get('with_archived') == 'on') {
          $whereArchived = '  `deleted_at` IS NOT NULL  ';
      }

      // $type = $request->type ?? '';
      // $typeWhereClause = '';
      //
      // if ($type != '') {
      //   $typeWhereClause = ' AND has_error = 1';
      // }

      $vendors = DB::select('
									SELECT *,
                  (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
                  (SELECT mm2.status FROM chat_messages mm2 WHERE mm2.id = message_id) as message_status,
                  (SELECT mm3.created_at FROM chat_messages mm3 WHERE mm3.id = message_id) as message_created_at

                  FROM (SELECT vendors.id, vendors.frequency, vendors.reminder_message, vendors.category_id, vendors.name, vendors.phone, vendors.email, vendors.address, vendors.social_handle, vendors.website, vendors.login, vendors.password, vendors.gst, vendors.account_name, vendors.account_iban, vendors.account_swift,
                  category_name,
                  chat_messages.message_id FROM vendors

                  LEFT JOIN (SELECT MAX(id) as message_id, vendor_id FROM chat_messages GROUP BY vendor_id ORDER BY created_at DESC) AS chat_messages
                  ON vendors.id = chat_messages.vendor_id

                  LEFT JOIN (SELECT id, title AS category_name FROM vendor_categories) AS vendor_categories
                  ON vendors.category_id = vendor_categories.id WHERE '. $whereArchived . '
                  )

                  AS vendors

                  WHERE (name LIKE "%' . $term . '%" OR
                  phone LIKE "%' . $term . '%" OR
                  email LIKE "%' . $term . '%" OR
                  address LIKE "%' . $term . '%" OR
                  social_handle LIKE "%' . $term . '%" OR
                  category_id IN (SELECT id FROM vendor_categories WHERE title LIKE "%' . $term . '%") OR
                   id IN (SELECT model_id FROM agents WHERE model_type LIKE "%Vendor%" AND (name LIKE "%' . $term . '%" OR phone LIKE "%' . $term . '%" OR email LIKE "%' . $term . '%")))
                  ORDER BY ' . $sortByClause . ' message_created_at DESC;
							');

              // dd($vendors);

      $currentPage = LengthAwarePaginator::resolveCurrentPage();
  		$perPage = Setting::get('pagination');
  		$currentItems = array_slice($vendors, $perPage * ($currentPage - 1), $perPage);

  		$vendors = new LengthAwarePaginator($currentItems, count($vendors), $perPage, $currentPage, [
  			'path'	=> LengthAwarePaginator::resolveCurrentPath()
  		]);

      $vendor_categories = VendorCategory::all();

      $users = User::all();

      return view('vendors.index', [
        'vendors' => $vendors,
        'vendor_categories' => $vendor_categories,
        'term'    => $term,
        'orderby'    => $orderby,
          'users' => $users
      ]);
    }

    public function assignUserToCategory(Request $request) {
        $user = $request->get('user_id');
        $category = $request->get('category_id');

        $category = VendorCategory::find($category);
        $category->user_id = $user;
        $category->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function product()
    {
      $products = VendorProduct::with('vendor')->latest()->paginate(Setting::get('pagination'));
      $vendors = Vendor::select(['id', 'name'])->get();

      return view('vendors.product', [
        'products'  => $products,
        'vendors'  => $vendors
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
        'category_id'   => 'sometimes|nullable|numeric',
        'name'          => 'required|string|max:255',
        'address'       => 'sometimes|nullable|string',
        'phone'         => 'sometimes|nullable|numeric',
        'email'         => 'sometimes|nullable|email',
        'social_handle' => 'sometimes|nullable',
        'website'       => 'sometimes|nullable',
        'login'         => 'sometimes|nullable',
        'password'      => 'sometimes|nullable',
        'gst'           => 'sometimes|nullable|max:255',
        'account_name'  => 'sometimes|nullable|max:255',
        'account_iban'  => 'sometimes|nullable|max:255',
        'account_swift' => 'sometimes|nullable|max:255'
      ]);

      $data = $request->except('_token');

      Vendor::create($data);

      return redirect()->route('vendor.index')->withSuccess('You have successfully saved a vendor!');
    }

    public function productStore(Request $request)
    {
      $this->validate($request, [
        'vendor_id'       => 'required|numeric',
        'images.*'        => 'sometimes|nullable|image',
        'date_of_order'   => 'required|date',
        'name'            => 'required|string|max:255',
        'qty'             => 'sometimes|nullable|numeric',
        'price'           => 'sometimes|nullable|numeric',
        'payment_terms'   => 'sometimes|nullable|string',
        'recurring_type'  => 'required|string',
        'delivery_date'   => 'sometimes|nullable|date',
        'received_by'     => 'sometimes|nullable|string',
        'approved_by'     => 'sometimes|nullable|string',
        'payment_details' => 'sometimes|nullable|string'
      ]);

      $data = $request->except('_token');

      $product = VendorProduct::create($data);

      if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)->upload();
          $product->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->back()->withSuccess('You have successfully saved a vendor product!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $vendor = Vendor::find($id);
      $vendor_categories = VendorCategory::all();
      $vendor_show = true;
      $reply_categories = ReplyCategory::all();
      $users_array = Helpers::getUserArray(User::all());

      return view('vendors.show', [
        'vendor'  => $vendor,
        'vendor_categories'  => $vendor_categories,
        'vendor_show'  => $vendor_show,
        'reply_categories'  => $reply_categories,
        'users_array'  => $users_array
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
        'category_id'     => 'sometimes|nullable|numeric',
        'name'            => 'required|string|max:255',
        'address'         => 'sometimes|nullable|string',
        'phone'           => 'sometimes|nullable|numeric',
        'default_phone'   => 'sometimes|nullable|numeric',
        'whatsapp_number' => 'sometimes|nullable|numeric',
        'email'           => 'sometimes|nullable|email',
        'social_handle'   => 'sometimes|nullable',
        'website'         => 'sometimes|nullable',
        'login'           => 'sometimes|nullable',
        'password'        => 'sometimes|nullable',
        'gst'             => 'sometimes|nullable|max:255',
        'account_name'    => 'sometimes|nullable|max:255',
        'account_iban'    => 'sometimes|nullable|max:255',
        'account_swift'   => 'sometimes|nullable|max:255'
      ]);

      $data = $request->except('_token');

      Vendor::find($id)->update($data);

      return redirect()->route('vendor.index')->withSuccess('You have successfully updated a vendor!');
    }

    public function productUpdate(Request $request, $id)
    {
      $this->validate($request, [
        'vendor_id'       => 'sometimes|nullable|numeric',
        'images.*'        => 'sometimes|nullable|image',
        'date_of_order'   => 'required|date',
        'name'            => 'required|string|max:255',
        'qty'             => 'sometimes|nullable|numeric',
        'price'           => 'sometimes|nullable|numeric',
        'payment_terms'   => 'sometimes|nullable|string',
        'recurring_type'  => 'required|string',
        'delivery_date'   => 'sometimes|nullable|date',
        'received_by'     => 'sometimes|nullable|string',
        'approved_by'     => 'sometimes|nullable|string',
        'payment_details' => 'sometimes|nullable|string'
      ]);

      $data = $request->except('_token');

      $product = VendorProduct::find($id);
      $product->update($data);

      if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)->upload();
          $product->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->back()->withSuccess('You have successfully updated a vendor product!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $vendor = Vendor::find($id);

//      foreach ($vendor->products as $product) {
//        $product->detachMediaTags(config('constants.media_tags'));
//      }

//      $vendor->products()->delete();
//      $vendor->chat_messages()->delete();
//      $vendor->agents()->delete();
      $vendor->delete();

      return redirect()->route('vendor.index')->withSuccess('You have successfully deleted a vendor');
    }

    public function productDestroy($id)
    {
      $product = VendorProduct::find($id);

      $product->detachMediaTags(config('constants.media_tags'));
      $product->delete();

      return redirect()->back()->withSuccess('You have successfully deleted a vendor product!');
    }
}
