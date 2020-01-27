<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateCouponRequest;
use App\Coupon;
use App\Helpers\SSP;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $coupons = Coupon::orderBy('id', 'DESC')->get();
        return view('coupon.index', compact('coupons'));
    }

    public function loadData()
    {
        $tableName = with(new Coupon)->getTable();
        $primaryKey = 'id';
        $columns = array(
            array('db' => 'discount_fixed', 'dt' => -1),
            array('db' => 'discount_percentage', 'dt' => -1),
            array('db' => 'code', 'dt' => 0),
            array('db' => 'description',  'dt' => 1),
            array('db' => 'expiration',   'dt' => 2),
            //TODO: Discount here
            array(
                'db'        => 'currency',
                'dt'        => 3,
                'formatter' => function ($d, $row) {
                    $discount = '';
                    if ($row['currency']) {
                        $discount .= $row['currency'] . ' ';
                    }
                    if ($row['discount_fixed']) {
                        $discount .= $row['discount_fixed'] . ' fixed plus ';
                    }
                    if ($row['discount_percentage']) {
                        $discount .= $row['discount_percentage'] . '% discount';
                    }
                    return $discount;
                }
            ),
            array('db' => 'minimum_order_amount',   'dt' => 4),
            array('db' => 'maximum_usage',   'dt' => 5),
            array('db' => 'usage_count',   'dt' => 6),
            array(
                'db' => 'id',
                'dt' => 7,
                'formatter' => function ($d, $row) {
                    return 'actions';
                }
            )
        );

        $sql_details = array(
            'user' =>  config('database.connections.mysql.username'),
            'pass' => config('database.connections.mysql.password'),
            'db'   => config('database.connections.mysql.database'),
            'host' => config('database.connections.mysql.host')
        );



        return response(
            json_encode(
                SSP::simple($_GET, $sql_details, $tableName, $primaryKey, $columns)
            )
        );
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
    public function store(CreateCouponRequest $request)
    {
        Coupon::create($request->all());
        return redirect()->route('coupons.index')->withSuccess('You have successfully saved a coupon!');
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
        //
    }
}
