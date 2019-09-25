<?php

namespace App\Http\Controllers;

use App\User;
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
       $passwords = Password::latest()->paginate(Setting::get('pagination'));
        $users = User::orderBy('name','asc')->get();
        return view('passwords.index', [
          'passwords' => $passwords,
          'users' => $users,
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
        $old_password =  $password->password;
        $data_old['password'] = $old_password;
        $data_old['registered_with'] = $password->registered_with;
        PasswordHistory::create($data_old);

        $data = $request->except('_token');
        $data['password'] = Crypt::encrypt($request->password);
        $password->update($data);

        if(isset($request->send_message) && $request->send_message == 1){
            $user_id = $request->user_id;
            $user = User::findorfail($user_id);
            $number = $user->phone;
            $whatsappnumber = '971545889192';
            $message = 'Password Change For '. $request->website .'is, Old Password  : ' . Crypt::decrypt($old_password) . ' New Password is : ' . $request->password;

            $whatsappmessage = new WhatsAppController();
            $whatsappmessage->sendWithThirdApi($number, $whatsappnumber , $message);
         }

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

    public  function getHistory(Request $request){

       $password =  PasswordHistory::where('password_id',$request->password_id)->get();
       $count = 0;
       foreach ($password as $passwords){
        $value[$count]['username'] = $passwords->username;
        $value[$count]['website'] = $passwords->website;
        $value[$count]['url'] = $passwords->url;
        $value[$count]['registered_with'] = $passwords->registered_with;
        $value[$count]['password_decrypt'] = Crypt::decrypt($passwords->password);
        $count++;
       }
       if(count($password) == 0){
           return array();
       }else{
           return $value;
       }
       return $value;


    }
}
