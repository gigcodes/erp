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
use App\ReferralProgram;
use App\Customer;
use App\StoreWebsite;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendReferralMail;
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
            'referrer_last_name' => 'max:30',
            'referrer_email' => 'required|email',
            'referrer_phone' => 'max:20',
            'referee_first_name' => 'required|max:30',
            'referee_last_name' => 'max:30',
            'referee_email' => 'required|email',
            'referee_phone' => 'max:20',
            'website' => 'required|exists:store_websites,website',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => 'Please check validation errors !', 'errors' => $validator->errors()], 400);
        }
        $storeweb = StoreWebsite::where('website',$request->input('website'))->first(); 
        
        //$customer = Customer::where(['store_website_id'=>$storeweb->id,'email'=>$request->input('referrer_email')])->first();
        
        //$uuid = isset($customer->id)?md5($customer->id):'';
        $uuid = md5(Str::random(15));
        if(!$uuid){
            $message = $this->generate_erp_response("ticket.failed", $storeweb->id, $default = 'Referrer does not exist in records');
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $message,
                ],
                404
            );
        }
        $referFriendData = [];
        $referFriendData = $request->all();
        $referFriendData['store_website_id'] = $storeweb->id;
        $success = ReferFriend::create($referFriendData);
        if (!is_null($success)) {
            $refferal_data['referrer_email'] = $request->input('referrer_email');
            $refferal_data['referee_email'] = $request->input('referee_email');
            $refferal_data['website'] = $request->input('website');
            $refferal_data['uuid'] = $uuid;
            $refferal_data['store_website_id'] = $storeweb->id;
            return $this->createCoupon($refferal_data);
        }
        $message = $this->generate_erp_response("ticket.failed", $storeweb->id, $default = 'Unable to create referral at the moment. Please try later !');
        return response()->json(['status' => 'failed', 'message' => $message], 500);
    }
    public function createCoupon($data = null)
    {

        if (!isset($data) || $data == '') {
            $message = $this->generate_erp_response("coupon.failed", $data['store_website_id'], $default = 'Unable to create coupon');
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $message,
                ],
                500
            );
        }
        //$httpClient = new Client;
        $referrer_coupon = Str::random(15);
        $referee_coupon = Str::random(15);
        $refferal_program = ReferralProgram::where(['name'=>'signup_referral','store_website_id'=>$data['store_website_id']])->first();
        if(!$refferal_program){
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'refferal program for website does not exists !',
                ],
                404
            );
        }
        $coupondata = array(
            'description' => 'Coupon generated from refer a friend scheme',
            //'discount_fixed' => 100,
            //'discount_percentage' => 15,
            //'minimum_order_amount' => 500,
            //'maximum_usage' => 1,
            'initial_amount' =>$refferal_program->credit,
        );
        $referrer_coupondata = [];
        $referee_coupondata = [];
        if ($data['referrer_email']) {
            $referrer_coupondata = $coupondata;
            $referrer_coupondata['code'] = $referrer_coupon;
            $referrer_coupondata['start'] = date('y-m-d H:i');
            $referrer_coupondata['expiration'] = null;
            $referrer_coupondata['email'] = $data['referrer_email'];
            $referrer_coupondata['uuid'] = $data['uuid'];
            $referrer_coupondata['currency'] = $refferal_program->currency;
            $referrer_coupondata['coupon_type'] = 'referafriend';
            $referrer_coupondata['status'] =0;

        }
        if ($data['referee_email']) {
            $referee_coupondata = $coupondata;
            $referee_coupondata['code'] = $referee_coupon;
            $referee_coupondata['start'] =  date('y-m-d H:i');
            $referee_coupondata['expiration'] = null;
            $referee_coupondata['email'] = $data['referee_email'];
            $referee_coupondata['currency'] = $refferal_program->currency;
            $referee_coupondata['coupon_type'] = 'referafriend';
            $referee_coupondata['status'] =1;
        }
        //$queryString1 = http_build_query($referrer_coupondata);
        //$queryString2 = http_build_query($referee_coupondata);
        try {
            //$url1 = 'https://devsite.sololuxury.com/contactcustom/index/createCoupen?' . $queryString1;
            //$response1 = $httpClient->get($url1);

            //$url2 = 'https://devsite.sololuxury.com/contactcustom/index/createCoupen?' . $queryString2;
            //$response2 = $httpClient->get($url2);

            Coupon::create($referrer_coupondata);
            $referreSuccess = Coupon::create($referee_coupondata);
            if($referreSuccess){
                $referlink = $data['website'].'/register?uuid='.$data['uuid'];
                $mailData['referee_email'] = $data['referee_email'];
                $mailData['referrer_email'] = $data['referrer_email'];
                $mailData['referlink'] =  $referlink;
                $mailData['referee_coupon'] =  $referee_coupon;
                $this->sendMail($mailData);
                return response()->json([
                    'status' => 'success',
                    'message' => 'refferal created successfully',
                    'referrer_code' => $referrer_coupon,
                    //'referee_code' => $referee_coupon,
                    'referrer_email' => $data['referrer_email'],
                    'referee_email' => $data['referee_email']
                ], 200);
            }
            return response()->json([
                'status' => 'failed',
                'message' => 'Unable to create coupon',
            ], 500);
            /* return response()->json([
                'status' => 'success',
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
    public function sendMail($data=null)
    {
        if($data){
            $to = $data['referee_email'];
            Mail::to($to)->send(new SendReferralMail($data));    
        }
        return true;
    }
}
