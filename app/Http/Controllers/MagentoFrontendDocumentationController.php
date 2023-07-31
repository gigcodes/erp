<?php

namespace App\Http\Controllers;

use App\SiteDevelopmentCategory;
use Illuminate\Http\Request;
use App\Models\MagentoFrontendDocumentation;
use App\Models\MagentoFrontendRemark;
use App\Models\MagentoFrontendHistory;
use Auth;
use App\Jobs\UploadGoogleDriveScreencast;
use Google\Client;
use Google\Service\Drive;
use App\Models\MagentoFrontendCategoryHistory;
use App\Models\MagentoFrontendParentFolder;
use App\Models\MagentoFrontendChildFolder;

class MagentoFrontendDocumentationController extends Controller
{
    public function magentofrontenDocs(Request $request)
    {
        $storecategories = SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();

        if ($request->ajax()) {
            $items = MagentoFrontendDocumentation::with('storeWebsiteCategory','user')
            ->select(
                'magento_frontend_docs.*',
                'magento_frontend_docs.location',
                'magento_frontend_docs.admin_configuration',
                'magento_frontend_docs.frontend_configuration',
                'magento_frontend_docs.file_name',
            )
            ->join('site_development_categories', 'site_development_categories.id', '=', 'magento_frontend_docs.store_website_category_id');

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
        $magentofrontenddocs = [];

        $magentofrontenddoc = new MagentoFrontendDocumentation();
        $magentofrontenddoc->user_id = Auth::id();
        $magentofrontenddoc->store_website_category_id = $request->magento_docs_category_id;
        $magentofrontenddoc->location = $request->location;
        $magentofrontenddoc->admin_configuration = $request->admin_configuration;
        $magentofrontenddoc->frontend_configuration = $request->frontend_configuration;
        $magentofrontenddoc->read = $request->read ? implode(',',  $request->read) : null;
        $magentofrontenddoc->write =$request->write ? implode(',', $request->write) : null;
        $magentofrontenddoc->save();

        if ($request->hasFile('child_folder_image')) {
            $file = $request->file('child_folder_image');
            $name = uniqid() . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/magentofrontend-child-image');
            $file->move($destinationPath, $name); 
            $magentofrontenddoc->child_folder_image  = $name;
            $magentofrontenddoc->save();
        }

        if ($request->hasFile('file')) {
            foreach ($request->file as $file) {
                $magentofrontenddoc->file_name = $file->getClientOriginalName();
                $magentofrontenddoc->extension = $file->extension();
            
                $magentofrontenddoc->save();

                UploadGoogleDriveScreencast::dispatchNow($magentofrontenddoc, $file);

                $magentofrontenddocs[] = $magentofrontenddoc;
            }
        }

       $magnetohistory =  new MagentoFrontendHistory();

       $magnetohistory->magento_frontend_docs_id =  $magentofrontenddoc->id;
        $magnetohistory->store_website_category_id = $request->magento_docs_category_id;
        $magnetohistory->location = $request->location;
        $magnetohistory->admin_configuration =  $request->admin_configuration;
        $magnetohistory->frontend_configuration  = $request->frontend_configuration;
        $magnetohistory->updated_by =  \Auth::id();
        $magnetohistory->save();

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

    public function magentofrontendEdit($id)
    {
        $magento_module = MagentoFrontendDocumentation::find($id);
        $storecategories = SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();


        if ($magento_module) {
            return response()->json(['code' => 200, 'data' => $magento_module ,'storecategories' => $storecategories    ]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    public function magentofrontendOptions(Request $request)
    {
        $oldData = MagentoFrontendDocumentation::where('id', (int) $request->id)->first();
        $updateMagentoModule = MagentoFrontendDocumentation::where('id', (int) $request->id)->update([$request->columnName => $request->data]);
        $newData = MagentoFrontendDocumentation::where('id', (int) $request->id)->first();

        if ($request->columnName == "store_website_category_id") {
             $oldCategoryId = $oldData->store_website_category_id;

            $this->saveCategoryHistory($oldData, $oldCategoryId , $request->data);
        }

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

    public function magentofrontendUpdate (Request $request)
    {
        $oldData = MagentoFrontendDocumentation::where('id', (int) $request->id)->first();
        $oldData->location  = $request->location ;
        $oldData->admin_configuration  =$request->admin_configuration ;
        $oldData->frontend_configuration  = $request->frontend_configuration;
        $oldData->save();

        if ($request->hasFile('child_folder_image')) {
            $file = $request->file('child_folder_image');
            $name = uniqid() . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/magentofrontend-child-image');
            $file->move($destinationPath, $name); 
        } else {
            $name = null;
        }
        
        if (!is_null($name)) {
            $oldData->child_folder_image = $name;
            $oldData->save();
        }
        
        $magnetohistory =  new MagentoFrontendHistory();
        $magnetohistory->magento_frontend_docs_id = $oldData->id;
        $magnetohistory->store_website_category_id = $oldData->store_website_category_id;
        $magnetohistory->location = $request->location;
        $magnetohistory->admin_configuration =  $request->admin_configuration;
        $magnetohistory->frontend_configuration  = $request->frontend_configuration;
        $magnetohistory->frontend_configuration  = $request->frontend_configuration;
        $magnetohistory->updated_by =  \Auth::id();
        $magnetohistory->save();

        return response()->json([
            'status' => true,
            'message' => 'Updated successfully',
            'status_name' => 'success',
            'code' => 200,
        ], 200);

    }


    public function magentofrontendhistoryShow($id)
    {
        $magento_module_api_histories = MagentoFrontendHistory::with([
            'user:id,name',
            'storeWebsiteCategory:id,title',
        ])
            ->where('magento_frontend_docs_id', $id)->get();
 
        return response()->json([
            'status' => true,
            'data' => $magento_module_api_histories,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }
    
    public function magentofrontendCategoryHistoryShow($id)
    {
        $histories = MagentoFrontendCategoryHistory::with(['newCategory','oldCategory','user'])->where('magento_frontend_docs_id', $id)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }


    public function magentofrontendStoreParentFolder(Request $request)
    {
        $magentofrontendremark =   new MagentoFrontendParentFolder();
        $magentofrontendremark->magento_frontend_docs_id = $request->magento_front_end_id;
        $magentofrontendremark->parent_folder_name = $request->folderName;
        $magentofrontendremark->user_id =  \Auth::id();
        $magentofrontendremark->save();

        return response()->json([
            'status' => true,
            'data' => $magentofrontendremark,
            'message' => 'magneto frontend parent Folder Added succesfully',
            'status_name' => 'success',
        ], 200);
       
    }

    public function magentofrontendgetparentFolder(Request $request)
    {
        $remarks = MagentoFrontendParentFolder::with(['user'])->where('magento_frontend_docs_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontendparentFolderImage(Request $request)
    {
        $parentImage =   new MagentoFrontendParentFolder();

        if ($request->hasFile('parent_folder_image')) {
            $file = $request->file('parent_folder_image');
            $name = uniqid() . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/magentofrontend-parent-image');
            $file->move($destinationPath, $name); 
        } else {
            $name = null;
        }

        $parentImage->parent_image =  $name;
        $parentImage->type = 'image';
        $parentImage->magento_frontend_docs_id =  $request->magento_frontend_id;
        $parentImage->user_id =  \Auth::id();
        $parentImage->save();

        return response()->json([
            'status' => true,
            'data' => $parentImage,
            'message' => 'magneto frontend Parent Image Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontendChildImage(Request $request)
    {
        $childImage =   new MagentoFrontendChildFolder();

        if ($request->hasFile('child_folder_image')) {
            $file = $request->file('child_folder_image');
            $name = uniqid() . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/magentofrontend-child-image');
            $file->move($destinationPath, $name); 
        } else {
            $name = null;
        }

        $childImage->child_image =  $name;
        $childImage->type = 'image';
        $childImage->magento_frontend_docs_id =  $request->magento_frontend_id;
        $childImage->user_id =  \Auth::id();
        $childImage->save();

        return response()->json([
            'status' => true,
            'data' => $childImage,
            'message' => 'magneto frontend Child Image Added succesfully',
            'status_name' => 'success',
        ], 200);

    }
    
    public function magentofrontendChildfolderstore(Request $request)
    {     
        $magentofrontendremark =   new MagentoFrontendChildFolder();
        $magentofrontendremark->magento_frontend_docs_id = $request->magento_front_end_id;
        $magentofrontendremark->child_folder_name = $request->folderName;
        $magentofrontendremark->user_id =  \Auth::id();
        $magentofrontendremark->save();

        return response()->json([
            'status' => true,
            'data' => $magentofrontendremark,
            'message' => 'magneto frontend Child Folder Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontendgetChildFolder(Request $request)
    {
        $childFolder = MagentoFrontendChildFolder::with(['user'])->where('magento_frontend_docs_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $childFolder,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }
    protected function saveCategoryHistory($magentoFrontEnd, $oldCategoryId, $newCategoryId)
    {
        $history = new MagentoFrontendCategoryHistory();
        $history->magento_frontend_docs_id = $magentoFrontEnd->id;
        $history->old_category_id = $oldCategoryId;
        $history->new_category_id = $newCategoryId;
        $history->user_id = Auth::user()->id;
        $history->save();

        return true;
    }


}
