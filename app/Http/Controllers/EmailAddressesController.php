<?php

namespace App\Http\Controllers;

use App\EmailAddress;
use Illuminate\Http\Request;

class EmailAddressesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $emailAddress = EmailAddress::paginate(15);
        return view('email-addresses.index', [
            'emailAddress' => $emailAddress
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'from_name' => 'required|string|max:255',
            'from_address' => 'required|string|max:255',
            'driver' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|string|max:255',
            'encryption' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255'
        ]);

        $data = $request->except('_token');
        
        EmailAddress::create($data);

        return redirect()->route('email-addresses.index')->withSuccess('You have successfully saved a Email Address!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'from_name' => 'required|string|max:255',
            'from_address' => 'required|string|max:255',
            'driver' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|string|max:255',
            'encryption' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255'
        ]);

        $data = $request->except('_token');
        
        EmailAddress::find($id)->update($data);

        return redirect()->back()->withSuccess('You have successfully updated a Email Address!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $emailAddress = EmailAddress::find($id);

        $emailAddress->delete();

        return redirect()->route('email-addresses.index')->withSuccess('You have successfully deleted a Email Address');
    }
}
