<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UserLogin;
use App\Setting;
use App\Helpers;
use App\NotificationQueue;
use App\PushNotification;
use App\ApiKey;
use App\Task;
use App\Product;
use App\Customer;
use App\UserProduct;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Cache;
use Auth;
use Log;
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
		$this->middleware('permission:user-list', ['except' => ['assignProducts']]);
		$this->middleware('permission:user-create', ['only' => ['create','store']]);
		$this->middleware('permission:user-edit', ['only' => ['edit','update']]);
		$this->middleware('permission:user-delete', ['only' => ['destroy']]);
		$this->middleware('permission:product-lister', ['only' => ['assignProducts']]);
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
			'phone' => 'sometimes|nullable|integer|unique:users,phone',
			'password' => 'required|same:confirm-password',
			'roles' => 'required',

		]);


		$input = $request->all();
		$input['name'] = str_replace(' ', '_', $input['name']);
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

		if (Auth::id() != $id) {
			return redirect()->route('users.index')->withWarning("You don't have access to this page!");
		}

		$users_array = Helpers::getUserArray(User::all());
		$roles = Role::pluck('name','name')->all();
		$users = User::all();
		$userRole = $user->roles->pluck('name','name')->all();
		$agent_roles  = array('sales' =>'Sales' , 'support' => 'Support' , 'queries' => 'Others');
    $user_agent_roles = explode(',', $user->agent_role);
		$api_keys = ApiKey::select('number')->get();

		$pending_tasks = Task::where('is_statutory', 0)
		                     ->whereNull('is_completed')
												 ->where(function ($query) use ($id) {
													 	return $query->orWhere('assign_from', $id)
											             				->orWhere('assign_to', $id);
													})->get();

		// dd($pending_tasks);

		return view('users.show', [
			'user'	=> $user,
			'users_array'	=> $users_array,
			'roles'	=> $roles,
			'users'	=> $users,
			'userRole'	=> $userRole,
			'agent_roles'	=> $agent_roles,
			'user_agent_roles'	=> $user_agent_roles,
			'api_keys'	=> $api_keys,
			'pending_tasks'	=> $pending_tasks,
		]);
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
		$api_keys = ApiKey::select('number')->get();
		$customers_all = Customer::select(['id', 'name', 'email', 'phone', 'instahandler'])->whereRaw("customers.id NOT IN (SELECT customer_id FROM user_customers WHERE user_id != $id)")->get()->toArray();


		return view('users.edit',compact('user', 'users', 'roles','userRole' , 'agent_roles' ,'user_agent_roles', 'api_keys', 'customers_all'));
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
			'phone' => 'sometimes|nullable|integer|unique:users,phone,' . $id,
			'password' => 'same:confirm-password',
			'roles' => 'required',

		]);

		$input = $request->all();
		$input['name'] = str_replace(' ', '_', $input['name']);
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

		if ($request->customer[0] != '') {
			$user->customers()->sync($request->customer);
		}

		if (!$user->hasRole('Products Lister') && in_array('Prod  ucts Lister', $request->roles)) {
			$requestData = new Request();
			$requestData->setMethod('POST');
			$requestData->request->add(['amount_assigned' => 100]);

			$this->assignProducts($requestData, Auth::id());
		}

		DB::table('model_has_roles')->where('model_id',$id)->delete();

		$user->assignRole($request->input('roles'));



		return redirect()->back()
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

	public function assignProducts(Request $request, $id)
	{
		$user = User::find($id);

		// $amount_assigned = $request->amount_assigned ?? $user->amount_assigned;
		$amount_assigned = 25;

		if ($amount_assigned != '') {
			$products = Product::where('is_scraped', 1)->where('stock', '>=', 1)->where('is_image_processed', 1)->where('is_crop_approved', 1)->where('is_crop_ordered', 1)->where('is_approved', 0)->where('isUploaded', 0)->where('isFinal', 0);
			$user_products = UserProduct::pluck('product_id');
			// dd($user_products);
			// $product_ids = [];
			// foreach ($user_products as $product) {
			// 	$product_ids[] = $product->product_id;
			// }
			//
			// dd($product_ids);
			// dd($products->get());
			// if ($user->products()->count()) {
				// $product_ids = [];
				// foreach ($user->products as $product) {
				// 	$product_ids[] = $product->id;
				// }

				$products = $products->whereNotIn('id', $user_products)->latest()->take($amount_assigned)->get();

				// dd($products);
				$user->products()->attach($products);
			// } else {
			// 	$products = $products->whereNotIn('id', $product_ids)->take($user->amount_assigned)->get();
			// 	$user->products()->attach($products);
			// }
		} else {
			return redirect()->back()->withErrors('Please select amount assigned first!');
		}

		if (count($products) > 0) {
			$message = "You have successfully assigned " . count($products) . " products";
		} else {
			$message = "There were no products to assign!";
		}

		return redirect()->back()->withSuccess($message);
	}

	public function login(Request $request)
	{
		$date = $request->date ? $request->date : Carbon::now()->format('Y-m-d');
		$logins = UserLogin::whereBetween('login_at', [$date, Carbon::parse($date)->addDay()])->latest()->paginate(Setting::get('pagination'));

		return view('users.login', [
			'logins'	=> $logins,
			'date'		=> $date
		]);
	}

	public function activate(Request $request, $id)
	{
		$user = User::find($id);

		if ($user->is_active == 1) {
			$user->is_active = 0;
		} else {
			$user->is_active = 1;
		}

		$user->save();

		return redirect()->back()->withSuccess('You have successfully updated the user!');
	}

	public function checkUserLogins()
	{
		Log::info(Carbon::now() . " begin checking users logins");
		$users = User::all();

		foreach ($users as $user) {
			if ($login = UserLogin::where('user_id', $user->id)->where('created_at', '>', Carbon::now()->format('Y-m-d'))->latest()->first()) {

			} else {
				$login = UserLogin::create(['user_id'	=> $user->id]);
			}

			if (Cache::has('user-is-online-' . $user->id)) {
				if ($login->logout_at) {
					UserLogin::create(['user_id'	=> $user->id, 'login_at'	=> Carbon::now()]);
				} else if (!$login->login_at) {
					$login->update(['login_at'	=> Carbon::now()]);
				}
			} else {
				if ($login->created_at && !$login->logout_at) {
					$login->update(['logout_at'	=> Carbon::now()]);
				}
			}
		}

		Log::info(Carbon::now() . " end of checking users logins");
	}
}
