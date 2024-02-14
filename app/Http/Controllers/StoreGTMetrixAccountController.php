<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StoreGTMetrixAccount;

class StoreGTMetrixAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Accounts = StoreGTMetrixAccount::latest()->paginate(5);

        return view('GtMetrixAccount.index', compact('Accounts'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('GtMetrixAccount.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'account_id' => 'required',
        ]);

        StoreGTMetrixAccount::create($request->all());

        return redirect()->route('GtMetrixAccount.index')
            ->with('success', 'GtMetrixAccount created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StoreGTMetrixAccount $StoreGTMetrixAccount)
    {
        return view('GtMetrixAccount.show', compact('Accounts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $account = StoreGTMetrixAccount::where('id', $id)->get()->first();

        return view('GtMetrixAccount.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
            'account_id' => 'required',
        ]);
        $id = $request->input('id');
        $input['email'] = $request->input('email');
        $input['password'] = $request->input('password');
        $input['account_id'] = $request->input('account_id');
        $input['status'] = $request->input('status');
        $insert = StoreGTMetrixAccount::where('id', $id)->update($input);

        return redirect()->route('GtMetrixAccount.index')
            ->with('success', 'GtMetrixAccount updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $StoreGTMetrixAccount = StoreGTMetrixAccount::find($id);
        $StoreGTMetrixAccount->delete();

        return redirect()->route('GtMetrixAccount.index')
            ->with('success', 'GtMetrix Account deleted successfully');
    }
}
