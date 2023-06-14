<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\TaskStatus;
use App\StoreWebsite;
use App\MagentoModule;
use App\MagentoModuleType;
use App\MagentoModuleRemark;
use Illuminate\Http\Request;
use App\MagentoModuleHistory;
use App\MagentoModuleCategory;
use App\Http\Requests\MagentoModule\MagentoModuleRequest;
use App\Http\Requests\MagentoModule\MagentoModuleRemarkRequest;

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
        if (env('PRODUCTION', true)) {
            $users = User::select('name', 'id')->role('Developer')->orderby('name', 'asc')->where('is_active', 1)->get();
        } else {
            $users = User::select('name', 'id')->where('is_active', 1)->orderby('name', 'asc')->get();
        }
        $module_categories = MagentoModuleCategory::select('category_name', 'id')->where('status', 1)->get();
        $magento_module_types = MagentoModuleType::select('magento_module_type', 'id')->get();
        $task_statuses = TaskStatus::select('name', 'id')->get();
        $store_websites = StoreWebsite::select('website', 'id')->get();

        if ($request->ajax()) {
            $items = MagentoModule::with(['lastRemark'])
                ->join('magento_module_categories', 'magento_module_categories.id', 'magento_modules.module_category_id')
                ->join('magento_module_types', 'magento_module_types.id', 'magento_modules.module_type')
                ->join('store_websites', 'store_websites.id', 'magento_modules.store_website_id')
                ->join('users', 'users.id', 'magento_modules.developer_name')
                ->leftJoin('task_statuses', 'task_statuses.id', 'magento_modules.task_status')
                ->select(
                    'magento_modules.*',
                    'magento_module_categories.category_name',
                    'magento_module_types.magento_module_type',
                    'task_statuses.name as task_name',
                    'store_websites.website',
                    'store_websites.title',
                    'users.name as developer_name',
                    'users.id as developer_id'
                );

            if (isset($request->module) && ! empty($request->module)) {
                $items->where('magento_modules.module', 'Like', '%' . $request->module . '%');
            }

            if (isset($request->user_id) && ! empty($request->user_id)) {
                $items->where('users.user_id', $request->user_id);
            }

            if (isset($request->store_website_id) && ! empty($request->store_website_id)) {
                $items->where('magento_modules.store_website_id', $request->store_website_id);
            }

            if (isset($request->module_type) && ! empty($request->module_type)) {
                $items->where('magento_modules.module_type', $request->module_type);
            }

            if (isset($request->task_status) && ! empty($request->task_status)) {
                $items->where('magento_modules.task_status', $request->task_status);
            }

            if (isset($request->is_customized)) {
                $items->where('magento_modules.is_customized', $request->is_customized);
            }

            if (isset($request->module_category_id) && ! empty($request->module_category_id)) {
                $items->where('magento_modules.module_category_id', $request->module_category_id);
            }
            if (isset($request->site_impact)) {
                $items->where('magento_modules.site_impact', $request->site_impact);
            }

            if (isset($request->status)) {
                $items->where('magento_modules.status', $request->status);
            }

            return datatables()->eloquent($items)->addColumn('m_types', $magento_module_types)->addColumn('developer_list', $users)->addColumn('categories', $module_categories)->addColumn('website_list', $store_websites)->toJson();
        } else {
            $title = 'Magento Module';
            $users = $users->pluck('name', 'id');
            $module_categories = $module_categories->pluck('category_name', 'id');
            $magento_module_types = $magento_module_types->pluck('magento_module_type', 'id');
            $task_statuses = $task_statuses->pluck('name', 'id');
            $store_websites = $store_websites->pluck('website', 'id');

            return view($this->index_view, compact('title', 'module_categories', 'magento_module_types', 'task_statuses', 'store_websites', 'users'));
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
        $module_categories = MagentoModuleCategory::where('status', 1)->get()->pluck('category_name', 'id');
        $magento_module_types = MagentoModuleType::get()->pluck('magento_module_type', 'id');
        $task_statuses = TaskStatus::pluck('name', 'id');

        return view($this->create_view, compact('module_categories', 'title', 'task_statuses', 'magento_module_types'));
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

        $data = MagentoModule::create($input);

        if ($data) {
            $input_data = $data->toArray();
            $input_data['magento_module_id'] = $data->id;
            unset($input_data['id']);
            $input_data['user_id'] = auth()->user()->id;
            MagentoModuleHistory::create($input_data);

            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Magento Module saved successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
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
                'code' => 200,
            ], 200);
        } else {
            return response()->json([
                'data' => '',
                'title' => $title,
                'code' => 500,
            ], 500);
        }

        return view($this->detail_view, compact('title', 'magento_module'));
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
        $module_categories = MagentoModuleCategory::where('status', 1)->get()->pluck('category_name', 'id');
        $task_statuses = TaskStatus::pluck('name', 'id');

        return view($this->edit_view, compact('module_categories', 'title', 'magento_module', 'task_statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(MagentoModuleRequest $request, MagentoModule $magento_module)
    {
        $input = $request->except(['_token']);
        $data = $magento_module->update($input);

        if ($data) {
            $input_data = $magento_module->toArray();
            $input_data['magento_module_id'] = $magento_module->id;
            unset($input_data['id']);
            $input_data['user_id'] = auth()->user()->id;
            // dd($input_data);
            MagentoModuleHistory::create($input_data);

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Updated unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(MagentoModule $magento_module)
    {
        $data = $magento_module->delete();

        if ($data) {
            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Deleted unsuccessfully',
                'status_name' => 'error',
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

        if ($magento_module_remark) {
            $update = MagentoModule::where('id', $request->magento_module_id)->update(['last_message' => $request->remark]);
            // dd($update, $request->magento_module_id, $request->remark);
            return response()->json([
                'status' => true,
                'message' => 'Remark added successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Remark added unsuccessfully',
                'status_name' => 'error',
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
            'status_name' => 'success',
        ], 200);
    }

    public function updateMagentoModuleOptions(Request $request)
    {
        $updateMagentoModule = MagentoModule::where('id', (int) $request->id)->update([$request->columnName => $request->data]);

        if ($updateMagentoModule) {
            return response()->json([
                'status' => true,
                'message' => 'Updated successfully',
                'status_name' => 'success',
                'code' => 200,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Updated unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    public function magentoModuleList()
    {
        $magento_modules = MagentoModule::orderBy('module', 'asc')->get();
        $storeWebsites = StoreWebsite::pluck('title', 'id')->toArray();

        return view('magento_module.magento-listing', ['magento_modules' => $magento_modules, 'storeWebsites' => $storeWebsites]);
    }
}
