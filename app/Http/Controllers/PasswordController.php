<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Crypt;
use App\Password;
use App\Setting;
use App\PasswordHistory;

class PasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 56 || Auth::id() == 90 || Auth::id() == 65) {
        $passwords = Password::latest()->paginate(Setting::get('pagination'));

        return view('passwords.index', [
          'passwords' => $passwords
        ]);
      } else {
        return redirect()->back();
      }
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
        'website'   => 'sometimes|nullable|string|max:255',
        'url'       => 'required|url',
        'username'  => 'required|min:3|max:255',
        'password'  => 'required|min:6|max:255'
      ]);

      $data = $request->except('_token');
      $data['password'] = Crypt::encrypt($request->password);

      Password::create($data);

      return redirect()->route('password.index')->withSuccess('You have successfully stored password');
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'website'   => 'sometimes|nullable|string|max:255',
            'url'       => 'required|url',
            'username'  => 'required|min:3|max:255',
            'password'  => 'required|min:6|max:255'
        ]);

        $password = Password::findorfail($request->id);
        $data_old['password_id'] = $password->id;
        $data_old['website'] = $password->website;
        $data_old['url'] = $password->url;
        $data_old['username'] = $password->username;
        $data_old['password'] = $password->password;
        PasswordHistory::create($data_old);

        $data = $request->except('_token');
        $data['password'] = Crypt::encrypt($request->password);
        $password->update($data);

        return redirect()->route('password.index')->withSuccess('You have successfully changed password');
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
