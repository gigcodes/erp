<?php

namespace App\Http\Controllers;

use Auth;
use App\Task;
use App\User;
use Exception;
use App\GoogleDoc;
use Google\Client;
use App\DeveloperTask;
use Google\Service\Drive;
use Illuminate\Http\Request;
use App\Jobs\CreateGoogleDoc;
use Illuminate\Validation\Rule;
use App\Models\GoogleDocsCategory;
use Illuminate\Support\Facades\DB;
use App\Jobs\CreateGoogleSpreadsheet;

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
        /*if ($keyword = request('name')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->whereIn('google_docs.id', $keyword);
            });
        }*/
        if ($keyword = request('name')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('google_docs.name', 'LIKE', $keyword);
            });
        }
        if ($keyword = request('docid')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('docid', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('user_gmail')) {
            $data = $data->where(function ($q) use ($keyword) {
                foreach ($keyword as $key => $value) {
                    $q->whereRaw("find_in_set('" . $value . "',google_docs.read)")->orWhereRaw("find_in_set('" . $value . "',google_docs.write)");    
                }                
            });
        }
        /*if ($keyword = request('tasks')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->whereIn('google_docs.belongable_id', $keyword);
            });
        }*/

        if ($keyword = request('tasks')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('google_docs.belongable_id', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('task_type')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('google_docs.belongable_type', $keyword);
            });
        }
        if (isset($request->googleDocCategory)) {
            $data = $data->whereIn('category', $request->googleDocCategory ?? []);
        }
        if (! Auth::user()->isAdmin()) {
            $data->whereRaw("find_in_set('" . Auth::user()->gmail . "',google_docs.read)")->orWhereRaw("find_in_set('" . Auth::user()->gmail . "',google_docs.write)");
        }
        $data = $data->get();
        $users = User::select('id', 'name', 'email', 'gmail')->whereNotNull('gmail')->get();

        return view('googledocs.index', compact('data', 'users', 'request'))
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
            'type' => ['required', Rule::in('spreadsheet', 'doc', 'ppt', 'txt', 'xps')],
            'doc_name' => ['required', 'max:800'],
            // 'doc_category' => ['required', 'max:191'],
            'existing_doc_id' => ['sometimes', 'nullable', 'string', 'max:800'],
            'read' => ['sometimes'],
            'write' => ['sometimes'],
        ]);

        DB::transaction(function () use ($data) {
            $googleDoc = new GoogleDoc();
            $googleDoc->type = $data['type'];
            $googleDoc->name = $data['doc_name'];
            $googleDoc->created_by = Auth::user()->id;
            $googleDoc->category = $data['doc_category'] ?? null;
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $updateData = [];
        if (isset($request->doc_category)) {
            $updateData['category'] = $request->doc_category;
        }
        if (isset($request->type)) {
            $updateData['type'] = $request->type;
        }
        if (isset($request->name)) {
            $updateData['name'] = $request->name;
        }
        if (isset($request->docId)) {
            $updateData['docId'] = $request->docId;
        }
        if (count($updateData) > 0) {
            $modal = GoogleDoc::where('id', $request->id)->update($updateData);
            if ($modal) {
                return back()->with('success', 'Google Doc Category successfully updated.');
            } else {
                return back()->with('error', 'Something went wrong.');
            }
        } else {
            return back()->with('error', 'Something went wrong.');
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
            echo 'An error occurred: ' . $e->getMessage();
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
            if ($permission['role'] != 'owner' && ($permission['emailAddress'] != env('GOOGLE_SCREENCAST_FOLDER_OWNER_ID'))) {
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

    public function permissionRemove(Request $request)
    {
        $googledocs = GoogleDoc::where(function ($query) use ($request) {
            $query->orWhere('read', 'like', '%' . ($request->remove_permission) . '%');
            $query->orWhere('read', 'like', '%' . ($request->remove_permission) . '%');
        })->get();

        foreach ($googledocs as $googledoc) {
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
            $permissions = $driveService->permissions->listPermissions($googledoc->docId, $parameters);

            // dd($permissions->getPermissions());

            $is_already_have_permission = false;
            foreach ($permissions->getPermissions() as $permission) {
                $permissionEmails[] = $permission['emailAddress'];
                //Remove old Permission
                if ($permission['emailAddress'] == $request->remove_permission && $permission['role'] != 'owner' && ($permission['emailAddress'] != env('GOOGLE_SCREENCAST_FOLDER_OWNER_ID'))) {
                    $driveService->permissions->delete($googledoc->docId, $permission['id']);
                }
            }

            $read = explode(',', $googledoc->read);

            if (($key = array_search($request->remove_permission, $read)) !== false) {
                unset($read[$key]);
            }

            $new_read_data = implode(',', $read);
            $googledoc->read = $new_read_data;

            $write = explode(',', $googledoc->write);
            if (($key = array_search($request->remove_permission, $write)) !== false) {
                unset($write[$key]);
            }
            $new_write_data = implode(',', $write);
            $googledoc->write = $new_write_data;

            $googledoc->update();
        }

        return back()->with('success', 'Permission successfully Remove');
    }

    public function permissionView(Request $request)
    {
        $googledoc = GoogleDoc::where('id', $request->id)->first();

        $data = [
            'read' => $googledoc->read,
            'write' => $googledoc->write,
            'code' => 200,
        ];

        return $data;
    }

    /**
     * Search data of google docs.
     *
     * @param  string  $subject
     * @return \Illuminate\Http\Response
     */
    public function googledocSearch(Request $request)
    {
        $subject = $request->subject;
        $data = GoogleDoc::where('name', 'LIKE', '%' . $subject . '%')->orderBy('created_at', 'desc')->get();

        return view('googledocs.partials.list-files', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * create the document on devtask
     */
    public function createDocumentOnTask(Request $request)
    {
        $data = $this->validate($request, [
            'doc_type' => ['required', Rule::in('spreadsheet', 'doc', 'ppt', 'txt', 'xps')],
            'doc_name' => ['required', 'max:800'],
            // 'doc_category' => ['required', 'max:191'],
            'task_id' => ['required'],
            'task_type' => ['required'],
        ]);
        try {
            $authUser = Auth::user();

            DB::transaction(function () use ($data, $authUser, $request) {
                $task = null;
                $class = null;

                if ($data['task_type'] == 'DEVTASK') {
                    $task = DeveloperTask::find($data['task_id']);
                    $class = DeveloperTask::class;
                }
                if ($data['task_type'] == 'TASK') {
                    $task = Task::find($data['task_id']);
                    $class = Task::class;
                }

                $googleDoc = new GoogleDoc();
                $googleDoc->type = $data['doc_type'];
                $googleDoc->name = $data['doc_name'];
                $googleDoc->created_by = Auth::user()->id;
                $googleDoc->category = $data['doc_category'] ?? null;

                // Add the task name and description in document name
                if (isset($request->attach_task_detail)) {
                    if ($data['task_type'] == 'DEVTASK') {
                        $googleDoc->name .= " (DEVTASK-$task->id " . ($task->subject ?? '-') . ')';
                    }
                    if ($data['task_type'] == 'TASK') {
                        $googleDoc->name .= " (TASK-$task->id " . ($task->task_subject ?? '-') . ')';
                    }
                }

                if ($authUser->isAdmin()) {
                    $googleDoc->read = null;
                    $googleDoc->write = null;
                } else {
                    $googleDoc->read = $authUser->gmail;
                    $googleDoc->write = $authUser->gmail;
                }

                if (isset($task) && isset($task->id)) {
                    $googleDoc->belongable_type = $class;
                    $googleDoc->belongable_id = $task->id;
                }

                $googleDoc->save();

                if ($googleDoc->type === 'spreadsheet') {
                    CreateGoogleSpreadsheet::dispatch($googleDoc);
                }

                if ($googleDoc->type === 'doc' || $googleDoc->type === 'ppt' || $googleDoc->type === 'txt' || $googleDoc->type === 'xps') {
                    CreateGoogleDoc::dispatch($googleDoc);
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Document created successsfuly.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'success' => 'Something went wrong!',
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
                $class = '';
                if ($request->task_type == 'TASK') {
                    $class = Task::class;
                }
                if ($request->task_type == 'DEVTASK') {
                    $class = DeveloperTask::class;
                }

                $googleDoc = GoogleDoc::where('belongable_type', $class)->where('belongable_id', $request->task_id)->get();

                return response()->json([
                    'status' => false,
                    'data' => view('googledocs.task-document', compact('googleDoc'))->render(),
                ]);
            } else {
                throw new Exception('Task ID not found');
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => view('googledocs.task-document')->render(),
            ]);
        }
    }

    public function updateGoogleDocCategory(Request $request)
    {
        try {
            if (isset($request->category_id) && isset($request->doc_id)) {
                GoogleDoc::where('id', $request->doc_id)->update([
                    'category' => $request->category_id,
                ]);

                return response()->json(['status' => true, 'message' => 'Category updated.']);
            } else {
                return response()->json(['status' => false, 'message' => 'Invalid request']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error while updating status']);
        }
    }

    public function createGoogleDocCategory(Request $request)
    {
        try {
            GoogleDocsCategory::create([
                'name' => $request->name,
            ]);

            return redirect()->back()->with('success', 'Category added successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error while creating category');
        }
    }

    public function assignUserPermission(Request $request)
    {
        try {
            if (! isset($request->user_id)) {
                throw new Exception('User Id required');
            }
            if (! isset($request->document_id)) {
                throw new Exception('Please select Document');
            }

            $user = User::find($request->user_id);
            if (! $user) {
                throw new Exception('User not found.');
            }
            $doc = GoogleDoc::find($request->document_id);
            if (! $doc) {
                throw new Exception('Document not found.');
            }

            $readPermission = [];
            if ($doc->read) {
                $readPermission = explode(',', $doc->read);
            }
            $writePermission = [];
            if ($doc->read) {
                $writePermission = explode(',', $doc->write);
            }

            if (! isset($user->gmail) || $user->gmail == '') {
                throw new Exception('User Email not found.');
            }

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
            $permissions = $driveService->permissions->listPermissions($doc->docId, $parameters);

            // dd($permissions->getPermissions());

            $is_already_have_permission = false;
            foreach ($permissions->getPermissions() as $permission) {
                $permissionEmails[] = $permission['emailAddress'];
                //Remove old Permission
                if ($permission['emailAddress'] == $user->gmail && $permission['role'] != 'owner' && ($permission['emailAddress'] != env('GOOGLE_SCREENCAST_FOLDER_OWNER_ID'))) {
                    $driveService->permissions->delete($doc->docId, $permission['id']);
                    unset($readPermission[array_search($user->gmail, $readPermission)]);
                    unset($writePermission[array_search($user->gmail, $writePermission)]);
                }
            }

            //assign permission based on requested data
            $index = 1;
            $driveService->getClient()->setUseBatch(true);

            $batch = $driveService->createBatch();
            $userPermission = new Drive\Permission([
                'type' => 'user',
                'role' => 'reader',
                'emailAddress' => $user->gmail,
            ]);

            // dd([
            //     'type' => 'user',
            //     'role' => 'reader',
            //     'emailAddress' => $user->gmail,
            // ], $doc->docId);

            $r_request = $driveService->permissions->create($doc->docId, $userPermission, ['fields' => 'id']);
            $batch->add($r_request, 'user' . rand(0, 999));
            // $index++;
            // foreach ($readData as $email) {
            // }
            $results = $batch->execute();
            $readPermission[] = $user->gmail;

            $batch = $driveService->createBatch();
            $userPermission = new Drive\Permission([
                'type' => 'user',
                'role' => 'writer',
                'emailAddress' => $user->gmail,
            ]);

            $w_request = $driveService->permissions->create($doc->docId, $userPermission, ['fields' => 'id']);
            $batch->add($w_request, 'user' . rand(0, 999));
            // $index++;
            // foreach ($writeData as $email) {
            // }
            $results = $batch->execute();
            $writePermission[] = $user->gmail;

            if ($doc->belongable_id == null) {
                $doc->belongable_id = $request->task_id;
                $doc->belongable_type = ($request->task_type == 'DEVTASK') ? DeveloperTask::class : Task::class;
            }
            $doc->read = ! empty($readPermission) ? implode(',', $readPermission) : null;
            $doc->write = ! empty($writePermission) ? implode(',', $writePermission) : null;
            $doc->save();

            return redirect()->back()->withSuccess('Permission assigned successsfully');
        } catch (Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }

    public function getGoogleDocList(Request $request)
    {
        try {
            $doc = GoogleDoc::select('id', 'name as text')->get()->toArray();

            return response()->json([
                'status' => true,
                'docs' => $doc,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
            ]);
        }
    }

    public function googleDocRemovePermission(Request $request)
    {
        $fileIds = explode(',', request('remove_doc_ids'));
        $fileIds = array_map('intval', $fileIds);
        $readArray = request('read');
        $writeArray = request('write');

        foreach ($fileIds as $fileId) {
            $file = GoogleDoc::find($fileId);
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
            $permissions = $driveService->permissions->listPermissions($file->docId, $parameters);

            $is_already_have_permission = false;
            foreach ($permissions->getPermissions() as $permission) {
                $permissionEmails[] = $permission['emailAddress'];
                //Remove old Permission
                if (in_array($permission['emailAddress'], $readArray) && $permission['role'] != 'owner' && ($permission['emailAddress'] != env('GOOGLE_SCREENCAST_FOLDER_OWNER_ID'))) {
                    $driveService->permissions->delete($file->docId, $permission['id']);
                }
            }

            $readUsers = array_diff(explode(',', $file->read), $readArray);
            $writeUsers = array_diff(explode(',', $file->write), $writeArray);
            $file->read = implode(',', $readUsers);
            $file->write = implode(',', $writeUsers);
            $file->save();
        }

        return back()->with('success', 'Permission successfully removed');
    }

    public function addMulitpleDocPermission(Request $request)
    {
        $fileIds = explode(',', request('add_doc_ids'));
        $fileIds = array_map('intval', $fileIds);
        $readData = request('read');
        $writeData = request('write');
        $permissionEmails = [];

        foreach ($fileIds as $fileId) {
            $fileData = GoogleDoc::find($fileId);
            $client = new Client();
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            // Build a parameters array
            $parameters = [];
            // Specify what fields you want
            $parameters['fields'] = 'permissions(*)';
            // Call the endpoint to fetch the permissions of the file
            $permissions = $driveService->permissions->listPermissions($fileData->docId, $parameters);

            foreach ($permissions->getPermissions() as $permission) {
                $permissionEmails[] = $permission['emailAddress'];
                //Remove Permission
                if ($permission['role'] != 'owner' && ($permission['emailAddress'] != env('GOOGLE_SCREENCAST_FOLDER_OWNER_ID'))) {
                    $driveService->permissions->delete($fileData->docId, $permission['id']);
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

                    $request = $driveService->permissions->create($fileData->docId, $userPermission, ['fields' => 'id']);
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

                    $request = $driveService->permissions->create($fileData->docId, $userPermission, ['fields' => 'id']);
                    $batch->add($request, 'user' . $index);
                    $index++;
                }
                $results = $batch->execute();
            }
            $fileData->read = ! empty($readData) ? implode(',', $readData) : null;
            $fileData->write = ! empty($writeData) ? implode(',', $writeData) : null;
            $fileData->save();
        }

        return back()->with('success', 'Permission successfully updated.');
    }

    public function googleDocumentList(Request $request)
    {
        
        $dataDropdown = GoogleDoc::pluck('name', 'id')->toArray();

        // Get the user input
        $input = $_GET['term'];

        // Filter tags based on user input
        $filteredTags = array_filter($dataDropdown, function($tag) use ($input) {
            return stripos($tag, $input) !== false;
        });

        // Return the filtered tags as JSON
        echo json_encode($filteredTags);
    }

    public function googleTasksList(Request $request)
    {
            
        $tasksData = \App\Task::pluck('id')->toArray();
        $DeveloperTaskData = \App\DeveloperTask::pluck('id')->toArray();

        $tasks = array_unique(array_merge($tasksData,$DeveloperTaskData));

        sort($tasks);

        if(!empty($tasks)){
            $tasks = explode (", ", implode(", ", $tasks));
        }

        // Get the user input
        $input = $_GET['term'];

        // Filter tags based on user input
        $filteredTags = array_filter($tasks, function($tag) use ($input) {
            return stripos($tag, $input) !== false;
        });

        // Return the filtered tags as JSON
        echo json_encode($filteredTags);
    }
}
