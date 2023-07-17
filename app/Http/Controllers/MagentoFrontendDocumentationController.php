<?php

namespace App\Http\Controllers;

use App\SiteDevelopmentCategory;
use Illuminate\Http\Request;
use App\Models\MagentoFrontendDocumentation;
use App\Models\MagentoFrontendRemark;
use Auth;
use App\Jobs\UploadGoogleDriveScreencast;
use Google\Client;
use Google\Service\Drive;

class MagentoFrontendDocumentationController extends Controller
{
    public function magentofrontenDocs(Request $request)
    {
        $storecategories = SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();

        if ($request->ajax()) {
            $items = MagentoFrontendDocumentation::with('storeWebsiteCategory')
            ->select(
                'magento_frontend_docs.*',
                'magento_frontend_docs.location',
                'magento_frontend_docs.admin_configuration',
                'magento_frontend_docs.frontend_configuration',
                'magento_frontend_docs.file_name',
            )
            ->join('store_website_categories', 'store_website_categories.id', '=', 'magento_frontend_docs.store_website_category_id');

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


            return view('magento-frontend-documentation.index',$storecategories);
        }

        return view('magento-frontend-documentation.index',$storecategories);
    }

    public function magentofrontendStore(Request $request)
    {
        $data = $this->validate($request, [
            'file' => ['required', 'array'],
            'file.*' => ['required', 'file'],
            'read' => ['sometimes'],
            'write' => ['sometimes'],
        ]);

        $magentofrontenddocs = [];

        foreach ($data['file'] as $file) {
            $magentofrontenddoc = new MagentoFrontendDocumentation();
            $magentofrontenddoc->file_name = $file->getClientOriginalName();
            $magentofrontenddoc->extension = $file->extension();
            $magentofrontenddoc->user_id = Auth::id();
            $magentofrontenddoc->store_website_category_id = $request->magento_docs_category_id;
            $magentofrontenddoc->location = $request->location;
            $magentofrontenddoc->admin_configuration = $request->admin_configuration;
            $magentofrontenddoc->frontend_configuration = $request->frontend_configuration;
            $magentofrontenddoc->read = isset($data['read']) ? implode(',', $data['read']) : null;
            $magentofrontenddoc->write = isset($data['write']) ? implode(',', $data['write']) : null;

            $magentofrontenddoc->save();

            UploadGoogleDriveScreencast::dispatchNow($magentofrontenddoc, $file);

            $magentofrontenddocs[] = $magentofrontenddoc;
        }

        return response()->json([
            'status' => true,
            'data' => $magentofrontenddocs,
            'message' => 'Magento frontend Documentation created successfully',
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
        $remarks = MagentoFrontendRemark::with(['user'])->where('magento_frontend_docs_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

}
