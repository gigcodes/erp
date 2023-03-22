<?php

namespace App\Http\Controllers;

use App\GoogleScreencast;
use App\DeveloperTask;
use App\User;
use Auth;
use App\Jobs\UploadGoogleDriveScreencast;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GoogleScreencastController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //get file list
        $data = GoogleScreencast::orderBy('id', 'desc');
        //fetch task list
        $taskList = DeveloperTask::select('id','subject')->where('task_type_id',1)->orderBy('id', 'desc');
        if(!Auth::user()->isAdmin())
        {
            $taskList = $taskList->where('user_id', Auth::id());
        }
        $tasks = $taskList->get();
        $users = User::select('id','name','email','gmail')->whereNotNull('gmail')->get();
        if ($keyword = request('name')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('file_name', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('docid')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('google_drive_file_id', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('task_id')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('developer_task_id',$keyword);
            });
        }
        if ($keyword = request('user_gmail')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->whereRaw("find_in_set('".$keyword."',google_drive_screencast_upload.read)")->orWhereRaw("find_in_set('".$keyword."',google_drive_screencast_upload.write)");
            });
        }
        if(empty($request->input('name')) && empty($request->input('docid')) && empty($request->input('task_id')) && !Auth::user()->isAdmin())
        {
            $data->where('user_id',Auth::id())->orWhereRaw("find_in_set('".Auth::user()->gmail."',google_drive_screencast_upload.read)")->orWhereRaw("find_in_set('".Auth::user()->gmail."',google_drive_screencast_upload.write)");
        }
        $data = $data->get();

        return view('googledrivescreencast.index', compact('data','tasks','users'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $this->validate($request, [
            'task_id' => ['required'],
            'file' => ['required'],
            'file.*' => ['required'],
            'file_creation_date' => ['required'],
            'file_read' => ['sometimes'],
            'file_write' => ['sometimes'],
            'remarks' => ['sometimes'],
        ]);
        foreach($data['file'] as $file)
        {
            DB::transaction(function () use ($file,$data) {
                $googleScreencast = new GoogleScreencast();
                $googleScreencast->file_name = $file->getClientOriginalName();
                $googleScreencast->extension = $file->extension();
                $googleScreencast->user_id = Auth::id();
                if (isset($data['file_read'])) {
                    $googleScreencast->read = implode(',', $data['file_read']);
                }
                if (isset($data['file_write'])) {
                    $googleScreencast->write = implode(',', $data['file_write']);
                }
                $googleScreencast->remarks = $data['remarks'];
                $googleScreencast->file_creation_date = $data['file_creation_date'];
                $googleScreencast->developer_task_id = $data['task_id'];
                $googleScreencast->save();
                UploadGoogleDriveScreencast::dispatchNow($googleScreencast,$file);
                
            });
    
        }
        
        return back()->with('success', "File is Uploaded to Google Drive.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
    public function filePermissionUpdate(Request $request)
    {
        $fileId = request('file_id');
        $fileData = GoogleScreencast::find(request('id'));
        $readData = request('read');
        $writeData = request('write');
        $permissionEmails=[];
        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        $driveService = new Drive($client);
        // Build a parameters array
        $parameters = array();
        // Specify what fields you want 
        $parameters['fields'] = "permissions(*)";
        // Call the endpoint to fetch the permissions of the file
        $permissions = $driveService->permissions->listPermissions($fileId, $parameters);
        
        foreach ($permissions->getPermissions() as $permission){
            $permissionEmails[] = $permission['emailAddress'];
            //Remove Permission
            if($permission['role']!='owner' && $permission['emailAddress'] != (env('GOOGLE_SCREENCAST_FOLDER_OWNER_ID')))
            {
                $driveService->permissions->delete($fileId, $permission['id']);
            }
        }
        //assign permission based on requested data
        $index = 1;
        $driveService->getClient()->setUseBatch(true);
        if(!empty($readData))
        {
            $batch = $driveService->createBatch();
            foreach ($readData as $email) {
                $userPermission = new Drive\Permission([
                    'type' => 'user',
                    'role' => 'reader',
                    'emailAddress' => $email,
                ]);

                $request = $driveService->permissions->create($fileId, $userPermission, ['fields' => 'id']);
                $batch->add($request, 'user'.$index);
                $index++;
            }
            $results = $batch->execute();
        }
        if(!empty($writeData))
        {
            $batch = $driveService->createBatch();
            foreach ($writeData as $email) {
                $userPermission = new Drive\Permission([
                    'type' => 'user',
                    'role' => 'writer',
                    'emailAddress' => $email,
                ]);

                $request = $driveService->permissions->create($fileId, $userPermission, ['fields' => 'id']);
                $batch->add($request, 'user'.$index);
                $index++;
            }
            $results = $batch->execute();
        }
        $fileData->read = !empty($readData)?implode(',',$readData):NULL;
        $fileData->write = !empty($writeData)?implode(',',$writeData):NULL;
        $fileData->save();
        return back()->with('success', "Permission successfully updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        $driveService = new Drive($client);
        try {
            $driveService->files->delete($id);
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
        GoogleScreencast::where('google_drive_file_id', $id)->delete();
        return redirect()->back()->with('success', 'Your File has been deleted successfuly!');
    }
}
