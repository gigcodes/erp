<?php

namespace App\Http\Controllers;

use Session;
use App\ConversionRate;
use Illuminate\Http\Request;

class ConversionRateController extends Controller
{
    public function index()
    {
        $conversionRates = ConversionRate::orderBy('id', 'asc')->paginate(30);

        return view('conversion_rate.index', compact('conversionRates'));
    }

    public function update(Request $request)
    {
        $data = $request->input();
        unset($data['_token']);
        if ($request->post('id')) {
            ConversionRate::whereId($request->post('id'))->update($data);
            Session::flash('message', 'Conversion Rate Updated Successfully');
        } else {
            //Setting::add('disable_twilio', $disable_twilio, 'tinyint');
            unset($data['id']);
            ConversionRate::updateOrCreate(['currency' => $data['currency'], 'to_currency' => $data['to_currency']], $data);
            Session::flash('message', 'Conversion Rate  Created Successfully');
        }

        return redirect('/conversion/rates');
    }
}
