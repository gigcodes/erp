<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use Illuminate\Http\Request;
use DataTables;
use Validator;

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
            $query->select('id','sku','name','supplier',\DB::raw('(CASE WHEN status_id = 36 THEN "Unknown Category" WHEN status_id = 37 THEN "Unknown Color"  WHEN status_id = 38 THEN "Unknown Size" WHEN status_id = 40 THEN "Unknown Measurement" ELSE "" END) AS attribute_name'));
            if(isset($request->status_id) && !empty($request->status_id)) {
                $query->where('status_id',$request->status_id);
            } else {
                $query->whereIn('status_id',[StatusHelper::$unknownSize,StatusHelper::$unknownMeasurement,StatusHelper::$unknownCategory,StatusHelper::$unknownColor]);
            }
            $query->where('stock','>=',1);    
            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="get-product-attribute-detail btn btn-warning btn-sm">Update</a>';
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
    
    public function attributeAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attribute_id' => 'required',
            'attribute_value' => 'required',
        ]);

        if ($validator->fails()) {
            $return = ['code' => 500, 'message' => 'Invalid request parameters'];
        } else {
            
            $data['product_id'] = $request->product_id;
            $data['attribute_id'] = $request->attribute_id;
            $data['attribute_value'] = $request->attribute_value;
            
            \App\Jobs\AttributeAssignment::dispatch($data)->onQueue('attribute_assignment');
            
            $return = ['code' => 200, 'message' => 'Attribute assignment request is submitted'];
        }
        
        return response()->json($return);
    }
    
    public function getProductAttributeDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            $return = ['code' => 500, 'message' => 'Invalid request parameters'];
        } else {
            
            $product = Product::select('id','sku','name','supplier','status_id',\DB::raw('(CASE WHEN status_id = 36 THEN "Unknown Category" WHEN status_id = 37 THEN "Unknown Color"  WHEN status_id = 38 THEN "Unknown Size" WHEN status_id = 40 THEN "Unknown Measurement" ELSE "" END) AS attribute_name'))
                            ->where('id',$request->product_id)
                            ->first();
            if(isset($product) && !empty($product)) {
                $return = ['code' => 200, 'message' => 'Success','results'=> $product];
            } else {
                $return = ['code' => 500, 'message' => 'No Results Found.'];
            }
        }
        
        return response()->json($return);
    }

}
