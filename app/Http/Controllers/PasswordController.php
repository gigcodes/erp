<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\User;
use Illuminate\Http\Request;
use Auth;
use Crypt;
use App\Password;
use App\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\WhatsAppController;

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
    public function update(Request $request, $id)
    {
        //
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

    public function manage()
    {
        $users = User::where('is_active',1)->orderBy('id','desc')->get();
        return view('passwords.change-password',compact('users'));
    }

    public function changePassword(Request $request){

        $users = $request->users;
        $data = array();
        foreach ($users as $key) {
            // Generate new password
            $newPassword = str_random(12);

            // Set hash password
            $hashPassword = Hash::make($newPassword);

            // Update password
            $user = User::findorfail($key);
            $user->password = $hashPassword;
            $user->save();
            $data[$key] = $newPassword;
            // Output new ones
            //echo $user->name . "\t" . $user->email . "\t" . $newPassword . "\n";
        }

        return view("passwords.send-whatsapp", ['data' => $data]);
    }

    public function sendWhatsApp(Request $request){
        if(isset($request->single) && $request->single == 1) {
            $user_id = $request->user_id;
            $password = $request->password;
            $user = User::findorfail($user_id);
            $number = $user->phone;
            $message = 'Your New Password For ERP desk is Username : ' . $user->email . ' Password : ' . $password;

            $whatsappmessage = new WhatsAppController();
            $whatsappmessage->sendWithThirdApi($number, null, $message);
            $params['user_id'] = $user_id;
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
            $msg = 'WhatsApp send';
            $data = [
                'success' => true,
                'message' => $msg
            ];
            return response()->json($data);
        }else{
            $user_id = $request->user_id;
            $password = $request->password;
                for ($i=0;$i<count($user_id);$i++){
                    $user = User::findorfail($user_id[$i]);
                    $number = $user->phone;
                    $message = 'Your New Password For ERP desk is Username : ' . $user->email . ' Password : ' . $password[$i];

                    $whatsappmessage = new WhatsAppController();
                    $whatsappmessage->sendWithThirdApi($number, null, $message);
                    $params['user_id'] = $user->id;
                    $params['message'] = $message[$i];
                    $chat_message = ChatMessage::create($params);
                }
            return redirect()->route('password.manage')->with('message', 'SuccessFully Messages Send !');

        }
    }

}
