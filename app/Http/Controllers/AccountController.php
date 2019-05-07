<?php

namespace App\Http\Controllers;

use App\Account;
use InstagramAPI\Instagram;
use Illuminate\Http\Request;

Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class AccountController extends Controller
{
    public function show($id) {
        $account = Account::findOrFail($id);
        return view('reviews.show', compact('account'));
    }

    public function sendMessage($id, Request $request, Instagram $instagram) {
        $this->validate($request, [
            'username' => 'required',
            'message' => 'required'
        ]);

        $account = Account::findOrFail($id);
        $last_name = $account->last_name;
        $password = $account->password;

        $instagram->login($last_name, $password);
        $instagram->direct->sendText(['users' => [$request->get('username')]], $request->get('message'));
        return redirect()->back()->with('success', 'Message sent to ' . $request->get('username'));

    }
}
