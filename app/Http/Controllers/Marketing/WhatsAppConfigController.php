<?php

namespace App\Http\Controllers\Marketing;

use App\Marketing\WhatsappConfig;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Setting;
use Validator;
use Crypt;
use Response;

class WhatsappConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

       if($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date){

        $query =  WhatsappConfig::query();

            //global search term
        if (request('term') != null) {
            $query->where('number', 'LIKE', "%{$request->term}%")
            ->orWhere('username', 'LIKE', "%{$request->term}%")
            ->orWhere('password', 'LIKE', "%{$request->term}%")
            ->orWhere('provider', 'LIKE', "%{$request->term}%");
        }


        if (request('date') != null) {
            $query->whereDate('created_at', request('website'));
        }


               //if number is not null
        if (request('number') != null) {
            $query->where('number','LIKE', '%' . request('number') . '%');
        }

            //If username is not null
        if (request('username') != null) {
            $query->where('username','LIKE', '%' . request('username') . '%');
        }

           //if provider with is not null
        if (request('provider') != null) {
            $query->where('provider', 'LIKE', '%' . request('provider') . '%');
        }

           //if provider with is not null
        if (request('customer_support') != null) {
            $query->where('is_customer_support', request('customer_support'));
        }

        $whatsAppConfigs = $query->orderby('id','desc')->paginate(Setting::get('pagination'));

    }else{
        $whatsAppConfigs = WhatsappConfig::latest()->paginate(Setting::get('pagination'));
    }

    if ($request->ajax()) {
        return response()->json([
            'tbody' => view('marketing.whatsapp-configs.partials.data', compact('whatsAppConfigs'))->render(),
            'links' => (string)$whatsAppConfigs->render()
        ], 200);
    }



    return view('marketing.whatsapp-configs.index', [
      'whatsAppConfigs' => $whatsAppConfigs,
    ]);

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

        $this->validate($request, [
        'number'   => 'required|max:13|unique:whatsapp_configs,number',
        'provider'       => 'required',
        'customer_support' => 'required',
        'username'  => 'required|min:3|max:255',
        'password'  => 'required|min:6|max:255',
        'frequency' => 'required',
        'send_start' => 'required',
        'send_end' => 'required',
      ]);

      $data = $request->except('_token');
      $data['password'] = Crypt::encrypt($request->password);
      $data['is_customer_support'] = $request->customer_support;

      WhatsappConfig::create($data);

      return redirect()->back()->withSuccess('You have successfully stored Whats App Config');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\WhatsappConfig  $whatsAppConfig
     * @return \Illuminate\Http\Response
     */
    public function show(WhatsappConfig $whatsAppConfig)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WhatsappConfig  $whatsAppConfig
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

         $this->validate($request, [
        'number' => 'required|max:13',
        'provider' => 'required',
        'customer_support' => 'required',
        'username'  => 'required|min:3|max:255',
        'password'  => 'required|min:6|max:255',
        'frequency' => 'required',
        'send_start' => 'required',
        'send_end' => 'required',
        ]);
        $config = WhatsappConfig::findorfail($request->id);
        $data = $request->except('_token','id');
        $data['password'] = Crypt::encrypt($request->password);
        $data['is_customer_support'] = $request->customer_support;
        $config->update($data);

        return redirect()->back()->withSuccess('You have successfully changed Whats App Config');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\WhatsappConfig  $whatsAppConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WhatsappConfig $whatsAppConfig)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WhatsappConfig  $whatsAppConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $config = WhatsappConfig::findorfail($request->id);
        $config->delete();
        return Response::json(array(
            'success' => true,
            'message' => 'WhatsApp Config Deleted'
        ));
    }
}
