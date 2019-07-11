<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Voucher;
use App\VoucherCategory;
use App\Setting;
use App\Helpers;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class VoucherController extends Controller
{
    public function __construct() {
      $this->middleware('permission:voucher');
    }

    public function index(Request $request)
    {
      $start = $request->range_start ? $request->range_start : Carbon::now()->startOfWeek();
      $end = $request->range_end ? $request->range_end : Carbon::now()->endOfWeek();

      if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM')) {
        if ($request->user[0] != null) {
          $vouchers = Voucher::whereIn('user_id', $request->user)->whereBetween('date', [$start, $end]);
        } else {
          $vouchers = Voucher::whereBetween('date', [$start, $end]);
        }
      } else {
        $vouchers = Voucher::where('user_id', Auth::id())->whereBetween('date', [$start, $end]);
      }

      $vouchers = $vouchers->orderBy('date', 'DESC')->get();
      // dd($vouchers);
      //
      // $currentPage = LengthAwarePaginator::resolveCurrentPage();
  		// $perPage = Setting::get('pagination');
  		// $currentItems = array_slice($vouchers, $perPage * ($currentPage - 1), $perPage);
      //
  		// $vouchers = new LengthAwarePaginator($currentItems, count($vouchers), $perPage, $currentPage, [
  		// 	'path'	=> LengthAwarePaginator::resolveCurrentPath()
  		// ]);
      //
      // dd($vouchers);
      // paginate(Setting::get('pagination'));
      $users_array = Helpers::getUserArray(User::all());

      return view('vouchers.index', [
        'vouchers'    => $vouchers,
        'users_array' => $users_array,
        'user'        => $request->user
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $voucher_categories = VoucherCategory::where('parent_id', 0)->get();
      $voucher_categories_dropdown = VoucherCategory::attr(['name' => 'category_id','class' => 'form-control', 'placeholder' => 'Select a Category'])
  		                                        ->renderAsDropdown();

      return view('vouchers.create', [
        'voucher_categories' => $voucher_categories,
        'voucher_categories_dropdown' => $voucher_categories_dropdown,
      ]);
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
        'description' => 'required|min:3',
        'travel_type' => 'sometimes|nullable|string',
        'amount'      => 'sometimes|nullable|numeric',
        'paid'        => 'sometimes|nullable|numeric',
        'date'        => 'required|date',
      ]);

      $data = $request->except('_token');
      $data['user_id'] = Auth::id();

      $voucher = Voucher::create($data);

      if ($request->ajax()) {
        return response()->json(['id' => $voucher->id]);
      }

      return redirect()->route('voucher.index')->with('success', 'You have successfully created cash voucher');
    }

    public function storeCategory(Request $request)
    {
      $this->validate($request,[
  			'title' => 'required_without:subcategory'
  		]);

  		if ($request->title != '') {
  			VoucherCategory::create(['title' => $request->title]);
  		}

  		if ($request->parent_id != '' && $request->subcategory != '') {
  			VoucherCategory::create(['title' => $request->subcategory, 'parent_id' => $request->parent_id]);
  		}


  		return redirect()->back()->with('success','Category created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $voucher = Voucher::find($id);
      $voucher_categories = VoucherCategory::where('parent_id', 0)->get();
      $voucher_categories_dropdown = VoucherCategory::attr(['name' => 'category_id','class' => 'form-control', 'placeholder' => 'Select a Category'])
                                              ->selected($voucher->category_id)
  		                                        ->renderAsDropdown();

      return view('vouchers.edit', [
        'voucher' => $voucher,
        'voucher_categories'  => $voucher_categories,
        'voucher_categories_dropdown'  => $voucher_categories_dropdown,
      ]);
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
      if ($request->type == "partial") {
        $this->validate($request, [
          'travel_type' => 'sometimes|nullable|string',
          'amount'      => 'sometimes|nullable|numeric',
        ]);
      } else {
        $this->validate($request, [
          'description' => 'required|min:3',
          'travel_type' => 'sometimes|nullable|string',
          'amount'      => 'sometimes|nullable|numeric',
          'paid'        => 'sometimes|nullable|numeric',
          'date'        => 'required|date',
        ]);
      }

      $data = $request->except('_token');

      Voucher::find($id)->update($data);

      if ($request->type == "partial") {
        return redirect()->back()->with('success', 'You have successfully updated cash voucher');
      }

      return redirect()->route('voucher.index')->with('success', 'You have successfully updated cash voucher');
    }

    public function approve(Request $request, $id)
    {
      $voucher = Voucher::find($id);

      if ($voucher->approved == 1) {
        $voucher->approved = 2;
      } else {
        $voucher->approved = 1;
      }

      $voucher->save();

      return redirect()->route('voucher.index')->withSuccess('You have successfully updated the voucher!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      Voucher::find($id)->delete();

      return redirect()->route('voucher.index')->with('success', 'You have successfully deleted a cash voucher');
    }
}
