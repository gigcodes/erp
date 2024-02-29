<?php

namespace App\Http\Controllers;

use App\Task;
use App\User;
use Exception;
use App\DeveloperTask;
use Illuminate\Http\Request;
use App\Models\ScriptDocuments;
use Illuminate\Support\Facades\DB;
use App\Models\ScriptDocumentFiles;
use App\Models\ScriptsExecutionHistory;
use App\Jobs\UploadGoogleDriveScreencast;
use Illuminate\Support\Facades\Validator;

class ScriptDocumentsController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Script Documents';

        $records       = ScriptDocuments::select('*', DB::raw('MAX(id) AS id'))->orderBy('id', 'DESC');
        $records       = $records->groupBy('file')->get();
        $records_count = $records->count();

        $allUsers = User::where('is_active', '1')->select('id', 'name')->orderBy('name')->get();

        return view(
            'script-documents.index', [
                'title'         => $title,
                'records_count' => $records_count,
                'allUsers'      => $allUsers,
            ]
        );
    }

    public function records(Request $request)
    {
        $records = ScriptDocuments::select('*', DB::raw('MAX(id) AS id'))->orderBy('id', 'DESC');

        if ($keyword = request('keyword')) {
            $records = $records->where(
                function ($q) use ($keyword) {
                    $q->where('file', 'LIKE', "%$keyword%");
                    $q->orWhere('description', 'LIKE', "%$keyword%");
                    $q->orWhere('category', 'LIKE', "%$keyword%");
                    $q->orWhere('usage_parameter', 'LIKE', "%$keyword%");
                    $q->orWhere('comments', 'LIKE', "%$keyword%");
                    $q->orWhere('author', 'LIKE', "%$keyword%");
                    $q->orWhere('location', 'LIKE', "%$keyword%");
                    $q->orWhere('last_run', 'LIKE', "%$keyword%");
                    $q->orWhere('status', 'LIKE', "%$keyword%");
                }
            );
        }

        $records       = $records->take(25)->groupBy('file')->get();
        $records_count = $records->count();

        $records = $records->map(
            function ($script_document) {
                $script_document->created_at_date = \Carbon\Carbon::parse($script_document->created_at)->format('d-m-Y');

                $script_document->last_output_text = '';
                if (! empty($script_document->last_output)) {
                    $script_document->last_output_text = base64_decode($script_document->last_output);
                }

                return $script_document;
            }
        );

        return response()->json(
            [
                'code'  => 200,
                'data'  => $records,
                'total' => $records_count,
            ]
        );
    }

    public function store(Request $request)
    {
        $script_document = $request->all();
        $validator       = Validator::make(
            $script_document, [
                'file'            => 'required|string',
                'usage_parameter' => 'required|string',
                'category'        => 'required|string',
                'comments'        => 'required|string',
                'author'          => 'required|string',
                'description'     => 'required',
                'location'        => 'required',
                'last_run'        => 'required',
                'status'          => 'required',
            ]
        );

        if ($validator->fails()) {
            $outputString = '';
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . '<br>';
                }
            }

            return redirect()->back()->with('error', $outputString);
        }

        $id = $request->get('id', 0);

        $records = ScriptDocuments::find($id);

        if (! $records) {
            $records = new ScriptDocuments();
        }

        $script_document['user_id'] = \Auth::user()->id;
        $records->fill($script_document);
        $records->save();

        return redirect()->back()->with('success', 'You have successfully inserted a Script Document!');
    }

    public function edit($id)
    {
        $scriptDocument = ScriptDocuments::findorFail($id);

        if ($scriptDocument) {
            return response()->json(
                [
                    'code' => 200,
                    'data' => $scriptDocument,
                ]
            );
        }

        return response()->json(
            [
                'code'  => 500,
                'error' => 'Wrong script document id!',
            ]
        );
    }

    public function update(Request $request)
    {
        $this->validate(
            $request, [
                'file'            => 'required|string',
                'usage_parameter' => 'required|string',
                'category'        => 'required|string',
                'comments'        => 'required|string',
                'author'          => 'required|string',
                'description'     => 'required',
                'location'        => 'required',
                'last_run'        => 'required',
                'status'          => 'required',
            ]
        );

        $data            = $request->except('_token', 'id');
        $script_document = ScriptDocuments::where('id', $request->id)->first();
        $script_document->update($data);

        return redirect()->route('script-documents.index')->with('success', 'You have successfully updated a Script Document!');
    }

    public function destroy(ScriptDocuments $ScriptDocuments, Request $request)
    {
        try {
            $script_document = ScriptDocuments::where('id', '=', $request->id)->delete();

            return response()->json(
                [
                    'code'    => 200,
                    'data'    => $script_document,
                    'message' => 'Deleted successfully!!!',
                ]
            );
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(
                [
                    'code'    => 500,
                    'message' => $msg,
                ]
            );
        }
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'images'             => 'required',
            'file_creation_date' => 'required',
            'remarks'            => 'sometimes',
            'script_document_id' => 'required',
        ]);

        $data = $request->all();
        try {
            foreach ($data['images'] as $file) {
                DB::transaction(function () use ($file, $data) {
                    $scriptDocumentFiles            = new ScriptDocumentFiles();
                    $scriptDocumentFiles->file_name = $file->getClientOriginalName();
                    $scriptDocumentFiles->extension = $file->extension();

                    $scriptDocumentFiles->script_document_id = $data['script_document_id'];
                    $scriptDocumentFiles->remarks            = $data['remarks'];
                    $scriptDocumentFiles->file_creation_date = $data['file_creation_date'];
                    $scriptDocumentFiles->save();
                    UploadGoogleDriveScreencast::dispatchNow($scriptDocumentFiles, $file, 'anyone');
                });
            }

            return back()->with('success', 'File is Uploaded to Google Drive.');
        } catch (Exception $e) {
            return back()->with('error', 'Something went wrong. Please try again');
        }
    }

    public function getScriptDocumentFilesList(Request $request)
    {
        try {
            $result = [];
            if (isset($request->script_document_id)) {
                $result = ScriptDocumentFiles::where('script_document_id', $request->script_document_id)->orderBy('id', 'desc')->get();
                if (isset($result) && count($result) > 0) {
                    $result = $result->toArray();
                }

                return response()->json([
                    'data' => view('script-documents.google-drive-list', compact('result'))->render(),
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'data' => view('script-documents.google-drive-list', ['result' => null])->render(),
            ]);
        }
    }

    public function recordScriptDocumentAjax(Request $request)
    {
        $title = 'Script Documents';
        $page  = $_REQUEST['page'];
        $page  = $page * 25;

        $records = ScriptDocuments::select('*', DB::raw('MAX(id) AS id'))->orderBy('id', 'DESC')->offset($page)->limit(25)->groupBy('file');

        if ($keyword = request('keyword')) {
            $records = $records->where(
                function ($q) use ($keyword) {
                    $q->where('file', 'LIKE', "%$keyword%");
                    $q->orWhere('category', 'LIKE', "%$keyword%");
                    $q->orWhere('usage_parameter', 'LIKE', "%$keyword%");
                    $q->orWhere('comments', 'LIKE', "%$keyword%");
                    $q->orWhere('author', 'LIKE', "%$keyword%");
                }
            );
        }

        $records = $records->get();

        $records = $records->map(
            function ($script_document) {
                $script_document->created_at_date = \Carbon\Carbon::parse($script_document->created_at)->format('d-m-Y');

                return $script_document;
            }
        );

        return view(
            'script-documents.index-ajax', [
                'title' => $title,
                'data'  => $records,
                'total' => count($records),
            ]
        );
    }

    public function ScriptDocumentHistory($id)
    {
        $records = ScriptsExecutionHistory::with('scriptDocument')->where('script_document_id', $id)->orderBy('id', 'DESC')->get();

        $records = $records->map(
            function ($script_document) {
                $script_document->created_at_date = \Carbon\Carbon::parse($script_document->created_at)->format('d-m-Y');

                $script_document->last_output_text = '';
                if (! empty($script_document->run_output)) {
                    $script_document->last_output_text = base64_decode($script_document->run_output);
                }

                return $script_document;
            }
        );

        return response()->json([
            'status'      => true,
            'data'        => $records,
            'message'     => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function ScriptDocumentComment($id)
    {
        $scriptDocument = ScriptDocuments::findorFail($id);

        return response()->json([
            'status'      => true,
            'data'        => $scriptDocument,
            'last_output' => base64_decode(utf8_encode($scriptDocument['last_output'])),
            'message'     => 'Data get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function ScriptDocumentCommentHistory($id)
    {
        $scriptDocument = ScriptsExecutionHistory::findorFail($id);

        return response()->json([
            'status'      => true,
            'data'        => $scriptDocument,
            'last_output' => base64_decode(utf8_encode($scriptDocument['run_output'])),
            'message'     => 'Data get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function taskCount($site_developement_id)
    {
        $taskStatistics['Devtask'] = DeveloperTask::where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select();

        $query               = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
        $query               = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
        $taskStatistics      = $query->get();
        $othertask           = Task::where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select();
        $query1              = Task::join('users', 'users.id', 'tasks.assign_to')->where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
        $query1              = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
        $othertaskStatistics = $query1->get();
        $merged              = $othertaskStatistics->merge($taskStatistics);

        return response()->json(['code' => 200, 'taskStatistics' => $merged]);
    }

    public function getScriptDocumentErrorLogs(Request $request)
    {
        $records = ScriptsExecutionHistory::select('*', DB::raw('MAX(id) AS id'))->where('run_status', 'Failed')->orderBy('id', 'DESC');
        $records = $records->groupBy('script_document_id')->get();

        return response()->json(['code' => 200, 'message' => 'Content render', 'count' => $records->count()]);
    }

    public function getScriptDocumentErrorLogsList(Request $request)
    {
        $datas = ScriptsExecutionHistory::with('scriptDocument')->select('*', DB::raw('MAX(id) AS id'))->where('run_status', 'Failed')->orderBy('id', 'DESC');
        $datas = $datas->groupBy('script_document_id')->take(10)->get();

        return response()->json([
            'tbody' => view('partials.modals.script-document-error-logs-modal-html', compact('datas'))->render(),
            'count' => $datas->count(),
        ]);
    }
}
