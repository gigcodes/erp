<?php

namespace App\Http\Controllers;

use App\StoreWebsiteCategory;
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
        $storecategories = StoreWebsiteCategory::select('category_name', 'id')->wherenotNull('category_name')->take(5)->get();

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
        // dd($request->all());
        $remarks = MagentoFrontendRemark::with(['user'])->where('magento_frontend_docs_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoDriveFilePermissionUpdate(Request $request)
    {

        dd($request);
        $fileId = request('file_id');
        $fileData = MagentoFrontendDocumentation::find(request('id'));
        $readData = request('read');
        $writeData = request('write');
        $permissionEmails = [];
        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        $driveService = new Drive($client);
        // Build a parameters array
        $parameters = [];
        // Specify what fields you want
        $parameters['fields'] = 'permissions(*)';
        // Call the endpoint to fetch the permissions of the file
        $permissions = $driveService->permissions->listPermissions($fileId, $parameters);

        foreach ($permissions->getPermissions() as $permission) {
            $permissionEmails[] = $permission['emailAddress'];
            //Remove Permission
            if ($permission['role'] != 'owner' && $permission['emailAddress'] != (env('GOOGLE_SCREENCAST_FOLDER_OWNER_ID'))) {
                $driveService->permissions->delete($fileId, $permission['id']);
            }
        }
        //assign permission based on requested data
        $index = 1;
        $driveService->getClient()->setUseBatch(true);
        if (! empty($readData)) {
            $batch = $driveService->createBatch();
            foreach ($readData as $email) {
                $userPermission = new Drive\Permission([
                    'type' => 'user',
                    'role' => 'reader',
                    'emailAddress' => $email,
                ]);

                $request = $driveService->permissions->create($fileId, $userPermission, ['fields' => 'id']);
                $batch->add($request, 'user' . $index);
                $index++;
            }
            $results = $batch->execute();
        }
        if (! empty($writeData)) {
            $batch = $driveService->createBatch();
            foreach ($writeData as $email) {
                $userPermission = new Drive\Permission([
                    'type' => 'user',
                    'role' => 'writer',
                    'emailAddress' => $email,
                ]);

                $request = $driveService->permissions->create($fileId, $userPermission, ['fields' => 'id']);
                $batch->add($request, 'user' . $index);
                $index++;
            }
            $results = $batch->execute();
        }
        $fileData->read = ! empty($readData) ? implode(',', $readData) : null;
        $fileData->write = ! empty($writeData) ? implode(',', $writeData) : null;
        $fileData->save();

        return back()->with('success', 'Permission successfully updated.');
    }

}
