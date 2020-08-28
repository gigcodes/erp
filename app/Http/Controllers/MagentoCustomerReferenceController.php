<?php

namespace App\Http\Controllers;

use App\MagentoCustomerReference;
use Illuminate\Http\Request;
use App\Setting;
use App\Customer;
use App\Helpers\InstantMessagingHelper;

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
       
        if (empty($request->name)) {
            return response()->json(['error' => 'Name is required'], 403);
        }

        if (empty($request->phone)) {
            return response()->json(['error' => 'Phone is required'], 403);
        }

        if (empty($request->email)) {
            return response()->json(['error' => 'Email is required'], 403);
        }

        if (empty($request->social)) {
            return response()->json(['error' => 'Social is required'], 403);
        }

        //getting reference
        $reference = Customer::where('phone',$request->phone)->first();
        
        
        if(empty($reference)){

            $reference = new Customer();
            $reference->name = $request->name;
            $reference->phone = $request->phone;
            $reference->email = $request->email;
            $reference->save();
        
        }
        
        //get welcome message
        $welcomeMessage = InstantMessagingHelper::replaceTags($reference, Setting::get('welcome_message'));

        //sending message
        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($reference->phone, '', $welcomeMessage, '', '');

        return response()->json(['success' => 'Saved SucessFully'], 200);

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
