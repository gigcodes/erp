<?php

namespace App\Http\Controllers;

use App\MagentoCustomerReference;
use Illuminate\Http\Request;
use App\Setting;
use App\Customer;
use App\StoreWebsite;
use App\Helpers\InstantMessagingHelper;
use App\Helpers\MagentoOrderHandleHelper;

class MagentoCustomerReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
    }

    /**
     * Create magento order
     *
     * @return \Illuminate\Http\Response
     */
    public function createOrder( Request $request )
    {   
        $bodyContent = $request->getContent();

        if( empty( $bodyContent )  ){
            $message = $this->generate_erp_response("magento.order.failed.validation",0, $default = 'Invalid data',request('lang_code'));
            return response()->json([
                'status'  => false,
                'message' => $message,
            ]);
        }

        $order = json_decode( $bodyContent );

        if( isset( $order->items[0]->website_id ) ){
            $website = StoreWebsite::where( 'id', $order->items[0]->website_id )->first();   
            
            if( $website ){
                $orderCreate = MagentoOrderHandleHelper::createOrder( $order, $website );
                if( $orderCreate == true ){
                    return response()->json([
                        'status'  => true,
                        'message' => 'Order create successfully',
                    ]);
                }
            }
        }

        $message = $this->generate_erp_response("magento.order.failed",0, $default = 'Something went wrong, Please try again', request('lang_code'));
        return response()->json([
            'status'  => false,
            'message' => $message,
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
       
        if (empty($request->name)) {
            $message = $this->generate_erp_response("customer_reference.403",0, $default = 'Name is required',request('lang_code'));
            return response()->json(['message' => $message], 403);
        }

        // if (empty($request->phone)) {
        //     return response()->json(['error' => 'Phone is required'], 403);
        // }

        if (empty($request->email)) {
            $message = $this->generate_erp_response("customer_reference.403",0, $default = 'Email is required', request('lang_code'));
            return response()->json(['message' => $message], 403);
        }

        if (empty($request->website)) {
            $message = $this->generate_erp_response("customer_reference.403",0, $default = 'website is required', request('lang_code'));
            return response()->json(['message' => $message], 403);
        }
        
        // if (empty($request->social)) {
        //     return response()->json(['error' => 'Social is required'], 403);
        // }
        $name = $request->name;
        $email = $request->email;
        $website = $request->website;
        $phone = null;
        $dob = null;
        $store_website_id = null;
        $wedding_anniversery = null;
        if($request->phone) {
            $phone = $request->phone;
        }
        if($request->dob) {
            $dob = $request->dob;
        }
        if($request->wedding_anniversery) {
            $wedding_anniversery = $request->wedding_anniversery;
        }

         //getting reference
         $reference = Customer::where('email',$email)->first();
         $store_website = StoreWebsite::where('website',"like", $website)->first();
         if($store_website) {
             $store_website_id = $store_website->id;
         }
        if(empty($reference)){

            $reference = new Customer();
            $reference->name = $name;
            $reference->phone = $phone;
            $reference->email = $email;
            $reference->store_website_id = $store_website_id;
            $reference->dob = $dob;
            $reference->wedding_anniversery = $wedding_anniversery;
            $reference->save();
        
        }
        else {
            $message = $this->generate_erp_response("customer_reference.403",0, $default = 'Account already exists with this email', request('lang_code'));
            return response()->json(['message' => $message], 403);
        }
        
        

        if($reference->phone) {
            //get welcome message
            $welcomeMessage = InstantMessagingHelper::replaceTags($reference, Setting::get('welcome_message'));
            //sending message
            app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($reference->phone, '', $welcomeMessage, '', '');
        }
        

        $message = $this->generate_erp_response("customer_reference.success",$store_website_id, $default = 'Saved successfully !', request('lang_code'));
        return response()->json(['message' => 'Saved SucessFully'], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MagentoCustomerReference  $magentoCustomerReference
     * @return \Illuminate\Http\Response
     */
    public function show(MagentoCustomerReference $magentoCustomerReference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MagentoCustomerReference  $magentoCustomerReference
     * @return \Illuminate\Http\Response
     */
    public function edit(MagentoCustomerReference $magentoCustomerReference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MagentoCustomerReference  $magentoCustomerReference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MagentoCustomerReference $magentoCustomerReference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MagentoCustomerReference  $magentoCustomerReference
     * @return \Illuminate\Http\Response
     */
    public function destroy(MagentoCustomerReference $magentoCustomerReference)
    {
        //
    }
}
