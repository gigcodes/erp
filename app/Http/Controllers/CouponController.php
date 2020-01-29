<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateCouponRequest;
use App\Coupon;
use App\Helpers\SSP;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            array('db' => 'start', 'dt' => -1),
            array('db' => 'code', 'dt' => 0),
            array('db' => 'description',  'dt' => 1),
            array('db' => 'expiration',   'dt' => 2),
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

                    $id = $row['id'];
                    $code = $row['code'];
                    $description = $row['description'];
                    $start = $row['start'];
                    $expiration = $row['expiration'];
                    $currency = $row['currency'];
                    $discountFixed = $row['discount_fixed'];
                    $discountPercentage = $row['discount_percentage'];
                    $minimumOrderAmount = $row['minimum_order_amount'];
                    $maximumUsage = $row['maximum_usage'];


                    $functionCall = "(
                        '$id',
                        '$code',
                        '$description',
                        '$start',
                        '$expiration',
                        '$currency',
                        $discountFixed,
                        $discountPercentage,
                        $minimumOrderAmount,
                        $maximumUsage
                    )";

                    return '<button title="edit" onclick="editCoupon' . $functionCall . '" class="btn btn-default">
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                    </button>

                    <button title="copy" onclick="copyCoupon' . $functionCall . '" class="btn btn-default">
                        <span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span>
                    </button>

                    <button title="report" class="btn btn-default">
                        <span class="glyphicon glyphicon-stats" aria-hidden="true"></span>
                    </button>

                    <button title="delete" onclick="deleteCoupon' . $functionCall . '" class="btn btn-default">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                    </button>';
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
    public function abc(Request $request, $id)
    {
        
        echo $id;
        exit;

        $request->validate([
            'code' => 'required',
            'description' => 'required',
            'start' => 'required|date',
            'expiration' => 'date|nullable',
            'discount_fixed' => 'numeric|min:0',
            'discount_percentage' => 'numeric|min:0',
            'minimum_order_amount' => 'numeric|min:0',
            'maximum_usage' => 'numberic'
        ]);

        $validated = $request->validated();

        //
        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->code = $validated->code;
            $coupon->description = $validated->description;
            $coupon->start = $validated->start;
            $coupon->expiration = $validated->expiration;
            $coupon->discount_fixed = $validated->discount_fixed;
            $coupon->discount_percentage = $validated->discount_percentage;
            $coupon->minimum_order_amount = $validated->minimum_order_amount;
            $coupon->maximum_usage = $validated->maximum_usage;
            $coupon->save();
        } catch (ModelNotFoundException $e) {

            response(
                json_encode([
                    'message' => 'Did not find coupon with id: '.$id
                ]),
                404
            );
        }
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
