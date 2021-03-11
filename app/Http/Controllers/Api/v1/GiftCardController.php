<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\StoreWebsite;
use App\GiftCard;
use App\Coupon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AddGiftCard;

class GiftCardController extends Controller
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
            'sender_name' => 'required|max:30',
            'sender_email' => 'required|email',
            'receiver_name' => 'required|max:30',
            'receiver_email' => 'required|email',
            'gift_card_coupon_code' => 'required|max:50|unique:gift_cards,gift_card_coupon_code',
            'gift_card_description' => 'max:1000',
            'gift_card_amount' => 'required|integer',
            'gift_card_message' => 'max:200',
            'expiry_date' => 'required|date_format:Y-m-d|after:yesterday',
            'website' => 'required|exists:store_websites,website',
        ]);
        if ($validator->fails()) {
            $message = $this->generate_erp_response("giftcard.failed.validation", 0, $default = "Please check validation errors !", request('lang_code'));
            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }
        $storeweb = StoreWebsite::where('website', $request->website)->first();
        $giftcardData = $couponData = [];
        $giftcardData = $request->all();
        $giftcardData['store_website_id'] = $storeweb->id;
        $success = GiftCard::create($giftcardData);
        if ($success) {
            $couponData['code'] = $success->gift_card_coupon_code;
            $couponData['description'] = $success->gift_card_description;
            $couponData['start'] = date('Y-m-d H:i');
            $couponData['expiration'] = $success->expiry_date;
            $couponData['details'] =  $success->gift_card_message;
            $couponData['initial_amount'] =  $success->gift_card_amount;
            $couponData['email'] =  $success->receiver_email;
            $couponData['coupon_type'] =  'giftcard';
            $couponData['status'] =  1;
            if (Coupon::create($couponData)) {
                $mailData['receiver_email'] = $success->receiver_email;
                $mailData['sender_email'] = $success->sender_email;
                $mailData['coupon'] =  $success->gift_card_coupon_code;
                $this->sendMail($mailData);

                $message = $this->generate_erp_response("giftcard.success", $storeweb->id, $default = "gift card added successfully", request('lang_code'));
                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                ], 200);
            }
            $message = $this->generate_erp_response("giftcard.failed", $storeweb->id, $default = "Unable to add gift card at the moment. Please try later !", request('lang_code'));
            return response()->json(['status' => 'failed', 'message' => $message], 500);
        }
        $message = $this->generate_erp_response("giftcard.failed", $storeweb->id, $default = "Unable to add gift card at the moment. Please try later !", request('lang_code'));
        return response()->json(['status' => 'failed', 'message' => $message], 500);
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
    /**
     * Display coupon information/amount.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkGiftcardCouponAmount(request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|max:30|exists:gift_cards,gift_card_coupon_code',
        ]);
        if ($validator->fails()) {
            $message = $this->generate_erp_response("giftcard.amount.failed.validation", 0, $default = "Please check validation errors !", request('lang_code'));
            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }
        $couponData = GiftCard::select('gift_card_amount', 'gift_card_coupon_code', 'updated_at')->where('gift_card_coupon_code', $request->coupon_code)->first();
        if(!$couponData){
            $message = $this->generate_erp_response("giftcard.amount.failed", 0, $default = "coupon does not exists in record !", request('lang_code'));
            return response()->json(['status' => 'failed', 'message' => $message], 500);
        }

        $message = $this->generate_erp_response("giftcard.amount.success", 0, $default = "gift card amount fetched successfully", request('lang_code'));
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $couponData,
        ], 200);
    }
    public function sendMail($data = null)
    {
        if ($data) {
            try {
                $to = $data['receiver_email'];
                Mail::to($to)->send(new AddGiftCard($data));
            } catch (\Exception $e) {
            }
        }
        return true;
    }
}
