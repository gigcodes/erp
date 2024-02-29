<?php

namespace App\Http\Controllers;

use App\StoreWebsite;
use App\WatsonAccount;
use App\Jobs\PushToWatson;
use Illuminate\Http\Request;

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
        $accounts       = WatsonAccount::all();

        return view('watson.index', compact('store_websites', 'accounts'));
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'store_website_id'       => 'required|integer',
            'api_key'                => 'required|string',
            'work_space_id'          => 'required|string',
            'assistant_id'           => 'required|string',
            'url'                    => 'required|string',
            'speech_to_text_api_key' => 'required|string',
            'speech_to_text_url'     => 'required|string',
            'user_name'              => 'required|string',
            'password'               => 'required|string',
        ]);
        WatsonAccount::create($request->all());

        return response()->json(['code' => 200, 'message' => 'Account Successfully created']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $account        = WatsonAccount::find($id);
        $store_websites = StoreWebsite::all();

        return response()->json(['account' => $account, 'store_websites' => $store_websites]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $account = WatsonAccount::find($id);
        $params  = $request->except('_token');
        if (array_key_exists('is_active', $params)) {
            $params['is_active'] = 1;
        } else {
            $params['is_active'] = 0;
        }
        $account->update($params);

        return response()->json(['code' => 200, 'message' => 'Account Successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = WatsonAccount::find($id);
        $account->delete();

        return redirect()->back();
    }

    public function addIntentsToWatson($id)
    {
        $account = WatsonAccount::find($id);
        PushToWatson::dispatch($id)->onQueue('watson_push');
        $account->update(['watson_push' => 1]);

        return response()->json(['message' => 'Successfully added to the queue', 'code' => 200]);
    }
}
