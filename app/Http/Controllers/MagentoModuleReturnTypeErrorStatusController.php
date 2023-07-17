<?php

namespace App\Http\Controllers;
use App\Models\MagentoModuleReturnTypeErrorStatus;
use App\Models\MagentoModuleReturnTypeErrorHistoryStatus;
use App\Http\Requests\MagentoModule\MagentoModuleReturntypeStatusRequest;
use Illuminate\Http\Request;
use Request as GlobalRequest;

class MagentoModuleReturnTypeErrorStatusController extends Controller
{
    public function __construct()
    {
        //view files
        $this->index_view = 'magento_module_returntype.index';
        $this->create_view = 'Magento Module returntype.create';
        $this->detail_view = 'Magento Module returntype.details';
        $this->edit_view = 'magento_module_returntype.edit';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $items = MagentoModuleReturnTypeErrorStatus::query();

            return datatables()->eloquent($items)->toJson();
        } else {
            $title = 'Magento Module returntype';
            $module_returntypes = MagentoModuleReturnTypeErrorStatus::pluck('magento_module_returntypes', 'id');
            $task_statuses = TaskStatus::pluck('name', 'id');

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
        $title = 'Magento Module returntype';
        $module_categories = MagentoModuleReturnTypeErrorStatus::pluck('magento_module_returntypes', 'id');
        $task_statuses = TaskStatus::pluck('name', 'id');

        return view($this->create_view, compact('module_categories', 'title', 'task_statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $magentoerror = new  MagentoModuleReturnTypeErrorStatus();
      $magentoerror->return_type_name = $request->return_type_name;
      $magentoerror->save();
        return response()->json([
                    'status' => true,
                    'data' => $magentoerror,
                    'message' => 'Stored successfully',
                    'status_name' => 'success',
                ], 200);
    }


    public function returnTypeHistory(Request $request)
    {
        $histories = MagentoModuleReturnTypeErrorHistoryStatus::with(['newLocation','oldLocation','user'])->where('magento_module_id', $request->id)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }
}
