<?php

namespace App\Http\Controllers\Social;

use App\Social\SocialConfig;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Setting;
use Validator;
use Crypt;
use Response;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class SocialConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {

            $query = SocialConfig::query();


            $socialConfigs = $query->orderby('id', 'desc')->paginate(Setting::get('pagination'));

        } else {
            $socialConfigs = SocialConfig::latest()->paginate(Setting::get('pagination'));
        }
        $websites = \App\StoreWebsite::select('id','title')->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.configs.partials.data', compact('socialConfigs'))->render(),
                'links' => (string)$socialConfigs->render()
            ], 200);
        }
     

        return view('social.configs.index',compact('socialConfigs', 'websites'));

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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request);
        $this->validate($request, [
            'store_website_id' => 'required',
            'platform' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            "status"=> 'required'
          
        ]);

        $data = $request->except('_token');
        $data['password'] = Crypt::encrypt($request->password);
        SocialConfig::create($data);

        return redirect()->back()->withSuccess('You have successfully stored Config.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\SocialConfig $SocialConfig
     * @return \Illuminate\Http\Response
     */
    public function show(SocialConfig $SocialConfig)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\SocialConfig $SocialConfig
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        $this->validate($request, [
            'store_website_id' => 'required',
            'platform' => 'required',
            'name' => 'required',
          //  'email' => 'required',
         //   'password' => 'required',
            "status"=> 'required',
        ]);
        $config = SocialConfig::findorfail($request->id);
        $data = $request->except('_token', 'id');
        $data['password'] = Crypt::encrypt($request->password);
        $config->fill($data);
        $config->save();
       // $config->update($data);

        return redirect()->back()->withSuccess('You have successfully changed  Config');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\SocialConfig $SocialConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SocialConfig $SocialConfig)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\SocialConfig $SocialConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $config = SocialConfig::findorfail($request->id);
        $config->delete();
        return Response::json(array(
            'success' => true,
            'message' => ' Config Deleted'
        ));
    }
   
    
}
