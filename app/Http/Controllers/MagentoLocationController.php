<?php

namespace App\Http\Controllers;

use App\TaskStatus;
use Illuminate\Http\Request;
use App\MagentoModuleLocation;
use App\Http\Requests\MagentoModule\MagentoModuleLocationRequest;

class MagentoLocationController extends Controller
{
    public function __construct()
    {
        //view files
        $this->index_view  = 'magento_module_location.index';
        $this->create_view = 'Magento Module location.create';
        $this->detail_view = 'Magento Module location.details';
        $this->edit_view   = 'magento_module_location.edit';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = MagentoModuleLocation::query();

            return datatables()->eloquent($items)->toJson();
        } else {
            $title            = 'Magento Module location';
            $module_locations = MagentoModuleLocation::pluck('magento_module_locations', 'id');
            $task_statuses    = TaskStatus::pluck('name', 'id');

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
        $title             = 'Magento Module location';
        $module_categories = MagentoModuleLocation::pluck('magento_module_locations', 'id');
        $task_statuses     = TaskStatus::pluck('name', 'id');

        return view($this->create_view, compact('module_categories', 'title', 'task_statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(MagentoModuleLocationRequest $request)
    {
        $input = $request->except(['_token']);

        $data = MagentoModuleLocation::create($input);

        if ($data) {
            return response()->json([
                'status'      => true,
                'data'        => $data,
                'message'     => 'Stored successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status'      => false,
                'message'     => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
    }
}
