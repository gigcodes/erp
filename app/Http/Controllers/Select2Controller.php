<?php

namespace App\Http\Controllers;

use App\Task;
use App\User;
use App\Brand;
use App\Vendor;
use App\Category;
use App\Customer;
use App\Supplier;
use App\DeveloperTask;
use Illuminate\Http\Request;
use App\TimeDoctor\TimeDoctorAccount;
use App\TimeDoctor\TimeDoctorMember;
use App\TimeDoctor\TimeDoctorProject;
use Illuminate\Support\Facades\Auth;
use App\TaskCategory;

class Select2Controller extends Controller
{
    public function customers(Request $request)
    {
        $customers = Customer::select('id', 'name', 'email');

        if (! empty($request->q)) {
            $customers->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->q . '%');
            });
        }

        $customers = $customers->paginate(30);

        $result['total_count'] = $customers->total();
        $result['incomplete_results'] = $customers->nextPageUrl() !== null;

        foreach ($customers as $customer) {
            $result['items'][] = [
                'id' => $customer->id,
                'text' => $customer->name,
            ];
        }

        return response()->json($result);
    }

    public function suppliers(Request $request)
    {
        $suppliers = Supplier::select('id', 'supplier');

        if (! empty($request->q)) {
            $suppliers->where(function ($q) use ($request) {
                $q->where('supplier', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->q . '%');
            });
        }
        $suppliers = $suppliers->paginate(30);
        $result['total_count'] = $suppliers->total();
        $result['incomplete_results'] = $suppliers->nextPageUrl() !== null;

        foreach ($suppliers as $supplier) {
            $result['items'][] = [
                'id' => $supplier->id,
                'text' => $supplier->supplier,
            ];
        }

        return response()->json($result);
    }

    public function scrapedBrand(Request $request)
    {
        $scrapedBrandsRaw = Supplier::selectRaw('scraped_brands_raw')->whereNotNull('scraped_brands_raw')->get();
        $rawBrands = [];

        foreach ($scrapedBrandsRaw as $key => $value) {
            array_push($rawBrands, array_unique(array_filter(array_column(json_decode($value->scraped_brands_raw, true), 'name'))));
        }

        $finalBrands = [];

        foreach ($rawBrands as $key => $brand) {
            $finalBrands += $brand;
        }
        $finalBrands = array_unique($finalBrands);
        if (! empty($request->q)) {
            $finalBrands = array_filter($finalBrands, function ($ele) use ($request) {
                return strpos(strtolower($ele), strtolower($request->q));
            });
        }
        foreach ($finalBrands as $key => $supplier) {
            if (strip_tags($supplier)) {
                $result['items'][] = [
                    'id' => strip_tags($supplier),
                    'text' => strip_tags($supplier),
                ];
            }
            $result['total_count'] = count($finalBrands);
        }

        return response()->json($result);
    }

    public function updatedbyUsers(Request $request)
    {
        $suppliers = User::select('id', 'name');

        $suppliers = $suppliers->paginate(30);

        $result['total_count'] = $suppliers->total();
        $result['incomplete_results'] = $suppliers->nextPageUrl() !== null;

        foreach ($suppliers as $supplier) {
            $result['items'][] = [
                'id' => $supplier->id,
                'text' => $supplier->name,
            ];
        }

        return response()->json($result);
    }

    public function users(Request $request)
    {
        $users = User::select('id', 'name', 'email');

        if (! empty($request->q)) {
            $users->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->q . '%');
            });
        }

        $users = $users->orderBy('name', 'asc')->paginate(30);

        $result['total_count'] = $users->total();
        $result['incomplete_results'] = $users->nextPageUrl() !== null;

        foreach ($users as $user) {
            $text = $user->name;

            if ($request->format === 'name-email') {
                $text = $user->name . ' - ' . $user->email;
            }

            $result['items'][] = [
                'id' => $user->id,
                'text' => $text,
            ];
        }

        return response()->json($result);
    }

    public function users_vendors(Request $request)
    {
        $users = User::select('id', 'name', 'email');

        if (! empty($request->q)) {
            $users->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->q . '%');
            });
        }

        $users = $users->orderBy('name', 'asc')->paginate(30);

        $result['total_count'] = $users->total();
        $result['incomplete_results'] = $users->nextPageUrl() !== null;

        foreach ($users as $user) {
            $text = $user->name;

            if ($request->format === 'name-email') {
                $text = $user->name . ' - ' . $user->email;
            }

            $result['items'][] = [
                'id' => $user->id,
                'text' => $text,
            ];
        }

        $vendors = Vendor::select('id', 'name', 'email');
        if (! empty($request->q)) {
            $vendors->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->q . '%');
            });
        }
        $vendors = $vendors->paginate(30);

        $result_vendors['vendors_total_count'] = $vendors->total();
        $result_vendors['vendors_incomplete_results'] = $vendors->nextPageUrl() !== null;

        foreach ($vendors as $user) {
            $text = $user->name;

            if ($request->format === 'name-email') {
                $text = $user->name . ' - ' . $user->email;
            }

            $result_vendors['items'][] = [
                'id' => $user->id,
                'text' => $text,
            ];
        }

        array_push($result, $result_vendors);

        return response()->json($result);
    }

    public function allBrand(Request $request)
    {
        if (isset($request->sort)) {
            $brands = Brand::select('id', 'name')->orderBy('name', 'ASC');
        } else {
            $brands = Brand::select('id', 'name');
        }

        if (! empty($request->q)) {
            $brands->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', $request->q . '%');
            });
        }

        $brands = $brands->paginate(30);

        $result['total_count'] = $brands->total();
        $result['incomplete_results'] = $brands->nextPageUrl() !== null;

        foreach ($brands as $brand) {
            $result['items'][] = [
                'id' => $brand->id,
                'text' => $brand->name,
            ];
        }

        return response()->json($result);
    }

    public function allTasks(Request $request)
    {
        if (isset($request->sort)) {
            $tasks = DeveloperTask::select('id', 'subject')->where('subject', '<>', '');
        } else {
            $tasks = DeveloperTask::select('id', 'subject')->where('subject', '<>', '');
        }

        if (! empty($request->q)) {
            $tasks->where(function ($q) use ($request) {
                $q->where('id', 'LIKE', $request->q . '%')->orwhere('subject', 'LIKE', $request->q . '%')->get();
            });
        }
        $tasks = $tasks->paginate(30);

        if (! count($tasks)) {
            if (isset($request->sort)) {
                $tasks = Task::select('id', 'task_subject')->where('task_subject', '<>', '');
            } else {
                $tasks = Task::select('id', 'task_subject')->where('task_subject', '<>', '');
            }

            if (! empty($request->q)) {
                $tasks->where(function ($q) use ($request) {
                    $q->where('id', 'LIKE', $request->q . '%')->orwhere('task_subject', 'LIKE', $request->q . '%')->get();
                });
            }
            $tasks = $tasks->paginate(30);
            // $result['total_count'] = $tasks->total();
            // $result['incomplete_results'] = $tasks->nextPageUrl() !== null;
        }
        $result['total_count'] = $tasks->total();
        $result['incomplete_results'] = $tasks->nextPageUrl() !== null;

        foreach ($tasks as $task) {
            $result['items'][] = [
                'id' => $task->id,
                'text' => get_class($task) == \App\DeveloperTask::class ? '#DEVTASK-' . $task->id . '-' . $task->subject : '#TASK-' . $task->id . '-' . $task->task_subject,
            ];
        }

        return response()->json($result);
    }

    public function allCategory(Request $request)
    {
        $category = Category::select('id', 'title');

        if (! empty($request->q)) {
            $category->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', $request->q . '%');
            });
        }

        $category = $category->paginate(30);

        $result['total_count'] = $category->total();
        $result['incomplete_results'] = $category->nextPageUrl() !== null;

        foreach ($category as $cat) {
            $result['items'][] = [
                'id' => $cat->id,
                'text' => $cat->title,
            ];
        }

        return response()->json($result);
    }

    public function customersByMultiple(Request $request)
    {
        $term = request()->get('q', null);
        $customers = \App\Customer::select('id', 'name', 'phone')->where('name', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%")->orWhere('id', 'like', "%{$term}%");

        $customers = $customers->paginate(30);

        $result['total_count'] = $customers->total();
        $result['incomplete_results'] = $customers->nextPageUrl() !== null;

        foreach ($customers as $customer) {
            $result['items'][] = [
                'id' => $customer->id,
                'text' => '<strong>Name</strong>: ' . $customer->name . ' <strong>Phone</strong>: ' . $customer->phone,
            ];
        }

        return response()->json($result);
    }

    public function allWebsites(Request $request)
    {
        $term = request()->get('q', null);
        $websites = \App\StoreWebsite::select('id', 'title');

        $websites = $websites->paginate(30);

        $result['total_count'] = $websites->total();
        $result['incomplete_results'] = $websites->nextPageUrl() !== null;

        foreach ($websites as $website) {
            $result['items'][] = [
                'id' => $website->id,
                'text' => $website->title,
            ];
        }

        return response()->json($result);
    }

    public function timeDoctorAccounts(Request $request)
    {
        $time_doctor_accounts = TimeDoctorAccount::select('id', 'time_doctor_email');

        if (! empty($request->q)) {
            $time_doctor_accounts->where(function ($q) use ($request) {
                $q->where('time_doctor_email', 'LIKE', '%' . $request->q . '%');
            });
        }

        $time_doctor_accounts = $time_doctor_accounts->orderBy('time_doctor_email', 'asc')->paginate(30);

        $result['total_count'] = $time_doctor_accounts->total();
        $result['incomplete_results'] = $time_doctor_accounts->nextPageUrl() !== null;

        foreach ($time_doctor_accounts as $account) {
            $text = $account->time_doctor_email;

            $result['items'][] = [
                'id' => $account->id,
                'text' => $text,
            ];
        }

        return response()->json($result);
    }

    public function timeDoctorAccountsForTask(Request $request)
    {
        $time_doctor_accounts = TimeDoctorAccount::select('id', 'time_doctor_email');

        if (! empty($request->q)) {
            $time_doctor_accounts->where(function ($q) use ($request) {
                $q->where('time_doctor_email', 'LIKE', '%' . $request->q . '%');
            });
        }

        // If I am the member of TimeDoctor, then get my latest time_doctor_account_id
        if (isset(Auth::user()->id)) {
            $myTimeDoctorMember = TimeDoctorMember::where('user_id', Auth::user()->id)->latest()->first();
            if ($myTimeDoctorMember) {
                // Check record exist, Otherwise it will ignore the below condition & get all the remaining accounts as usual. 
                $accountExists = TimeDoctorAccount::where('id', $myTimeDoctorMember->time_doctor_account_id)->where('auth_token', '!=', '')->exists();
                if($accountExists) {
                    $time_doctor_accounts = $time_doctor_accounts->where('id', $myTimeDoctorMember->time_doctor_account_id);
                }
            }
        }

        $time_doctor_accounts = $time_doctor_accounts->where('auth_token', '!=', '');

        $time_doctor_accounts = $time_doctor_accounts->orderBy('time_doctor_email', 'asc')->paginate(30);

        $result['total_count'] = $time_doctor_accounts->total();
        $result['incomplete_results'] = $time_doctor_accounts->nextPageUrl() !== null;

        foreach ($time_doctor_accounts as $account) {
            $text = $account->time_doctor_email;

            $result['items'][] = [
                'id' => $account->id,
                'text' => $text,
            ];
        }

        return response()->json($result);
    }

    public function timeDoctorProjects(Request $request)
    {
        $time_doctor_projects = TimeDoctorProject::select('time_doctor_project_id', 'time_doctor_project_name');

        if (! empty($request->q)) {
            $time_doctor_projects->where(function ($q) use ($request) {
                $q->where('time_doctor_project_name', 'LIKE', '%' . $request->q . '%');
            });
        }
        if (! empty($request->account_id)) {
            $time_doctor_projects->where('time_doctor_account_id', $request->account_id);
        }

        $time_doctor_projects = $time_doctor_projects->orderBy('time_doctor_project_id', 'asc')->paginate(30);

        $result['total_count'] = $time_doctor_projects->total();
        $result['incomplete_results'] = $time_doctor_projects->nextPageUrl() !== null;

        foreach ($time_doctor_projects as $project) {
            $text = $project->time_doctor_project_name;

            $result['items'][] = [
                'id' => $project->time_doctor_project_id,
                'text' => $text,
            ];
        }

        return response()->json($result);
    }

    public function timeDoctorProjectsAjax(Request $request)
    {
        $time_doctor_projects = TimeDoctorProject::select('time_doctor_project_id', 'time_doctor_project_name');

        if (! empty($request->q)) {
            $time_doctor_projects->where(function ($q) use ($request) {
                $q->where('time_doctor_project_name', 'LIKE', '%' . $request->q . '%');
            });
        }
        if (! empty($request->account_id)) {
            $time_doctor_projects->where('time_doctor_account_id', $request->account_id);
        }

        $time_doctor_projects = $time_doctor_projects->orderBy('time_doctor_project_id', 'asc')->get();
        $response_str = "<option value=''>Select Project</option>";
        foreach ($time_doctor_projects as $project) {
            $response_str .= "<option value='" . $project->time_doctor_project_id . "'>" . $project->time_doctor_project_name . '</option>';
        }

        return $response_str;

        /*$result['total_count'] = $time_doctor_projects->total();
        $result['incomplete_results'] = $time_doctor_projects->nextPageUrl() !== null;

        foreach ($time_doctor_projects as $project) {
            $text = $project->time_doctor_project_name;

            $result['items'][] = [
                'id' => $project->time_doctor_project_id,
                'text' => $text,
            ];
        }
        return response()->json($result);*/
    }

    public function taskCategory(Request $request)
    {
        $taskCategories = TaskCategory::where('is_approved', 1)->where('parent_id', 0)->get()->toArray();

        if (! empty($request->q)) {
            $taskCategories->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', $request->q . '%');
            });
        }

        foreach ($taskCategories as $cat) {
            $result['items'][] = [
                'id' => $cat['id'],
                'text' => $cat['title'],
            ];
        }

        return response()->json($result);
    }
}
