<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use Illuminate\Http\Request;
use DataTables;

class UnknownAttributeProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            
            $query = Product::query();
            if(isset($request->status_id) && !empty($request->status_id)) {
                $query->where('status_id',$request->status_id);
            } else {
                $query->whereIn('status_id',[StatusHelper::$unknownSize,StatusHelper::$unknownMeasurement,StatusHelper::$unknownCategory,StatusHelper::$unknownColor]);
            }
            $query->where('stock','>=',1);    
            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = 'N/A';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $status_list[StatusHelper::$unknownSize] = 'Unknown Size';
        $status_list[StatusHelper::$unknownMeasurement] = 'Unknown Measurement';
        $status_list[StatusHelper::$unknownCategory] = 'Unknown Category';
        $status_list[StatusHelper::$unknownColor] = 'Unknown Color';
        return view('unknown-attribute-product.index',compact('status_list'));
    }

}
