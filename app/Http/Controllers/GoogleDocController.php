<?php

namespace App\Http\Controllers;

use App\DeveloperTask;
use App\GoogleDoc;
use App\User;
use Auth;
use App\Jobs\CreateGoogleDoc;
use App\Jobs\CreateGoogleSpreadsheet;
use App\Task;
use Exception;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GoogleDocController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = GoogleDoc::orderBy('created_at', 'desc');

        if ($keyword = request('name')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('docid')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('docid', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('user_gmail')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->whereRaw("find_in_set('".$keyword."',google_docs.read)")->orWhereRaw("find_in_set('".$keyword."',google_docs.write)");
            });
        }
        if(!Auth::user()->isAdmin())
        {
            $data->whereRaw("find_in_set('".Auth::user()->gmail."',google_docs.read)")->orWhereRaw("find_in_set('".Auth::user()->gmail."',google_docs.write)");
        }
        $data = $data->get();
        $users = User::select('id','name','email','gmail')->whereNotNull('gmail')->get();

        return view('googledocs.index', compact('data','users'))
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
            'type'              => ['required', Rule::in('spreadsheet', 'doc', 'ppt', 'txt', 'xps')],
            'doc_name'          => ['required', 'max:800'],
            'doc_category'      => ['required', 'max:191'],
            'existing_doc_id'   => ['sometimes', 'nullable', 'string', 'max:800'],
            'read'              => ['sometimes'],
            'write'             => ['sometimes'],
        ]);

        DB::transaction(function () use ($data) {
            $googleDoc              = new GoogleDoc();
            $googleDoc->type        = $data['type'];
            $googleDoc->name        = $data['doc_name'];
            $googleDoc->category    = $data['doc_category'];
            if (isset($data['read'])) {
                $googleDoc->read = implode(',', $data['read']);
            }
            
            if (isset($data['write'])) {
                $googleDoc->write = implode(',', $data['write']);
            }
            $googleDoc->save();

            if (! empty($data['existing_doc_id'])) {
                $googleDoc->docId = $data['existing_doc_id'];
                $googleDoc->save();
            } else {
                if ($googleDoc->type === 'spreadsheet') {
                    CreateGoogleSpreadsheet::dispatchNow($googleDoc);
                }

                if ($googleDoc->type === 'doc' || $googleDoc->type === 'ppt' || $googleDoc->type === 'txt' || $googleDoc->type === 'xps') {
                    CreateGoogleDoc::dispatchNow($googleDoc);
                }
            }
        });

        return back()->with('success', "Google {$data['type']} is Created.");
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
        $modal = GoogleDoc::where('id', $id)->first();

        if ($modal) {
            return response()->json(['code' => 200, 'data' => $modal]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $modal = GoogleDoc::where('id', $request->id)->update(['category' => $request->doc_category]);
        if($modal){
            return back()->with('success', "Google Doc Category successfully updated.");
        }else{
            return back()->with('error', "Something went wrong.");
        }
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
        GoogleDoc::where('docId', $id)->delete();
        return redirect()->back()->with('success', 'Your File has been deleted successfuly!');
    }
    public function permissionUpdate(Request $request)
    {
        $fileId = request('file_id');
        $fileData = GoogleDoc::find(request('id'));
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
            if($permission['role']!='owner')
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

    public function permissionRemove(Request $request)
    {
        $googledocs = GoogleDoc::get();

        foreach($googledocs as $googledoc)
        {
            $read = explode(',',$googledoc->read);

            if (($key = array_search($request->remove_permission, $read)) !== false) {
                unset($read[$key]);
            }
            $new_read_data = implode(',',$read);
            $googledoc->read = $new_read_data;

            $write = explode(',',$googledoc->write);
            if (($key = array_search($request->remove_permission, $write)) !== false) {
                unset($write[$key]);
            }
            $new_write_data = implode(',',$write);
            $googledoc->write = $new_write_data;

            $googledoc->update();
        }

        return back()->with('success', "Permission successfully Remove");
    }

    public function permissionView(Request $request){
        $googledoc = GoogleDoc::where('id', $request->id)->first();

        $data =[
            'read' => $googledoc->read,
            'write' => $googledoc->write,
            'code' => 200,
        ];

        return $data;
    }
    
    /**
     * Search data of google docs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $subject
     * @return \Illuminate\Http\Response
     */
    public function googledocSearch(Request $request)
    {
        $subject = $request->subject;
        $data = GoogleDoc::where('name', 'LIKE', '%'.$subject.'%')->orderBy('created_at', 'desc')->get();
        return view('googledocs.partials.list-files', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5);
    }
    

    /**
     * create the document on devtask
     */
    public function createDocumentOnTask(Request $request)
    {
        try {
            $data = $this->validate($request, [
                'doc_type'              => ['required', Rule::in('spreadsheet', 'doc', 'ppt', 'txt', 'xps')],
                'doc_name'          => ['required', 'max:800'],
                'doc_category'      => ['required', 'max:191'],
                'task_id'      => ['required'],
                'task_type'      => ['required'],
            ]);
    
            DB::transaction(function () use ($data) {
                $task = null;
                $class = null;
    
                if($data['task_type'] == "DEVTASK") {
                    $task = DeveloperTask::find($data["task_id"]);
                    $class = DeveloperTask::class;
                } 
                if($data['task_type'] == "TASK") {
                    $task = Task::find($data["task_id"]);
                    $class = Task::class;
                }
    
                $googleDoc              = new GoogleDoc();
                $googleDoc->type        = $data['doc_type'];
                $googleDoc->name        = $data['doc_name'];
                $googleDoc->category    = $data['doc_category'];
                $googleDoc->read = "";
                $googleDoc->write = "";
                
                if(isset($task) && isset($task->id)) {
                    $googleDoc->belongable_type = $class;
                    $googleDoc->belongable_id = $task->id;
                }
                
                $googleDoc->save();
                
                if ($googleDoc->type === 'spreadsheet') {
                    CreateGoogleSpreadsheet::dispatch($googleDoc, "anyone");
                }
    
                if ($googleDoc->type === 'doc' || $googleDoc->type === 'ppt' || $googleDoc->type === 'txt' || $googleDoc->type === 'xps') {
                    CreateGoogleDoc::dispatch($googleDoc, "anyone");
                }
            });
    
            return response()->json([
                "status"=> true,
                "message"=> "Document created successsfuly is Created."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status"=> false,
                "success"=> "Something went wrong!"
            ]);
        }
    }

    /**
     * This function will list the created google document 
     */
    public function listDocumentOnTask(Request $request)
    {
        try {
            if (isset($request->task_id)) {
                $googleDoc = GoogleDoc::where("belongable_type", DeveloperTask::class)->where("belongable_id", $request->task_id)->get();
                
                return response()->json([
                    "status" => false,
                    "data" => view("googledocs.task-document", compact('googleDoc'))->render()
                ]);
            } else {
                throw new Exception("Task ID not found");
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "data" => view("googledocs.task-document")->render()
            ]);
        }
    }
}
