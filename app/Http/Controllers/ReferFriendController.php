<?php

namespace App\Http\Controllers;

use App\ReferFriend;
use Illuminate\Http\Request;

class ReferFriendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $query = ReferFriend::query();

        if ($request->id) {
            $query = $query->where('id', $request->id);
        }
        if ($request->term) {
            $query = $query->where('referrer_email', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referrer_first_name', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referrer_last_name', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referrer_phone', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referee_first_name', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referee_last_name', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referee_email', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referee_phone', 'LIKE', '%' . $request->term . '%')
                ->orWhere('website', 'LIKE', '%' . $request->term . '%')
                ->orWhere('status', 'LIKE', '%' . $request->term . '%');
        }

        if ($request->for_date) {
            $query = $query->whereDate('created_at', $request->for_date);
        }

        $data = $query->orderBy('id', 'desc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('referfriend.partials.list-referral', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $data->render(),
                'count' => $data->total(),
            ], 200);
        }

        return view('referfriend.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
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
        //
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ReferFriend = ReferFriend::find($id);
        $ReferFriend->delete();

        return redirect()->route('referfriend.list')
            ->with('success', 'Referral deleted successfully');
    }

    /*
    * logAjax : Return log of refere friend api
    */
    public function logAjax(Request $request)
    {
        if ($request->ajax()) {
            $log = \App\LogReferalCoupon::where('refer_friend_id', $request->get('id'))->get()->toArray();
            if ($log) {
                return response()->json([
                    'data' => $log,
                ], 200);
            }

            return response()->json([
                'data' => [],
            ], 200);
        }
    }
}
