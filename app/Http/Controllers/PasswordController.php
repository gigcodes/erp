<?php

namespace App\Http\Controllers;

use Auth;
use Crypt;
use App\User;
use App\Email;
use App\Setting;
use App\Password;
use App\ChatMessage;
use App\PasswordRemark;
use App\PasswordHistory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->website || $request->username || $request->password || $request->registered_with || $request->term || $request->date) {
            $query = Password::query();

            //global search term
            if (request('term') != null) {
                $query->where('website', 'LIKE', "%{$request->term}%")
                    ->orWhere('username', 'LIKE', "%{$request->term}%")
                    ->orWhere('password', 'LIKE', "%{$request->term}%")
                    ->orWhere('registered_with', 'LIKE', "%{$request->term}%");
            }

            if (request('date') != null) {
                $query->whereDate('created_at', request('website'));
            }

            //if website is not null
            if (request('website') != null) {
                $query->where('website', 'LIKE', '%' . request('website') . '%');
            }

            //If username is not null
            if (request('username') != null) {
                $query->where('username', 'LIKE', '%' . request('username') . '%');
            }

            //if password is not null
            if (request('password') != null) {
                $query->where('password', 'LIKE', '%' . Crypt::encrypt(request('password')) . '%');
            }

            //if registered with is not null
            if (request('registered_with') != null) {
                $query->where('registered_with', 'LIKE', '%' . request('registered_with') . '%');
            }

            $passwords = $query->orderby('website', 'asc')->paginate(Setting::get('pagination'));
        } else {
            $passwords = Password::latest()->paginate(Setting::get('pagination'));
        }
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('passwords.data', compact('passwords'))->render(),
                'links' => (string) $passwords->render(),
            ], 200);
        }
        $users = User::orderBy('name', 'asc')->get();
        $password_remark = PasswordRemark::get();

        return view('passwords.index', [
            'passwords' => $passwords,
            'users' => $users,
            'password_remark' => $password_remark,
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

    public function changePasswords(Request $request)
    {
        if (empty($request->users)) {
            return redirect()->back()->with('error', 'Please select user');
        }

        $users = explode(',', $request->users);
        $data = [];
        foreach ($users as $key) {
            // Generate new password
            $newPassword = Str::random(12);

            $user = Password::findorfail($key);
            $user->password = Crypt::encrypt($newPassword);
            $user->save();
            $data[$key] = $newPassword;
        }
        \Session::flash('success', 'Password Updated');

        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'website' => 'sometimes|nullable|string|max:255',
            'url' => 'required',
            'username' => 'required|min:3|max:255',
            'password' => 'required|min:6|max:255',
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (! isset($request->send_on_whatsapp)) {
            $this->validate($request, [
                'website' => 'sometimes|nullable|string|max:255',
                'url' => 'required',
                'username' => 'required|min:3|max:255',
                'password' => 'required|min:6|max:255',
            ]);

            $password = Password::findorfail($request->id);
            $data_old['password_id'] = $password->id;
            $data_old['website'] = $password->website;
            $data_old['url'] = $password->url;
            $data_old['username'] = $password->username;
            $old_password = $password->password;
            $data_old['password'] = $old_password;
            $data_old['registered_with'] = $password->registered_with;
            PasswordHistory::create($data_old);

            $data = $request->except('_token');
            $data['password'] = Crypt::encrypt($request->password);
            $password->update($data);
            $successMessage = 'Passwords Manager updated successfully';
        }
        if (isset($request->send_message) && $request->send_message == 1) {
            $user_id = $request->user_id;
            $user = User::findorfail($user_id);
            $number = $user->phone;
            $whatsappnumber = '971502609192';
            if (isset($request->send_on_whatsapp)) {
                $password = Password::findorfail($request->id);
                $message = 'Username for ' . $password->website . ' is: ' . $password['username'] . ' Password For ' . $password->website . ' is: ' . Crypt::decrypt($password->password);
                $successMessage = 'You have successfully sent password';
            } else {
                $message = 'Password Change For ' . $request->website . 'is, Old Password  : ' . Crypt::decrypt($old_password) . ' New Password is : ' . $request->password;
                $successMessage = 'You have successfully changed password';
            }
            $whatsappmessage = new WhatsAppController();
            $whatsappmessage->sendWithThirdApi($number, $user->whatsapp_number, $message);
        }

        return redirect()->back()->withSuccess($successMessage);
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

    public function autoSuggestUsername(Request $request)
    {
        $username = $request->input('username');
        $autosuggestions = User::where('name', 'like', $username . '%')->paginate(10)->pluck('name');

        return response()->json($autosuggestions);
    }

    public function autoSuggestEmail(Request $request)
    {
        $email = $request->input('email');
        $autosuggestions = User::where('email', 'like', $email . '%')->paginate(10)->pluck('email');

        return response()->json($autosuggestions);
    }


    public function manage(request $request)
    {
        $query = User::query();
        if ($request->username) {
            $dataArray = json_decode($request->username, true);
            $query = $query->where(function($query) use ($dataArray) {
                foreach ($dataArray as $username) {
                    $query->orWhere('name', 'LIKE', $username . '%');
                }
            });
        }
        if ($request->email) {
            $dataArrayEmail = json_decode($request->email, true);
            $query = $query->where(function($query) use ($dataArrayEmail) {
                foreach ($dataArrayEmail as $email) {
                    $query->orWhere('email', 'LIKE', $email . '%');
                }
            });
        }
        $emailAddressArr = \App\EmailAddress::orderBy('from_address', 'asc')->get();
        $users = $query->where('is_active', 1)->orderBy('id', 'desc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('passwords.partials.change-password', compact('users', 'emailAddressArr'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $users->render(),
                'count' => $users->total(),
            ], 200);
        }

        return view('passwords.change-password', compact('users', 'emailAddressArr'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function changePassword(Request $request)
    {
        if (empty($request->users)) {
            return redirect()->back()->with('error', 'Please select user');
        }

        $users = explode(',', $request->users);
        $data = [];
        foreach ($users as $key) {
            // Generate new password
            $newPassword = Str::random(12);

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

        return view('passwords.send-whatsapp', ['data' => $data]);
    }

    public function sendWhatsApp(Request $request)
    {
        if (isset($request->single) && $request->single == 1) {
            $user_id = $request->user_id;
            $password = $request->password;
            $user = User::findorfail($user_id);
            $number = $user->phone;
            $message = 'Your New Password For ERP desk is Username : ' . $user->email . ' Password : ' . $password;

            $whatsappmessage = new WhatsAppController();
            $whatsappmessage->sendWithThirdApi($number, $user->whatsapp_number, $message);
            $params['user_id'] = $user_id;
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
            $msg = 'WhatsApp send';
            $data = [
                'success' => true,
                'message' => $msg,
            ];

            return response()->json($data);
        } else {
            $user_id = $request->user_id;
            $password = $request->password;
            for ($i = 0; $i < count($user_id); $i++) {
                $user = User::findorfail($user_id[$i]);
                $number = $user->phone;
                $message = 'Your New Password For ERP desk is Username : ' . $user->email . ' Password : ' . $password[$i];

                $whatsappmessage = new WhatsAppController();
                $whatsappmessage->sendWithThirdApi($number, $user->whatsapp_number, $message);
                $params['user_id'] = $user->id;
                $params['message'] = $message[$i];
                $chat_message = ChatMessage::create($params);
            }

            return redirect()->route('password.manage')->with('message', 'SuccessFully Messages Send !');
        }
    }

    public function getHistory(Request $request)
    {
        $password = PasswordHistory::where('password_id', $request->password_id)->get();
        $count = 0;
        foreach ($password as $passwords) {
            $value[$count]['username'] = $passwords->username;
            $value[$count]['website'] = $passwords->website;
            $value[$count]['url'] = $passwords->url;
            $value[$count]['registered_with'] = $passwords->registered_with;
            $value[$count]['password_decrypt'] = Crypt::decrypt($passwords->password);
            $count++;
        }
        if (count($password) == 0) {
            return [];
        } else {
            return $value;
        }

        return $value;
    }

    public function passwordCreateGetRemark(Request $request)
    {
        try {
            $msg = '';
            if ($request->remark != '') {
                PasswordRemark::create(
                    [
                        'password_id' => $request->password_id,
                        'password_type' => $request->type,
                        'updated_by' => \Auth::id(),
                        'remark' => $request->remark,
                    ]
                );
                $msg = ' Created and ';
            }

            $taskRemarkData = PasswordRemark::where([['password_id', '=', $request->password_id], ['password_type', '=', $request->type]])->get();

            $html = '';
            foreach ($taskRemarkData as $taskRemark) {
                $html .= '<tr>';
                $html .= '<td>' . $taskRemark->id . '</td>';
                $html .= '<td>' . $taskRemark->users->name . '</td>';
                $html .= '<td>' . $taskRemark->remark . '</td>';
                $html .= '<td>' . $taskRemark->created_at . '</td>';
                $html .= "<td><i class='fa fa-copy copy_remark' data-remark_text='" . $taskRemark->remark . "'></i></td>";
            }

            $input_html = '';
            $i = 1;
            foreach ($taskRemarkData as $taskRemark) {
                $input_html .= '<span class="td-password-remark" style="margin:0px;"> ' . $i . '.' . $taskRemark->remark . '</span>';
                $i++;
            }

            return response()->json(['code' => 200, 'data' => $html, 'remark_data' => $input_html, 'message' => 'Remark ' . $msg . ' listed Successfully']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => '', 'remark_data' => '', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Search data of passwords.
     *
     * @param  string  $subject
     * @return \Illuminate\Http\Response
     */
    public function passwordsSearch(Request $request)
    {
        $subject = $request->subject;
        $data = Password::where('website', 'LIKE', '%' . $subject . '%')->orderby('website', 'asc')->get();
        $users = User::orderBy('name', 'asc')->get();
        $password_remark = PasswordRemark::get();

        return view('passwords.data', [
            'passwords' => $data,
            'users' => $users,
            'password_remark' => $password_remark,
        ]);
    }

    public function passwordsShowEditdata(Request $request)
    {
        $data = Password::where('id', $request->password_id)->first();
        $pass = Crypt::decrypt($data->password);

        return response()->json(['code' => 200, 'data' => $data, 'pass' => $pass]);
    }

    /**
     * Send email to given emailId
     *
     * @return \Illuminate\Http\Response
     */
    public function passwordSendEmail(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'from_email' => 'required',
            ]);

            $newPassword = Str::random(12);
            $message = '';
            $message .= 'Email id = ' . $request->email . '<br>';
            $message .= 'Password = ' . $newPassword;

            //Store data in chat_message table.
            $params = [
                'number' => null,
                'user_id' => Auth::user()->id,
                'message' => $message,
            ];

            ChatMessage::create($params);

            // Store data in email table
            $from_address = isset($request->from_email) && $request->from_email != '' ? $request->from_email : config('env.MAIL_FROM_ADDRESS');

            $email = Email::create([
                'model_id' => '',
                'model_type' => \App\Password::class,
                'from' => $from_address,
                'to' => $request->email,
                'subject' => 'Password Manager',
                'message' => $message,
                'template' => 'reset-password',
                'status' => 'pre-send',
                'store_website_id' => null,
                'is_draft' => 1,
            ]);

            // Send email
            \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
            \Session::flash('success', 'Password manager email send successfully');
        } catch (\Throwable $th) {
            $emails = Email::latest('created_at')->first();
            $emails->error_message = $th->getMessage();
            $emails->save();
            \Session::flash('error', $th->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Show email password history
     *
     * @return \Illuminate\Http\Response
     */
    public function passwordSendEmailHistory(Request $request)
    {
        $passwordEmails = Email::where('model_type', 'App\Password')->where('to', $request->email)->get();

        return view('emails.password-email-history', compact('passwordEmails'));
    }
}
