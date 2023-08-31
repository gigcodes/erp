<?php

namespace App\Http\Controllers;

use Auth;
use App\MagentoModule;
use Illuminate\Http\Request;
use App\PostmanRequestCreate;
use App\SiteDevelopmentCategory;
use App\Jobs\UploadGoogleDriveScreencast;
use App\Models\MagentoBackendDocumentation;
use App\Models\MagentoBackendDocumentationHistory;

class MagentoBackendDocumentationController extends Controller
{
    public function magentoBackendeDocs(Request $request)
    {
        $storecategories = SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();
        $postManAPi = PostmanRequestCreate::select('request_url', 'id')->groupBy('request_url')->get();
        $magentoModules = MagentoModule::select('module', 'id')->groupBy('module')->get();

        if ($request->ajax()) {
            $items = MagentoBackendDocumentation::with('siteDevelopementCategory', 'postmamRequest', 'magentoModule', 'user')
            ->select(
                'magento_backend_docs.*',
                'magento_backend_docs.features',
                'magento_backend_docs.bug',
                'magento_backend_docs.bug_details',
                'magento_backend_docs.bug_resolution',
                'magento_backend_docs.updated_by'
            )
            ->join('site_development_categories', 'site_development_categories.id', '=', 'magento_backend_docs.site_development_category_id')
            ->join('postman_request_creates', 'postman_request_creates.id', '=', 'magento_backend_docs.post_man_api_id')
            ->join('magento_modules', 'magento_modules.id', '=', 'magento_backend_docs.mageneto_module_id');

            return datatables()->eloquent($items)->addColumn('categories', $storecategories)->addColumn('postManAPi', $postManAPi)->addColumn('magentoModules', $magentoModules)->toJson();
        } else {
            return view('magento-backend-documentation.index', $storecategories);
        }

        return view('magento-backend-documentation.index', $storecategories);
    }

    public function getBackendDropdownDatas(Request $request)
    {
        $storecategories = SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();
        $postManAPi = PostmanRequestCreate::select('request_url', 'id')->groupBy('request_url')->get();
        $magentoModules = MagentoModule::select('module', 'id')->groupBy('module')->get();

        return response()->json(['storecategories' => $storecategories, 'postManAPi' => $postManAPi, 'magentoModules' => $magentoModules]);
    }

