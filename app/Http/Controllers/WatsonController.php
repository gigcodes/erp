<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StoreWebsite;
use App\WatsonAccount;
class WatsonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $store_websites = StoreWebsite::all();
        $accounts = WatsonAccount::all();
        return view('watson.index',compact('store_websites','accounts'));
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
            'store_website_id' => 'required|integer',
            'api_key' => 'required|string|string',
            'url' => 'required|string'
        ]);
        WatsonAccount::create($request->all());
        return response()->json(['code' => 200, 'message' => 'Account Successfully created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $account = WatsonAccount::find($id);
        $store_websites = StoreWebsite::all();
        return response()->json(['account' => $account,'store_websites' => $store_websites]);
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
        $account = WatsonAccount::find($id);
        $params = $request->except('_token');
        if(array_key_exists('is_active',$params)) {
            $params['is_active'] = 1;
        }
        $account->update($params);
        return response()->json(['code' => 200, 'message' => 'Account Successfully updated']);

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
