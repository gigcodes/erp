<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\ColdLeads;
use App\User;
use App\UserRate;
use App\Role;
use App\Permission;
use App\ApiKey;
use App\Customer;
use Auth;
use App\Helpers;
use App\Task;
use PragmaRX\Tracker\Vendor\Laravel\Models\Session;
use \Carbon\Carbon;
use DateTime;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffPaymentAccount;
use App\Payment;
use App\PaymentMethod;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $title = "User management";
        return view('usermanagement::index', compact('title'));
    }

    public function records(Request $request)
    {
        $user = User::query();

        if($request->keyword != null) {
            $user = $user->where(function($q) use ($request) {
                $q->where("email","like","%".$request->keyword."%")
                ->orWhere("name","like","%".$request->keyword."%")
                ->orWhere("phone","like","%".$request->keyword."%");
            });
        }


        $user = $user->select(["users.*"])->paginate(12);

        $limitchacter = 50;

        $items = [];
        if (!$user->isEmpty()) {
            foreach ($user as $u) {
                $currentRate = $u->latestRate;

                $u["hourly_rate"] = ($currentRate) ? $currentRate->hourly_rate : 0;
                $u["currency"]    = ($currentRate) ? $currentRate->currency : "USD";

                // setup task list
                $taskList = $u->taskList;
                if (!$taskList->isEmpty()) {
                    $pendingDevtask = 0;
                    $pendingIssue = 0;
                    $pendingTask = 0;
                    $completedDevTask = 0;
                    $completedIssue = 0;
                    $completedTask = 0;

                    foreach($taskList as $task) {
                        if($task->model_type == 'App\DeveloperTask') {
                            $devtask =  \App\DeveloperTask::where("id",$task->model_id)->first();
                            if($devtask && $devtask->completed) {
                                $completedDevTask++;
                            }
                            else {
                                $pendingDevtask++;
                            }
                        }
                        if($task->model_type == 'App\Issue') {
                            $issue =  \App\Issue::where("id",$task->model_id)->first();
                            if($issue && $issue->is_resolved) {
                                $completedIssue++;
                            }
                            else {
                                $pendingIssue++;
                            }
                        }
                        if($task->model_type == 'App\Task') {
                            $ts =  \App\Task::where("id",$task->model_id)->first();
                            if($ts && $ts->is_completed) {
                                $completedTask++;
                            }
                            else {
                                $pendingTask++;
                            }
                        }
                    }
                    $u["completedDevTask"] = $completedDevTask;
                    $u["pendingDevtask"] = $pendingDevtask;
                    $u["completedIssue"] = $completedIssue;
                    $u["pendingIssue"] = $pendingIssue;
                    $u["completedTask"] = $completedTask;
                    $u["pendingTask"] = $pendingTask;
                    // $u["current_task_desc"] = isset($taskList[0]) ? $taskList[0]->detail()->task_id . "-" . $taskList[0]->detail()->task : "";

                    // $u["next_task_desc"]    = isset($taskList[1]) ? $taskList[1]->detail()->task_id . "-" . $taskList[1]->detail()->task : "";

                    // $u["current_task"]      = (strlen($u["current_task_desc"]) > $limitchacter) ? substr($u["current_task_desc"], 0, $limitchacter)."..." : $u["current_task_desc"];

                    // $u["next_task"]         = (strlen($u["next_task"]) > $limitchacter) ? substr($u["next_task"], 0, $limitchacter)."..." : $u["next_task"];
                }

                $u["yesterday_hrs"] = $u->yesterdayHrs();
                $starts_at = date("Y-m-d");
                if($u->payment_frequency == 'fornightly') {
                    $starts_at = date("Y-m-d");
                }
                if($u->payment_frequency == 'weekly') {
                    $starts_at = date("Y-m-d", strtotime('-7 days'));
                }
                if($u->payment_frequency == 'biweekly') {
                    $starts_at = date("Y-m-d", strtotime('-14 days'));
                }
                if($u->payment_frequency == 'monthly') {
                    $starts_at = date("Y-m-d", strtotime("-1 month"));
                }
                $u["previousDue"] = $u->previousDue($starts_at);
                $u["starts_at"] = $starts_at;
                $items[] = $u;
            }
            
        }

        return response()->json([
            "code"       => 200,
            "data"       => $items,
            "pagination" => (string) $user->links(),
            "total"      => $user->total(),
            "page"       => $user->currentPage(),
        ]);

    }

    public function edit($id) {
        $user = User::find($id);
		$roles = Role::orderBy('name', 'asc')->pluck('name', 'id')->all();
		$permission = Permission::orderBy('name', 'asc')->pluck('name', 'id')->all();

		$users = User::all();
		$userRole = $user->roles->pluck('name', 'id')->all();
		$userPermission = $user->permissions->pluck('name', 'id')->all();
		$agent_roles  = array('sales' => 'Sales', 'support' => 'Support', 'queries' => 'Others');
		$user_agent_roles = explode(',', $user->agent_role);
		$api_keys = ApiKey::select('number')->get();
		$customers_all = Customer::select(['id', 'name', 'email', 'phone', 'instahandler'])->whereRaw("customers.id NOT IN (SELECT customer_id FROM user_customers WHERE user_id != $id)")->get()->toArray();

		$userRate = UserRate::getRateForUser($user->id);

        return view('usermanagement::edit-user.index', compact('user','userRole', 'users', 'roles', 'edit', 'agent_roles', 'user_agent_roles', 'api_keys', 'customers_all', 'permission', 'userPermission', 'userRate'));
    }


    public function update(Request $request, $id)
	{
		$this->validate($request, [
			'name' => 'required',
			'email' => 'required|email|unique:users,email,' . $id,
			'phone' => 'sometimes|nullable|integer|unique:users,phone,' . $id,
			'password' => 'same:confirm-password',
			'roles' => 'required',
		]);


		$input = $request->all();
		$hourly_rate = $input['hourly_rate'];
		$currency = $input['currency'];

		unset($input['hourly_rate']);
		unset($input['currency']);

		$input['name'] = str_replace(' ', '_', $input['name']);
		if (isset($input['agent_role'])) {
			$input['agent_role'] = implode(',', $input['agent_role']);
		} else {
			$input['agent_role'] = '';
		}


		if (!empty($input['password'])) {
			$input['password'] = Hash::make($input['password']);
		} else {
			$input = array_except($input, array('password'));
		}

		$user = User::find($id);
		$user->update($input);

		if ($request->customer[0] != '') {
			$user->customers()->sync($request->customer);
		}

		$user->roles()->sync($request->input('roles'));
		$user->permissions()->sync($request->input('permissions'));

		$user->listing_approval_rate = $request->get('listing_approval_rate') ?? '0';
		$user->listing_rejection_rate = $request->get('listing_rejection_rate') ?? '0';
		$user->save();


		$userRate = new UserRate();
		$userRate->start_date = Carbon::now();
		$userRate->hourly_rate = $hourly_rate;
		$userRate->currency = $currency;
		$userRate->user_id = $user->id;
		$userRate->save();


		return redirect()->back()
			->with('success', 'User updated successfully');
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
    public function show($id)
	{
		$user = User::find($id);

		if (Auth::id() != $id) {
			return redirect()->route('user-management.index')->withWarning("You don't have access to this page!");
		}

		$users_array = Helpers::getUserArray(User::all());
		$roles = Role::pluck('name', 'name')->all();
		$users = User::all();
		$userRole = $user->roles->pluck('name', 'name')->all();
		$agent_roles  = array('sales' => 'Sales', 'support' => 'Support', 'queries' => 'Others');
		$user_agent_roles = explode(',', $user->agent_role);
		$api_keys = ApiKey::select('number')->get();

		$pending_tasks = Task::where('is_statutory', 0)
			->whereNull('is_completed')
			->where(function ($query) use ($id) {
				return $query->orWhere('assign_from', $id)
					->orWhere('assign_to', $id);
			})->get();

		// dd($pending_tasks);
        // return response()->json(["code" => 200, "user" => $user]);

        return view('usermanagement::templates.show', 
        [
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
    
    public function usertrack($id)
    {
        $user = User::find($id);
        $actions = $user->actions()->orderBy('created_at', 'DESC')->get();

        $tracks = Session::where('user_id', $id)->orderBy('created_at', 'DESC')->get();

        $routeActions = [
            'users.index' => 'Viewed Users Page',
            'users.show' => 'Viewed A User',
            'customer.index' => 'Viewed Customer Page',
            'customer.show' => 'Viewed A Customer Page',
            'cold-leads.index' => 'Viewed Cold Leads Page',
            'home' => 'Landed Homepage',
            'purchase.index' => 'Viewed Purchase Page'
        ];

        $models = [
            'users.show' => new User(),
            'customer.show' => new Customer(),
            'cold-leads.show' => new ColdLeads(),
        ];

        return view('usermanagement::templates.track', 
        [
			'actions'	=> $actions,
			'tracks'	=> $tracks,
			'routeActions'	=> $routeActions,
			'models'	=> $models
		]);
    }

    
    public function userPayments($id, Request $request)
	{
		$params = $request->all();

		$date = new DateTime();

		if (isset($params['year']) && isset($params['week'])) {
			$year = $params['year'];
			$week = $params['week'];
		} else {
			$week = $date->format("W");
			$year = $date->format("Y");
		}
		$result = getStartAndEndDate($week, $year);
		$start = $result['week_start'];
		$end = $result['week_end'];

		$user = User::join('hubstaff_payment_accounts as hpa',"hpa.user_id","users.id")->where('users.id', $id)->with(['currentRate'])->first();
		$usersRatesThisWeek = UserRate::ratesForWeek($week, $year);

		$usersRatesPreviousWeek = UserRate::latestRatesForWeek($week - 1, $year);

		$activitiesForWeek = HubstaffActivity::getActivitiesForWeek($week, $year);

		$paymentsDone = Payment::getConsidatedUserPayments();

		$amountToBePaid = HubstaffPaymentAccount::getConsidatedUserAmountToBePaid();

		

        $now = now();
        $paymentMethods = array();
            if($user) {
                $user->secondsTracked = 0;
			$user->currency = '-';
			$user->total = 0;

			

            $userPaymentsDone = 0;
            
			$userPaymentsDoneModel = $paymentsDone->first(function ($value) use($user) {
				return $value->user_id == $user->id;
			});

			if($userPaymentsDoneModel){
				$userPaymentsDone = $userPaymentsDoneModel->paid;
			}

			$userPaymentsToBeDone = 0;
			$userAmountToBePaidModel = $amountToBePaid->first(function ($value) use($user){
				return $value->user_id == $user->id;
			});

			if($userAmountToBePaidModel){
				$userPaymentsToBeDone = $userAmountToBePaidModel->amount;
			}

			$user->balance = $userPaymentsToBeDone - $userPaymentsDone;

			

			//echo $user->id. ' '.$userPaymentsToBeDone. ' '. $userPaymentsDone. PHP_EOL;


			$invidualRatesPreviousWeek  = $usersRatesPreviousWeek->first(function ($value, $key) use ($user) {
				return $value->user_id == $user->id;
			});




			$weekRates = [];

			if ($invidualRatesPreviousWeek) {
				$weekRates[] = array(
					'start_date' => $start,
					'rate' => $invidualRatesPreviousWeek->hourly_rate,
					'currency' => $invidualRatesPreviousWeek->currency
				);
			}

			$rates = $usersRatesThisWeek->filter(function ($value, $key) use ($user) {
				return $value->user_id == $user->id;
			});

			if ($rates) {

				foreach ($rates as $rate) {
					$weekRates[] = array(
						'start_date' => $rate->start_date,
						'rate' => $rate->hourly_rate,
						'currency' => $rate->currency
					);
				}
			}


			usort($weekRates, function ($a, $b) {
				return strtotime($a['start_date']) - strtotime($b['start_date']);
			});


			if (sizeof($weekRates) > 0) {
				$lastEntry = $weekRates[sizeof($weekRates) - 1];

				$weekRates[] = array(
					'start_date' => $end,
					'rate' => $lastEntry['rate'],
					'currency' => $lastEntry['currency']
				);

				$user->currency = $lastEntry['currency'];
			}

			$activities = $activitiesForWeek->filter(function ($value, $key) use ($user) {
				return $value->system_user_id === $user->id;
			});

			$user->trackedActivitiesForWeek = $activities;

			foreach ($activities as $activity) {
				$user->secondsTracked += $activity->tracked;
				$i = 0;
				while ($i < sizeof($weekRates) - 1) {

					$start = $weekRates[$i];
					$end = $weekRates[$i + 1];

					if ($activity->starts_at >= $start['start_date'] && $activity->start_time < $end['start_date']) {
						// the activity needs calculation for the start rate and hence do it
						$earnings = $activity->tracked * ($start['rate'] / 60 / 60);
						$activity->rate = $start['rate'];
						$activity->earnings = $earnings;
						$user->total += $earnings;
						break;
					}
					$i++;
				}
			}
		

		//exit;
            
            foreach (PaymentMethod::all() as $paymentMethod) {
                $paymentMethods[$paymentMethod->id] = $paymentMethod->name;
            }
        }
			
        
        return view('usermanagement::templates.payments', 
        [
			'user' => $user,
			'id' => $id,
			'selectedYear' => $year,
			'selectedWeek' => $week,
			'paymentMethods' => $paymentMethods
		]);
	}


}
