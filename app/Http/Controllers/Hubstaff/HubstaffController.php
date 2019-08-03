<?php

namespace App\Http\Controllers\Hubstaff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Hubstaff\Hubstaff;

class HubstaffController extends Controller
{
	public $appToken;
	public $authToken;
	public $email;
	public $password;

	public function userDetails(Request $request){

		$this->appToken = getenv('HUBSTAFF_APP_KEY');
		$this->authToken = $request->auth_token;
		$this->email = $request->email;
		$this->password = $request->password;

		$hubstaff = Hubstaff::getInstance();

		$hubstaff->authenticate($this->appToken, $this->email, $this->password, $this->authToken);

		//Get Repository you want and call the method on the same
		$users = $hubstaff->getRepository('user')->getAllUsers();

		// dd($users[0]->name);
		return $users;

	}

	public function getUserPage(){
		return view('hubstaff.hubstaff-api-show', compact('users'));
	}
	
}
