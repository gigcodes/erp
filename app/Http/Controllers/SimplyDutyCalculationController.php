<?php

namespace App\Http\Controllers;

use App\SimplyDutyCalculation;
use Illuminate\Http\Request;

class SimplyDutyCalculationController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SimplyDutyCalculation  $simplyDutyCalculation
     * @return \Illuminate\Http\Response
     */
    public function show(SimplyDutyCalculation $simplyDutyCalculation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SimplyDutyCalculation  $simplyDutyCalculation
     * @return \Illuminate\Http\Response
     */
    public function edit(SimplyDutyCalculation $simplyDutyCalculation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SimplyDutyCalculation  $simplyDutyCalculation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SimplyDutyCalculation $simplyDutyCalculation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SimplyDutyCalculation  $simplyDutyCalculation
     * @return \Illuminate\Http\Response
     */
    public function destroy(SimplyDutyCalculation $simplyDutyCalculation)
    {
        //
    }

    public function calculate(Request $request){
        $this->validate(
        $request, 
        [   
            'OriginCountryCode' => 'required',
            'HSCode' => 'required',
            'DestinationCountryCode' => 'required',
            'Quantity' => 'required',
            'Value' => 'required',
            'Shipping' => 'required',
            'Insurance' => 'required',
        ],
        [   
            'HSCode.required'    => 'Please Provide HSCODE.',
            'DestinationCountryCode.required'    => 'Please Provide Destination Country Code.',
            'Quantity.required'    => 'Please Provide Quantity.',
            'Value.required'    => 'Please Provide Value.',
            'Shipping.required'    => 'Please Provide Shipping.',
            'Insurance.required'    => 'Please Provide Insurance.',
        ]);

        $originCountryCode = $request->OriginCountryCode;
        $destinationCountryCode = $request->DestinationCountryCode;
        $destinationStateCode = $request->DestinationStateCode;
        $hSCode = $request->HSCode;
        $quantity = $request->Quantity;
        $value = $request->Value;
        $shipping = $request->Shipping;
        $insurance = $request->Insurance;
        $originCurrencyCode = $request->OriginCurrencyCode;
        $destinationCurrencyCode = $request->DestinationCurrencyCode;
        $shipInsCalculationType = $request->ShipInsCalculationType;
        $contractInsuranceType = $request->ContractInsuranceType;

       $output =  array('HsCode' => '6109.10.0000','Value' => '1000','VAT' => '144.05','Duty' => 0 , 'Shipping' => 0 , 'Insurance' => 0 , 'Total' => 0 , 'ExchangeRate' => 0 , 'CurrencyTypeOrigin' => 0 , 'CurrencyTypeDestination' => 'GBP' , 'DutyMinimis' => 131 , 'DutyRate' => 0 , 'DutyType' => 'Full Rate' , 'DutyHSCode' => 'sdfs' , 'VatMinimis' => 15 , 'VatRate' => 20 , 'Quantity' => 1);    
        return json_encode($output);
    }
}
