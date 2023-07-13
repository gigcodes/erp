<?php

namespace App\Http\Controllers;

use App\StoreWebsiteCategory;
use Illuminate\Http\Request;
use App\Models\MagentoFrontendDocumentation;
use App\Models\MagentoFrontendRemark;

class MagentoFrontendDocumentationController extends Controller
{
    public function magentofrontenDocs(Request $request)
    {
        $storecategories = StoreWebsiteCategory::select('category_name', 'id')->wherenotNull('category_name')->take(5)->get();

        if ($request->ajax()) {
            $items = MagentoFrontendDocumentation::with('storeWebsiteCategory')
                ->select(
                    'magento_frontend_docs.*',
                    'magento_frontend_docs.location',
                    'magento_frontend_docs.admin_configuration',
                    'magento_frontend_docs.frontend_configuration',
                    'magento_frontend_docs.store_website_category_id'
                )
                ->join('store_website_categories', 'store_website_categories.id', '=', 'magento_frontend_docs.store_website_category_id')
                ->select('magento_frontend_docs.*', 'store_website_categories.category_name');


                if (isset($request->frontend_configuration)) {
                    $items->where('magento_frontend_docs.frontend_configuration','LIKE', '%' . $request->frontend_configuration . '%');
                }
                if (isset($request->admin_configuration)) {
                    $items->where('magento_frontend_docs.admin_configuration','LIKE', '%' . $request->admin_configuration . '%');
                }
                if (isset($request->location)) {
                    $items->where('magento_frontend_docs.location','LIKE', '%' . $request->location . '%');
                }
                if (isset($request->categoryname)) {
                    $items->where('magento_frontend_docs.store_website_category_id', $request->categoryname);
                }
        
            return datatables()->eloquent($items)->addColumn('categories', $storecategories)->toJson();
        } else {


            return view('magento-frontend-documentation/.index',$storecategories);
        }

        return view('magento-frontend-documentation/.index',$storecategories);
    }

    public function magentofrontendStore(Request $request)
    {
        $magentofrontenddocs =   new MagentoFrontendDocumentation();
        $magentofrontenddocs->store_website_category_id = $request->magento_docs_category_id;
        $magentofrontenddocs->location = $request->location;
        $magentofrontenddocs->admin_configuration = $request->admin_configuration;
        $magentofrontenddocs->frontend_configuration = $request->frontend_configuration;
        $magentofrontenddocs->save();
        
        return response()->json([
            'status' => true,
            'data' => $magentofrontenddocs,
            'message' => 'magneto frontend Documentation created succesfully',
            'status_name' => 'success',
        ], 200);

    }


    public function magentofrontendstoreRemark(Request $request)
    {
        $magentofrontendremark =   new MagentoFrontendRemark();
        $magentofrontendremark->magento_frontend_docs_id = $request->magento_front_end_id;
        $magentofrontendremark->remark = $request->remark;
        $magentofrontendremark->user_id =  \Auth::id();
        $magentofrontendremark->save();

        return response()->json([
            'status' => true,
            'data' => $magentofrontendremark,
            'message' => 'magneto frontend Remark Added succesfully',
            'status_name' => 'success',
        ], 200);
       
    }

    public function magentofrontendgetRemarks(Request $request)
    {
        // dd($request->all());
        $remarks = MagentoFrontendRemark::with(['user'])->where('magento_frontend_docs_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }
}
