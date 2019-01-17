<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UserLogin;
use App\Setting;
use App\NotificationQueue;
use App\PushNotification;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Cache;
use Carbon\Carbon;


class UserController extends Controller
{



	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	function __construct()
	{

		$this->middleware('permission:user-list');
		$this->middleware('permission:user-create', ['only' => ['create','store']]);
		$this->middleware('permission:user-edit', ['only' => ['edit','update']]);


		$this->middleware('permission:user-delete', ['only' => ['destroy']]);
	}



	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$data = User::orderBy('id','DESC')->paginate(5);
		return view('users.index',compact('data'))
			->with('i', ($request->input('page', 1) - 1) * 5);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$roles = Role::pluck('name','name')->all();
		$users = User::all();
		$agent_roles  = array('sales' =>'Sales' , 'support' => 'Support' , 'queries' => 'Others');
		return view('users.create',compact('roles', 'users' , 'agent_roles'));
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
			'name' => 'required',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|same:confirm-password',
			'roles' => 'required',

		]);


		$input = $request->all();
		$input['password'] = Hash::make($input['password']);
		if(isset($input['agent_role']))
        $input['agent_role'] = implode(',', $input['agent_role']);

		$user = User::create($input);
		$user->assignRole($request->input('roles'));


		return redirect()->route('users.index')
		                 ->with('success','User created successfully');
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$user = User::find($id);
		return view('users.show',compact('user'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$user = User::find($id);
		$roles = Role::pluck('name','name')->all();
		$users = User::all();
		$userRole = $user->roles->pluck('name','name')->all();
		$agent_roles  = array('sales' =>'Sales' , 'support' => 'Support' , 'queries' => 'Others');
        $user_agent_roles = explode(',', $user->agent_role);


		return view('users.edit',compact('user', 'users', 'roles','userRole' , 'agent_roles' ,'user_agent_roles'));
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
		$this->validate($request, [
			'name' => 'required',
			'email' => 'required|email|unique:users,email,'.$id,
			'password' => 'same:confirm-password',
			'roles' => 'required',

		]);


		$input = $request->all();
		if(isset($input['agent_role'])){
        $input['agent_role'] = implode(',', $input['agent_role']);
    }else{
    	$input['agent_role'] = '';
    }
//		$input['name'] = 'solo_admin';
//		$input['email'] = 'admin@example.com';
//		$input['password'] = 'admin@example.com';


		if(!empty($input['password'])){
			$input['password'] = Hash::make($input['password']);
		}else{
			$input = array_except($input,array('password'));
		}


		$user = User::find($id);
		$user->update($input);
		DB::table('model_has_roles')->where('model_id',$id)->delete();


		$user->assignRole($request->input('roles'));


		return redirect()->route('users.index')
		                 ->with('success','User updated successfully');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$user = User::find($id);

		// NotificationQueue::where('sent_to', $user->id)->orWhere('user_id', $user->id)->delete();
		// PushNotification::where('sent_to', $user->id)->orWhere('user_id', $user->id)->delete();

		$user->delete();

		return redirect()->route('users.index')
		                 ->with('success','User deleted successfully');
	}

	public function login()
	{
		$logins = UserLogin::paginate(Setting::get('pagination'));

		return view('users.login')->withLogins($logins);
	}

	public function checkUserLogins()
	{
		$users = User::all();

		foreach ($users as $user) {
			if ($login = UserLogin::where('user_id', $user->id)->latest()->first()) {

			} else {
				$login = UserLogin::create(['user_id'	=> $user->id]);
			}

			if (Cache::has('user-is-online-' . $user->id)) {
				if ($login->logout_at) {
					UserLogin::create(['user_id'	=> $user->id, 'login_at'	=> Carbon::now()]);
				}
			} else {
				if (!$login->logout_at) {
					$login->update(['logout_at'	=> Carbon::now()]);
				}
			}
		}
	}
}
