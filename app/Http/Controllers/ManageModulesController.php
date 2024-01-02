<?php

namespace App\Http\Controllers;

use App\DeveloperModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Task;
use DB;

class ManageModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Manage Modules';

        return view('manage-modules.index', compact('title'));
    }

    public function records()
    {
        $records = \App\DeveloperModule::leftJoin('developer_tasks as dt', 'dt.module_id', 'developer_modules.id');

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%");
            });
        }

        /*$records = $records->groupBy('developer_modules.id');

        $records = $records->select(['developer_modules.*', \DB::raw('count(dt.id) as total_task')])->get();*/

        $records = $records->whereNull('dt.deleted_at') // Adding condition for deleted_at being null
            ->groupBy('developer_modules.id')
            ->select(['developer_modules.*', \DB::raw('count(dt.id) as total_task')])
            ->get();


        return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);
    }

    public function save(Request $request)
    {
        $post = $request->all();

        $id = $request->get('id', 0);

        $records = DeveloperModule::find($id);

        if (! $records) {
            $records = new DeveloperModule;
            $validator = Validator::make($post, [            
                'name' => 'required|min:1|unique:developer_modules,name,NULL,id,deleted_at,NULL'
            ]);            
        } else {
            $validator = Validator::make($post, [            
                'name' => 'required|unique:developer_modules,name,'.$records->id.',id',
            ]);
        }

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . '<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $records->fill($post);
        $records->save();

        return response()->json(['code' => 200, 'data' => $records]);
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
        ]);

        $data = $request->except('_token');

        DeveloperModule::create($data);

        return redirect()->route('vendors.index')->withSuccess('You have successfully created a vendor category!');
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
     * Edit Page
     *
     * @param  Request  $request [description]
     */
    public function edit(Request $request, $id)
    {
        $modal = DeveloperModule::where('id', $id)->first();

        if ($modal) {
            return response()->json(['code' => 200, 'data' => $modal]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    /**
     * Update the specified resource in storage.
     *
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

    /**
     * delete Page
     *
     * @param  Request  $request [description]
     */
    public function delete(Request $request, $id)
    {
        $developerModule = DeveloperModule::where('id', $id)->first();

        $isExist = \App\DeveloperTask::where('module_id', $id)->first();
        if ($isExist) {
            return response()->json(['code' => 500, 'error' => 'Module is assigned to developer , Please update module before delete.']);
        }

        if ($developerModule) {
            $developerModule->delete();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong id!']);
    }

    public function mergeModule(Request $request)
    {
        $toModule = $request->get('to_module');
        $fromModule = $request->get('from_module');

        if (empty($toModule)) {
            return response()->json(['code' => 500, 'error' => 'Merge module is missing']);
        }

        if (empty($fromModule)) {
            return response()->json(['code' => 500, 'error' => 'Please select module before select merge module']);
        }

        if (in_array($toModule, $fromModule)) {
            return response()->json(['code' => 500, 'error' => 'Merge module can not be same']);
        }

        $module = \App\DeveloperModule::where('id', $toModule)->first();
        $allMergeModule = \App\DeveloperTask::whereIn('module_id', $fromModule)->get();

        if ($module) {
            // start to merge first
            if (! $allMergeModule->isEmpty()) {
                foreach ($allMergeModule as $amc) {
                    $amc->module_id = $module->id;
                    $amc->save();
                }
            }
            // once all merged category store then delete that category from table
            \App\DeveloperModule::whereIn('id', $fromModule)->delete();
        }

        return response()->json(['code' => 200, 'data' => [], 'messages' => 'Module has been merged successfully']);
    }

    public function removeDeveloperModules()
    {
        $modules = DeveloperModule::groupBy('name')->orderBy('id', 'ASC')->get();

        $DeveloperModule = [];
        if(!empty($modules)){
            foreach ($modules as $key => $value) {
                $otherModules = [];
                $otherModules = DeveloperModule::where('name', $value['name'])->where('id', '!=', $value['id'])->pluck('id')->toArray();

                if(!empty($otherModules)){
                    $DeveloperModule[$key] = $value;
                    $DeveloperModule[$key]['other_modules'] = $otherModules;

                    $allMergeModule = \App\DeveloperTask::whereIn('module_id', $otherModules)->update(['module_id' => $value['id']]);

                    \App\DeveloperModule::whereIn('id', $otherModules)->delete();

                }
            }
        }

        return $DeveloperModule;
    }

    public function taskCount($module_id, $search_keyword = '')
    {
        $taskStatistics['Devtask'] = \App\DeveloperTask::where('module_id', $module_id)->select();

        $query = \App\DeveloperTask::leftjoin('users', 'users.id', 'developer_tasks.assigned_to')->where('module_id', $module_id)->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
        $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));

        if(!empty($search_keyword)){
            if($search_keyword!='search'){
                $query = $query->where(function ($q) use ($search_keyword) {
                    $q->where('developer_tasks.id', 'LIKE', "%$search_keyword%");
                    $q->orWhere('users.name', 'LIKE', "%$search_keyword%");
                    $q->orWhere('developer_tasks.status', 'LIKE', "%$search_keyword%");
                    $q->orWhere('developer_tasks.task', 'LIKE', "%$search_keyword%");
                });
            }
        }

        $taskStatistics = $query->get();
        /*//print_r($taskStatistics);
        $othertask = Task::where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select();
        $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
        $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
        $othertaskStatistics = $query1->get();
        $merged = $othertaskStatistics->merge($taskStatistics);*/

        $merged = $taskStatistics;

        return response()->json(['code' => 200, 'taskStatistics' => $merged]);
    }
}
