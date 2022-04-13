<?php

namespace App\Http\Controllers;

use App\Http\Requests\MagentoModule\MagentoModuleRemarkRequest;
use Illuminate\Http\Request;
use App\ModuleCategory;
use App\MagentoModule;
use App\Setting;
use App\Http\Requests\MagentoModule\MagentoModuleRequest;
use App\MagentoModuleRemark;
use App\TaskStatus;
use Auth;

class MagentoModuleController extends Controller
{


    public function __construct()
    {
        //view files
        $this->index_view = 'magento_module.index';
        $this->create_view = 'magento_module.create';
        $this->detail_view = 'magento_module.details';
        $this->edit_view = 'magento_module.edit';

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // dd($request->all(), $request->module_category_id);
            
            $items = MagentoModule::with(['lastRemark'])
                ->join('module_categories', 'module_categories.id', 'magento_modules.module_category_id')
                ->leftJoin('task_statuses', 'task_statuses.id', 'magento_modules.task_status')
                ->select('magento_modules.*', 'module_categories.category_name', 'task_statuses.name as task_name');

            if (isset($request->module) && !empty($request->module)) {
                $items->where('magento_modules.module', 'Like', '%'. $request->module .'%');
            }
            if (isset($request->module_type) && !empty($request->module_type)) {
                $items->where('magento_modules.module_type', 'Like', '%'. $request->module_type .'%');
            }
            if (isset($request->is_customized)) {
                $items->where('magento_modules.is_customized', $request->is_customized );
            }

            if (isset($request->module_category_id) && !empty($request->module_category_id)) {
                $items->where('magento_modules.module_category_id', $request->module_category_id );
            }

            return datatables()->eloquent($items)->toJson();
        } else {
            $title = 'Magento Module';
            $module_categories = ModuleCategory::where('status',1)->get()->pluck('category_name', 'id');
            $task_statuses = TaskStatus::pluck("name", "id");
            return view($this->index_view, compact('title', 'module_categories', 'task_statuses'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Magento Module';
        $module_categories = ModuleCategory::where('status',1)->get()->pluck('category_name', 'id');
        $task_statuses = TaskStatus::pluck("name", "id");
        return view($this->create_view, compact('module_categories', 'title', 'task_statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MagentoModuleRequest $request)
    {
        // dd($request->all());
        $input = $request->except(['_token']);

        $magento_module = MagentoModule::create($input);

        return redirect()->route('magento_modules.index')
            ->with('success', "Created Successfully ");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(MagentoModule $magento_module)
    {
        $title = 'Magento Module Details';

        if (request()->ajax() && $magento_module) {
            return response()->json([
                'data' => view('magento_module.partials.data', compact('magento_module'))->render(),
                'title' => $title,
                'code' => 200
            ], 200);
        }else{
            return response()->json([
                'data' => "",
                'title' => $title,
                'code' => 500
            ], 500);

        }
        
        return view($this->detail_view, compact( 'title', 'magento_module'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(MagentoModule $magento_module)
    {
        $title = 'Magento Module';
        $module_categories = ModuleCategory::where('status',1)->get()->pluck('category_name', 'id');
        $task_statuses = TaskStatus::pluck("name", "id");
        return view($this->edit_view, compact('module_categories', 'title', 'magento_module', 'task_statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  MagentoModule $magento_module
     * @return \Illuminate\Http\Response
     */
    public function update(MagentoModuleRequest $request, MagentoModule $magento_module)
    {
        $input = $request->except(['_token']);

        $category = $magento_module->update($input);
        
        return redirect()->back()->with('success', "Updated Successfully ");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  MagentoModule $magento_module
     * @return \Illuminate\Http\Response
     */
    public function destroy(MagentoModule $magento_module)
    {
        $data = $magento_module->delete();
        
        if($data){
            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully',
                'status_name' => 'success'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Deleted unsuccessfully',
                'status_name' => 'error'
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeRemark(MagentoModuleRemarkRequest $request)
    {
        
        $input = $request->except(['_token']);
        $input['user_id'] = Auth::user()->id;
        
        $magento_module_remark = MagentoModuleRemark::create($input);

        if($magento_module_remark){
            $update = MagentoModule::where('id',$request->magento_module_id)->update(['last_message' => $request->remark]);
            // dd($update, $request->magento_module_id, $request->remark);
            return response()->json([
                'status' => true,
                'message' => 'Remark added successfully',
                'status_name' => 'success'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Remark added unsuccessfully',
                'status_name' => 'error'
            ], 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRemarks($magento_module)
    {
        
        $remarks = MagentoModuleRemark::with(['user'])->where('magento_module_id', $magento_module)->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success'
        ], 200);

    }


}