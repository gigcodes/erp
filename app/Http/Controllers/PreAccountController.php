<?php

namespace App\Http\Controllers;

use App\Account;
use App\PreAccount;
use Illuminate\Http\Request;

class PreAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = PreAccount::all();

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
            'email' => 'required|unique:pre_accounts',
            'password' => 'required'
        ]);

        $account = new Account();
        $account->first_name = $request->get('first_name');
        $account->last_name = $request->get('last_name');
        $account->email = $request->get('email');
        $account->password = $request->get('password');
        $account->instagram = 1;
        $account->facebook = 1;
        $account->pinterest = 1;
        $account->twitter = 1;
        $account->save();

        return redirect()->back()->with('message', 'E-mail added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PreAccount  $preAccount
     * @return \Illuminate\Http\Response
     */
    public function show(PreAccount $preAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PreAccount  $preAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(PreAccount $preAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PreAccount  $preAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PreAccount $preAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PreAccount  $preAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(PreAccount $preAccount)
    {
        //
    }
}