    public function magentoBackendStore(Request $request)
    {
        $magentobackenddoc = new MagentoBackendDocumentation();
        $magentobackenddoc->site_development_category_id = $request->site_development_category;
        $magentobackenddoc->post_man_api_id = $request->post_man_api_id;
        $magentobackenddoc->mageneto_module_id = $request->mageneto_module_id;
        $magentobackenddoc->features = $request->features;
        $magentobackenddoc->bug = $request->bug;
        $magentobackenddoc->bug_details = $request->bug_details;
        $magentobackenddoc->bug_resolution = $request->bug_resolution;
        $magentobackenddoc->template_file = $request->template_file;
        
        $magentobackenddoc->updated_by = Auth::id();
        $magentobackenddoc->save();

        return response()->json([
            'status' => true,
            'data' => $magentobackenddoc,
            'message' => 'Magento Backend Documentation created successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendOptions(Request $request)
    {
        $oldData = MagentoBackendDocumentation::where('id', (int) $request->id)->first();
        $updateMagentoModule = MagentoBackendDocumentation::where('id', (int) $request->id)->update([$request->columnName => $request->data]);
        $newData = MagentoBackendDocumentation::where('id', (int) $request->id)->first();

        $columnname = $request->columnName;
        $newData = $request->data;
        $backendId = $request->id;

        if ($request->columnName == 'site_development_category_id') {
            $oldId = $oldData->site_development_category_id;
        }
        if ($request->columnName == 'post_man_api_id') {
            $oldId = $oldData->post_man_api_id;
        }
        if ($request->columnName == 'mageneto_module_id') {
            $oldId = $oldData->mageneto_module_id;
        }

        $this->magentoBackendHistorysave($backendId, $oldId, $newData, $columnname);

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

    public function magentoBackendCategoryHistoryShow(Request $request)
    {
        $histories = MagentoBackendDocumentationHistory::with(['siteDevelopementOldCategory', 'siteDevelopementNewCategory', 'user'])
        ->where('magento_backend_docs_id', $request->id)
        ->Where('column_name', $request->column)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendPostmanHistoryShow(Request $request)
    {
        $histories = MagentoBackendDocumentationHistory::with(['postmanoldrequestapi', 'postmannewrequestapi', 'user'])
        ->where('magento_backend_docs_id', $request->id)
        ->Where('column_name', $request->column)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendModuleHistoryShow(Request $request)
    {
        $histories = MagentoBackendDocumentationHistory::with(['magneteoldmodule', 'magnetenewmodule', 'user'])
        ->where('magento_backend_docs_id', $request->id)
        ->Where('column_name', $request->column)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function magentobackendstoreRemark(Request $request)
    {
        $magentobackendremark = MagentoBackendDocumentation::find($request->magento_back_end_id);
        $oldId = $magentobackendremark->api_remark;
        $magentobackendremark->api_remark = $request->remark;
        $magentobackendremark->save();

        $backendId = $request->magento_back_end_id;
        $newData = $request->remark;
        $columnname = 'api_remark';

        $magnetohistory = new MagentoBackendDocumentationHistory();
        $magnetohistory->magento_backend_docs_id = $backendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->old_value = $oldId;
        $magnetohistory->new_value = $newData;
        $magnetohistory->user_id = \Auth::id();
        $magnetohistory->save();

        return response()->json([
            'status' => true,
            'data' => $magentobackendremark,
            'message' => 'magneto backend Remark Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendRemarkHistoryShow(Request $request)
    {
        $histories = MagentoBackendDocumentationHistory::with(['user'])
        ->where('magento_backend_docs_id', $request->id)
        ->Where('column_name', $request->column)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackenddescriptionHistoryShow(Request $request)
    {
        $histories = MagentoBackendDocumentationHistory::with(['user'])
        ->where('magento_backend_docs_id', $request->id)
        ->Where('column_name', $request->column)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendAdminHistoryShow(Request $request)
    {
        $histories = MagentoBackendDocumentationHistory::with(['user'])
        ->where('magento_backend_docs_id', $request->id)
        ->Where('column_name', $request->column)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoStorebackendFolder(Request $request)
    {
        $magentofrontendremark = MagentoBackendDocumentation::find($request->magento_back_end_id);
        $oldId = $magentofrontendremark->description;
        $magentofrontendremark->description = $request->description;
        $magentofrontendremark->save();

        $backendId = $request->magento_back_end_id;
        $newData = $request->description;
        $columnname = 'description';

        $magnetohistory = new MagentoBackendDocumentationHistory();
        $magnetohistory->magento_backend_docs_id = $backendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->old_value = $oldId;
        $magnetohistory->new_value = $newData;
        $magnetohistory->user_id = \Auth::id();
        $magnetohistory->save();

        return response()->json([
            'status' => true,
            'data' => $magentofrontendremark,
            'message' => 'magneto backend description Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoStorebackendadminConfig(Request $request)
    {
        $magentofrontendremark = MagentoBackendDocumentation::find($request->magento_back_end_id);
        $oldId = $magentofrontendremark->admin_configuration;
        $magentofrontendremark->admin_configuration = $request->adminconfig;
        $magentofrontendremark->save();

        $backendId = $request->magento_back_end_id;
        $newData = $request->adminconfig;
        $columnname = 'admin_configuration';

        $magnetohistory = new MagentoBackendDocumentationHistory();
        $magnetohistory->magento_backend_docs_id = $backendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->old_value = $oldId;
        $magnetohistory->new_value = $newData;
        $magnetohistory->user_id = \Auth::id();
        $magnetohistory->save();

        return response()->json([
            'status' => true,
            'data' => $magentofrontendremark,
            'message' => 'magneto backend admin_configuration Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendDescriptionUpload(Request $request)
    {
        $magentobackenddoc = MagentoBackendDocumentation::find($request->magento_backend_id);

        $backendId = $request->magento_backend_id;
        $newData = $magentobackenddoc->google_drive_file_id;
        $columnname = 'description';

        $magnetohistory = new MagentoBackendDocumentationHistory();
        $magnetohistory->magento_backend_docs_id = $backendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->new_value = $newData;
        $magnetohistory->user_id = \Auth::id();
        $magnetohistory->save();

        if ($request->hasFile('upload_description')) {
            foreach ($request->upload_description as $file) {
                $magentobackenddoc->description_file_name = $file->getClientOriginalName();
                $magentobackenddoc->description_extension = $file->extension();

                $magentobackenddoc->save();

                UploadGoogleDriveScreencast::dispatchNow($magentobackenddoc, $file);

                $magentobackenddocs[] = $magentobackenddoc;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'magneto backend Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendadminConfigUpload(Request $request)
    {
        $magentobackenddoc = MagentoBackendDocumentation::find($request->magento_backend_id);

        $backendId = $request->magento_backend_id;
        $newData = $magentobackenddoc->google_drive_file_id;
        $columnname = 'admin_configuration';

        $magnetohistory = new MagentoBackendDocumentationHistory();
        $magnetohistory->magento_backend_docs_id = $backendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->new_value = $newData;
        $magnetohistory->user_id = \Auth::id();
        $magnetohistory->save();
 
        if ($request->hasFile('child_folder_image')) {
            foreach ($request->child_folder_image as $file) {
                $magentobackenddoc->admin_configuration_file_name = $file->getClientOriginalName();
                $magentobackenddoc->admin_configuration_extension = $file->extension();

                $magentobackenddoc->save();

                UploadGoogleDriveScreencast::dispatchNow($magentobackenddoc, $file);

                $magentobackenddocs[] = $magentobackenddoc;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'magneto backend Updated succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentobackenddelete($id)
    {
        $magenotobackend = MagentoBackendDocumentation::find($id);

        if (! $magenotobackend) {
            return response()->json(['message' => 'Magento backend not found.'], 404);
        }

        $magenotobackend->delete();

        return response()->json(['message' => 'Magento backend deleted successfully.']);
    }

    private function magentoBackendHistorysave($backendId, $oldId, $newData, $columnname)
    {
        $magnetohistory = new MagentoBackendDocumentationHistory();
        $magnetohistory->magento_backend_docs_id = $backendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->old_id = $oldId;
        $magnetohistory->new_id = $newData;
        $magnetohistory->user_id = \Auth::id();
        $magnetohistory->save();

        return response()->json([
            'status' => true,
            'data' => $magnetohistory,
            'message' => 'Magento Bcakend Documentation created successfully',
            'status_name' => 'success',
        ], 200);
    }
}
