<?php

namespace App\Http\Controllers;

use App\Models\ScriptDocuments;
use App\Models\ScriptDocumentFiles;
use App\User;
use Exception;
use App\TestCase;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Jobs\UploadGoogleDriveScreencast;
use Illuminate\Support\Facades\Validator;

class ScriptDocumentsController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Script Documents';

        return view(
            'script-documents.index', [
                'title' => $title,
            ]
        );
    }

    public function records(Request $request)
    {   
        $records = ScriptDocuments::select('*', DB::raw("MAX(id) AS id"))->orderBy('id', 'DESC');

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

        $records = $records->take(10)->groupBy('file')->get();
        $records_count = $records->count();

        $records = $records->map(
            function ($script_document) {
                $script_document->created_at_date = \Carbon\Carbon::parse($script_document->created_at)->format('d-m-Y');
                return $script_document;
            }
        );

        return response()->json(
            [
                'code' => 200,
                'data' => $records,
                'total' => $records_count,
            ]
        );
    }

    public function store(Request $request)
    {
        $script_document = $request->all();
        $validator = Validator::make(
            $script_document, [
                'file' => 'required|string',
                'usage_parameter' => 'required|string',
                'category' => 'required|string',
                'comments' => 'required|string',
                'author' => 'required|string',
                'description' => 'required',
                'location' => 'required',
                'last_run' => 'required',
                'status' => 'required',
                'last_output' => 'required',
            ]
        );

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
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
                'code' => 500,
                'error' => 'Wrong script document id!',
            ]
        );
    }

    public function update(Request $request)
    {
        $this->validate(
            $request, [
                'file' => 'required|string',
                'usage_parameter' => 'required|string',
                'category' => 'required|string',
                'comments' => 'required|string',
                'author' => 'required|string',
                'description' => 'required',
                'location' => 'required',
                'last_run' => 'required',
                'status' => 'required',
            ]
        );

        $data = $request->except('_token', 'id');
        $script_document = ScriptDocuments::where('id', $request->id)->first();
        $script_document->update($data);

        return redirect()->route('script-documents.index')->with('success', 'You have successfully updated a Script Document!');
    }

    public function destroy(ScriptDocuments $ScriptDocuments, Request $request)
    {
        try {
            $script_document = ScriptDocuments::where('id', '=', $request->id)->delete();
            //$script_document_files = ScriptDocumentFiles::where('script_document_id', '=', $request->id)->delete();
            return response()->json(
                [
                    'code' => 200,
                    'data' => $script_document,
                    'message' => 'Deleted successfully!!!',
                ]
            );
        } catch(\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(
                [
                    'code' => 500,
                    'message' => $msg,
                ]
            );
        }
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'images' => 'required',
            'file_creation_date' => 'required',
            'remarks' => 'sometimes',
            'script_document_id' => 'required',
        ]);

        $data = $request->all();
        try {
            foreach ($data['images'] as $file) {
                DB::transaction(function () use ($file, $data) {
                    $scriptDocumentFiles = new ScriptDocumentFiles();
                    $scriptDocumentFiles->file_name = $file->getClientOriginalName();
                    $scriptDocumentFiles->extension = $file->extension();

                    $scriptDocumentFiles->script_document_id = $data['script_document_id'];
                    $scriptDocumentFiles->remarks = $data['remarks'];
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
        $page = $_REQUEST['page'];
        $page = $page * 10;

        $records = ScriptDocuments::select('*', DB::raw("MAX(id) AS id"))->orderBy('id', 'DESC')->offset($page)->limit(10);

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

        // return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);

        return view(
            'script-documents.index-ajax', [
                'title' => $title,
                'data' => $records,
                'total' => count($records),
            ]
        );
    }

    public function ScriptDocumentHistory($id)
    {   
        $scriptDocument = ScriptDocuments::findorFail($id);

        $records = [];
        if(!empty($scriptDocument)){

            $records = ScriptDocuments::where('file',$scriptDocument->file)->where('id', '!=', $id)->orderBy('id', 'DESC')->take(10)->get();

            $records = $records->map(
                function ($script_document) {
                    $script_document->created_at_date = \Carbon\Carbon::parse($script_document->created_at)->format('d-m-Y');
                    return $script_document;
                }
            );
        }

        return response()->json([
            'status' => true,
            'data' => $records,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function ScriptDocumentComment($id)
    {   
        $scriptDocument = ScriptDocuments::findorFail($id);

        return response()->json([
            'status' => true,
            'data' => $scriptDocument,
            'message' => 'Comment get successfully',
            'status_name' => 'success',
        ], 200);
    }
}
