<?php

namespace App\Http\Controllers;

use App\User;
use App\UserActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserActionsController extends Controller
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
        $this->validate($request, [
            'url' => 'required',
            'type' => 'required',
            'data' => 'required'
        ]);

        $action = new UserActions();
        $action->user_id = Auth::user()->id;
        $action->page = $request->get('url');
        $action->details = strip_tags($request->get('data'));
        $action->action = $request->get('type');
        $action->date = date('Y-m-d');
        $action->save();

        return response()->json('success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserActions  $userActions
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $actions = $user->actions()->orderBy('created_at', 'DESC')->get();

        return view('users.track', compact('actions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserActions  $userActions
     * @return \Illuminate\Http\Response
     */
    public function edit(UserActions $userActions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserActions  $userActions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserActions $userActions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserActions  $userActions
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserActions $userActions)
    {
        //
    }
}
