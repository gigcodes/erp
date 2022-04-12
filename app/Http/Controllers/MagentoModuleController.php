<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ModuleCategory;
use App\MagentoModule;
use App\Setting;
use App\Http\Requests\MagentoModule\MagentoModuleRequest;

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

            $items = MagentoModule::join('module_categories', 'module_categories.id', 'magento_modules.module_category_id')
                ->select('magento_modules.*', 'module_categories.category_name');

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
            return view($this->index_view, compact('module_categories', 'title'));
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
        return view($this->create_view, compact('module_categories', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MagentoModuleRequest $request)
    {
        $input = $request->except(['_token']);

        $category = MagentoModule::create($input);

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
        return view($this->edit_view, compact('module_categories', 'title', 'magento_module'));
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

}