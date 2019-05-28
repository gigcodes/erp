<?php

namespace App\Http\Controllers;

use App\ColdLeadBroadcasts;
use App\ColdLeads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use InstagramAPI\Instagram;

class ColdLeadBroadcastsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return view('cold_leads.broadcasts.index');
        }

        $this->validate($request, [
            'pagination' => 'required|integer',
        ]);

        if (strlen($request->get('query')) >= 4) {
            $query = $request->get('query');
            $leads = ColdLeadBroadcasts::where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%$query%");
                });
        } else {
            $leads = new ColdLeadBroadcasts;
        }

        $leads = $leads->orderBy('updated_at', 'DESC')->paginate($request->get('pagination'));


        return response()->json([
            'leads' => $leads,
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
            'name' => 'required',
            'number_of_users' => 'required',
            'frequency' => 'required',
            'message' => 'required',
            'started_at' => 'required',
            'status' => 'required',
        ]);

        $broadcast = new ColdLeadBroadcasts();
        $broadcast->name = $request->get('name');
        $broadcast->number_of_users = $request->get('number_of_users');
        $broadcast->frequency = $request->get('frequency');
        $broadcast->message = $request->get('message');
        $broadcast->started_at = $request->get('started_at');
        $broadcast->status = $request->get('status');
        $broadcast->save();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = Storage::disk('uploads')->putFile('', $file);
            $broadcast->image = $fileName;
            $broadcast->save();
        }

        $coldleads = ColdLeads::whereNotIn('status', [0])->where('messages_sent', '<', 5)->take($request->get('number_of_users'))->orderBy('messages_sent', 'ASC')->orderBy('id', 'ASC')->get();

        foreach ($coldleads as $coldlead) {

            $broadcast->lead()->attach($coldlead->id, [
                'status' => 0
            ]);

            $coldlead->status = 2;
            $coldlead->save();
        }

        return response()->json([
            'status' => 'success'
        ]);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ColdLeadBroadcasts  $coldLeadBroadcasts
     * @return \Illuminate\Http\Response
     */
    public function show(ColdLeadBroadcasts $coldLeadBroadcasts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ColdLeadBroadcasts  $coldLeadBroadcasts
     * @return \Illuminate\Http\Response
     */
    public function edit(ColdLeadBroadcasts $coldLeadBroadcasts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ColdLeadBroadcasts  $coldLeadBroadcasts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ColdLeadBroadcasts $coldLeadBroadcasts)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ColdLeadBroadcasts  $coldLeadBroadcasts
     * @return \Illuminate\Http\Response
     */
    public function destroy(ColdLeadBroadcasts $coldLeadBroadcasts)
    {
        //
    }
}
