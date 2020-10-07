<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\ReferFriend;
use App\Coupon;
use GuzzleHttp\Client;
use Exception;

class ReferaFriend extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $validator = Validator::make($request->all(), [
            'referrer_first_name' => 'required|max:30',
            'referrer_last_name' => 'required|max:30',
            'referrer_email' => 'required|email',
            'referrer_phone' => 'required|max:20',
            'referee_first_name' => 'required|max:30',
            'referee_last_name' => 'required|max:30',
            'referee_email' => 'required|email|unique:refer_friend,referee_email',
            'referee_phone' => 'required|max:20',
            'domain_name' => 'required|max:50',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => 'Please check validation errors !', 'errors' => $validator->errors()], 400);
        }
        $success = ReferFriend::create($request->all());
        if (!is_null($success)) {
            $refferal_data['referrer_email'] = $request->input('referrer_email');
            $refferal_data['referee_email'] = $request->input('referee_email');
            return $this->createCoupon($refferal_data);
        }
        return response()->json(['status' => 'failed', 'message' => 'Unable to create referral at the moment. Please try later !'], 500);
    }
    public function createCoupon($data = null)
    {

        if (!isset($data) || $data == '') {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Unable to create coupon',
                ],
                500
            );
        }
        $httpClient = new Client;
        $referrer_coupon = Str::random(15);
        $referee_coupon = Str::random(15);
        $coupondata = array(
            'description' => 'Coupon generated from refer a friend scheme',
            'discount_fixed' => 100,
            'discount_percentage' => 15,
            'minimum_order_amount' => 500,
            'maximum_usage' => 1,
        );
        $referrer_coupondata =[];
        $referee_coupondata =[];
        if ($data['referrer_email']) {
            $referrer_coupondata = $coupondata;
            $referrer_coupondata['code'] = $referrer_coupon;
            $referrer_coupondata['start'] = date('y-m-d H:i');
            $referrer_coupondata['expiration'] = null;
        }
        if ($data['referee_email']) {
            $referee_coupondata = $coupondata;
            $referee_coupondata['code'] = $referee_coupon;
            $referee_coupondata['start'] =  date('y-m-d H:i');
            $referee_coupondata['expiration'] = null;
        }
        //$queryString1 = http_build_query($referrer_coupondata);
        //$queryString2 = http_build_query($referee_coupondata);
        try {
            //$url1 = 'https://devsite.sololuxury.com/contactcustom/index/createCoupen?' . $queryString1;
            //$response1 = $httpClient->get($url1);

            //$url2 = 'https://devsite.sololuxury.com/contactcustom/index/createCoupen?' . $queryString2;
            //$response2 = $httpClient->get($url2);

            Coupon::create($referrer_coupondata);
            Coupon::create($referee_coupondata);
            return response()->json([
                'message' => 'refferal created successfully',
                'referrer_code' => $referrer_coupon,
                'referee_code' => $referee_coupon,
                'referrer_email' => $data['referrer_email'],
                'referee_email' => $data['referee_email']
            ], 200);
            /* return response()->json([
                'message' => 'refferal created successfully',
                'referrer_body' => $response1->getBody(),
                'referee_body' => $response2->getBody(),
                'referrer_code' => $response1->getStatusCode(),
                'referee_code' => $response2->getStatusCode(),
                'referrer_url' => $url1,
                'referee_url' => $url2
            ], 200); */
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unable to create coupon',
                'error' => $e->getMessage()
            ], 500);
        }
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
