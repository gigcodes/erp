<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use finfo;
use Storage;
use App\Task;
use App\User;
use App\Email;
use App\ApiKey;
use App\Vendor;
use App\Contact;
use App\Setting;
use App\Document;
use App\EmailAddress;
use App\DocumentRemark;
use App\DocumentHistory;
use App\DocumentCategory;
use App\DocumentSendHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mails\Manual\DocumentEmail;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->term || $request->date || $request->document_type || $request->category || $request->filename || $request->user) {
            $query = Document::query();

            if (request('term') != null) {
                $query->where('name', 'LIKE', "%{$request->term}%")
                    ->orWhere('filename', 'LIKE', "%{$request->term}%")
                    ->orWhereHas('documentCategory', function ($qu) use ($request) {
                        $qu->where('name', 'LIKE', "%{$request->term}%");
                    });
            }

            if (request('date') != null) {
                $query->whereDate('created_at', request('website'));
            }

            //if name is not null
            if (request('document_type') != null) {
                $query->where('name', 'LIKE', '%' . request('document_type') . '%');
            }

            //If username is not null
            if (request('filename') != null) {
                $query->where('filename', 'LIKE', '%' . request('filename') . '%');
            }

            if (request('category') != null) {
                $query->whereHas('documentCategory', function ($qu) {
                    $qu->where('name', 'LIKE', '%' . request('category') . '%');
                });
            }

            if (request('user') != null) {
                $query->whereHas('user', function ($qu) {
                    $qu->where('name', 'LIKE', '%' . request('user') . '%');
                });
            }

            $documents = $query->where('status', 1)->orderby('name', 'asc')->paginate(Setting::get('pagination'));
        } else {
            $documents = Document::where('status', 1)->latest()->paginate(Setting::get('pagination'));
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('documents.data', compact('documents'))->render(),
                'links' => (string) $documents->render(),
            ], 200);
        }

        $users = User::select(['id', 'name', 'email', 'agent_role'])->get();
        $emailAddresses = EmailAddress::orderBy('id', 'asc')->pluck('from_address', 'id');
        $category = DocumentCategory::select('id', 'name')->get();
        $api_keys = ApiKey::select('number')->get();

        return view('documents.index', [
            'documents' => $documents,
            'users' => $users,
            'category' => $category,
            'api_keys' => $api_keys,
            'emailAddresses' => $emailAddresses,
        ]);
    }

    public function documentList(Request $request)
    {
        $developertask = DB::table('developer_task_documents')
         ->select('subject', 'description', 'developer_task_id', 'developer_task_documents.created_at',
             'mediables.tag as tag', 'media.disk as disk', 'media.directory as directory', 'media.filename as filename',
             'media.extension as extension', 'users.name as username', DB::raw("'Devtask' as type"), 'media.id as media_id')
         ->join('mediables', 'mediables.mediable_id', '=', 'developer_task_documents.id')
         ->join('users', 'users.id', '=', 'developer_task_documents.created_by')
         ->join('media', 'media.id', '=', 'mediables.media_id')
         ->where('mediables.mediable_type', 'App\DeveloperTaskDocument')
         ->where('mediables.tag', config('constants.media_tags'));

        if ($request->task_subject && $request->task_subject != null) {
            $developertask = $developertask->where('subject', 'LIKE', "%$request->task_subject%");
            
        }
        if (! empty($request->user_id)) {
            $developertask = $developertask->where('developer_task_documents.created_by', $request->user_id);
           
        }
        if (! empty($request->term_id)) {
            $developertask = $developertask->where('developer_task_documents.developer_task_id', $request->term_id);
        }
        if (! empty($request->date)) {
            $developertask = $developertask->whereDate('developer_task_documents.created_at', $request->date);
        }
        // $developertask = $developertask->orderBy('developer_task_documents.id', 'desc');

        $uploadDocData = DB::table('tasks')
                ->select('task_subject as subject','task_details as description',
                    'tasks.id as developer_task_id', 'tasks.created_at', 'mediables.tag as tag',
                    'media.disk as disk', 'media.directory as directory', 'media.filename as filename',
                    'media.extension as extension', 'users.name as username', DB::raw("'Task' as type"), 'media.id as media_id')
                ->join('mediables', 'mediables.mediable_id', '=', 'tasks.id')
                ->join('users', 'users.id', '=', 'tasks.assign_from')
                ->join('media', 'media.id', '=', 'mediables.media_id')
                ->where('mediables.mediable_type', 'App\Task')
                ->where('mediables.tag', config('constants.media_tags'));

        if ($request->task_subject && $request->task_subject != null) {
            $uplodDocData = $uploadDocData->where('tasks.task_subject', 'LIKE', "%$request->task_subject%");
        }
        if (! empty($request->user_id)) {
            $uploadDocData = $uploadDocData->where('tasks.assign_from', $request->user_id);
        }
        if (! empty($request->term_id)) {
            $uploadDocData = $uploadDocData->where('tasks.id', $request->term_id);
        }
        if (! empty($request->date)) {
            $uploadDocData = $uploadDocData->whereDate('tasks.created_at', $request->date);
        }
        // $uploadDocData = $uploadDocData->orderBy('tasks.id', 'desc');
        $uploadDocData = $uploadDocData->union($developertask);
        $uploadDocData = $uploadDocData->orderBy('media_id', 'desc');
        $DataCount = $uploadDocData->count();
        $uploadDocData = $uploadDocData->paginate(50);
        
        $users = User::get();

        $totalCount = $DataCount;

        return view('development.documentList', compact('uploadDocData', 'users', 'totalCount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|numeric',
            'name' => 'required|string|max:255',
            'file' => 'required',
            'category_id' => 'required',
            'version' => 'required',
        ]);

        $data = $request->except(['_token', 'file']);
        // dd($data);
        foreach ($request->file('file') as $file) {
            $data['filename'] = $file->getClientOriginalName();
            $data['file_contents'] = $file->openFile()->fread($file->getSize());

            $file->storeAs('documents', $data['filename'], 'files');

            Document::create($data);
        }

        return redirect()->route('document.index')->withSuccess('You have successfully uploaded document(s)!');
    }

    public function download($id)
    {
        $document = Document::find($id);

        if (! empty($document->file_contents)) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $mime = $finfo->buffer($document->file_contents);

            return response($document->file_contents)
        ->header('Cache-Control', 'no-cache private')
        ->header('Content-Description', 'File Transfer')
        ->header('Content-Type', $mime)
        ->header('Content-length', strlen($document->file_contents))
        ->header('Content-Disposition', 'attachment; filename=' . $document->filename)
        ->header('Content-Transfer-Encoding', 'binary');

            /*return response()->make($document->file_contents, 200, array(
                'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($document->file_contents)
            ));*/
        }

        return Storage::disk('files')->download('documents/' . $document->filename);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $document = Document::findorfail($id);
        $document->user_id = $request->user_id;
        $document->name = $request->name;
        $document->category_id = $request->category_id;
        $document->status = 1;
        $document->update();

        return redirect()->route('document.index')->withSuccess('You have successfully updated document!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $document = Document::find($id);

        Storage::disk('files')->delete("documents/$document->filename");

        $document->delete();

        return redirect()->route('document.index')->withSuccess('You have successfully deleted document');
    }

    public function sendEmailBulk(Request $request)
    {
        //  dd($request->all());
        $this->validate($request, [
            'subject' => 'required|min:3|max:255',
            'message' => 'required',
            'cc.*' => 'nullable|email',
            'bcc.*' => 'nullable|email',
        ]);

        $file_paths = [];

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $filename = $file->getClientOriginalName();

                $file->storeAs('documents', $filename, 'files');

                $file_paths[] = "documents/$filename";
            }
        }

        $document = Document::findOrFail($request->document_id);

        if ($document) {
            $file_paths[] = "documents/$document->filename";
        }

        // dd($file_paths);
        $cc = $bcc = [];
        if ($request->has('cc')) {
            $cc = array_values(array_filter($request->cc));
        }
        if ($request->has('bcc')) {
            $bcc = array_values(array_filter($request->bcc));
        }
        $fromEmail = '';
        if (isset($request->from_select_id)) {
            $fromEmailArray = EmailAddress::where('id', $request->from_select_id)->first();
            if ($fromEmailArray) {
                $fromEmail = $fromEmailArray->from_address;
            }
        }

        if ($request->user_type == 1) {
            foreach ($request->users as $key) {
                $user = User::findOrFail($key);
                $user_email = $user->email;
                //  dd($request->all());
                /* echo $request["mitali1@gmail.com"];
                  echo $user_email;
                 dd($request->all());
                 dd($request[$user->email]);
                 dd($request[$user_email]);*/
                $reqKey = 'selected_email_' . $key;
                $email = (isset($request[$reqKey])) ? $request[$reqKey] : $user->email;

                /*$mail = Mail::to($user->email);

                if ($cc) {
                    $mail->cc($cc);
                }
                if ($bcc) {
                    $mail->bcc($bcc);
                }*/

                //History
                $history['send_by'] = Auth::id();
                $history['send_to'] = $user->id;
                $history['type'] = 'User';
                $history['via'] = 'Email';
                $history['document_id'] = $document->id;
                DocumentSendHistory::create($history);

                $emailClass = (new DocumentEmail($request->subject, $request->message, $file_paths))->build();

                $email = \App\Email::create([
                    'model_id' => $user->id,
                    'model_type' => \App\User::class,
                    'from' => ($fromEmail != '') ? $fromEmail : $emailClass->fromMailer,
                    'to' => $email,
                    'subject' => $emailClass->subject,
                    'message' => $emailClass->render(),
                    'template' => 'customer-simple',
                    'additional_data' => json_encode(['attachment' => $file_paths]),
                    'status' => 'pre-send',
                    'is_draft' => 1,
                    'cc' => $cc ?: null,
                    'bcc' => $bcc ?: null,
                ]);

                \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');

                /*$mail->send(new DocumentEmail($request->subject, $request->message, $file_paths));

                $params = [
                    'model_id' => $user->id,
                    'model_type' => User::class,
                    'from' => 'documents@amourint.com',
                    'seen' => 1,
                    'to' => $user->email,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'template' => 'customer-simple',
                    'additional_data' => json_encode(['attachment' => $file_paths]),
                    'cc' => $cc ? : null,
                    'bcc' => $bcc ? : null,
                ];

                Email::create($params);*/
            }
        } elseif ($request->user_type == 2) {
            foreach ($request->users as $key) {
                $vendor = Vendor::findOrFail($key);

                //History
                $history['send_by'] = Auth::id();
                $history['send_to'] = $vendor->id;
                $history['type'] = 'Vendor';
                $history['via'] = 'Email';
                $history['document_id'] = $document->id;
                DocumentSendHistory::create($history);

                $emailClass = (new DocumentEmail($request->subject, $request->message, $file_paths))->build();

                $reqKey = 'selected_email_' . $key;
                $email = (isset($request[$reqKey])) ? $request[$reqKey] : $vendor->email;

                $email = \App\Email::create([
                    'model_id' => $vendor->id,
                    'model_type' => \App\Vendor::class,
                    'from' => ($fromEmail != '') ? $fromEmail : $emailClass->fromMailer,
                    'to' => $email,
                    'subject' => $emailClass->subject,
                    'message' => $emailClass->render(),
                    'template' => 'customer-simple',
                    'additional_data' => json_encode(['attachment' => $file_paths]),
                    'status' => 'pre-send',
                    'is_draft' => 1,
                    'cc' => $cc ?: null,
                    'bcc' => $bcc ?: null,
                ]);

                \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
            }
        } elseif ($request->user_type == 3) {
            foreach ($request->users as $key) {
                $contact = Contact::findOrFail($key);

                //History
                $history['send_by'] = Auth::id();
                $history['send_to'] = $contact->id;
                $history['type'] = 'Contact';
                $history['via'] = 'Email';
                $history['document_id'] = $document->id;
                DocumentSendHistory::create($history);

                $emailClass = (new DocumentEmail($request->subject, $request->message, $file_paths))->build();

                $email = \App\Email::create([
                    'model_id' => $contact->id,
                    'model_type' => \App\Contact::class,
                    'from' => ($fromEmail != '') ? $fromEmail : $emailClass->fromMailer,
                    'to' => $contact->email,
                    'subject' => $emailClass->subject,
                    'message' => $emailClass->render(),
                    'template' => 'customer-simple',
                    'additional_data' => json_encode(['attachment' => $file_paths]),
                    'status' => 'pre-send',
                    'is_draft' => 1,
                    'cc' => $cc ?: null,
                    'bcc' => $bcc ?: null,
                ]);

                \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
            }
        } elseif (isset($request->emailcontact) && $request->emailcontact != null) {
            foreach ($request->emailcontact as $contacts) {
                $mail = Mail::to($contacts);

                if ($cc) {
                    $mail->cc($cc);
                }
                if ($bcc) {
                    $mail->bcc($bcc);
                }

                //History
                $history['send_by'] = Auth::id();
                $history['send_to'] = $contacts;
                $history['type'] = 'Manual Email';
                $history['via'] = 'Email';
                $history['document_id'] = $document->id;
                DocumentSendHistory::create($history);

                $mail->send(new DocumentEmail($request->subject, $request->message, $file_paths));

                $params = [
                    'model_id' => $contacts,
                    'model_type' => User::class,
                    'from' => ($fromEmail != '') ? $fromEmail : 'documents@amourint.com',
                    'seen' => 1,
                    'to' => $contacts,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'template' => 'customer-simple',
                    'additional_data' => json_encode(['attachment' => $file_paths]),
                    'cc' => $cc ?: null,
                    'bcc' => $bcc ?: null,
                ];

                Email::create($params);
            }
        }

        return redirect()->route('document.index')->withSuccess('You have successfully sent emails in bulk!');
    }

    public function getTaskRemark(Request $request)
    {
        $id = $request->input('id');

        $remark = DocumentRemark::where('document_id', $id)->get();

        return response()->json($remark, 200);
    }

    public function addRemark(Request $request)
    {
        $remark = $request->input('remark');
        $id = $request->input('id');
        $created_at = date('Y-m-d H:i:s');
        $update_at = date('Y-m-d H:i:s');
        if ($request->module_type == 'document') {
            $remark_entry = DocumentRemark::create([
                'document_id' => $id,
                'remark' => $remark,
                'module_type' => $request->module_type,
                'user_name' => $request->user_name ? $request->user_name : Auth::user()->name,
            ]);
        }

        return response()->json(['remark' => $remark], 200);
    }

    public function uploadDocument(Request $request)
    {
        $document = Document::findOrFail($request->document_id);

        //Create Document History
        $document_history = new DocumentHistory();
        $document_history->document_id = $document->id;
        $document_history->category_id = $document->category_id;
        $document_history->user_id = $document->user_id;
        $document_history->name = $document->name;
        $document_history->filename = $document->filename;
        $document_history->version = $document->version;
        $document_history->save();

        //Update the version and files name
        $document->version = ($document->version + 1);
        $file = $request->file('files');
        $document->filename = $file->getClientOriginalName();
        $document->file_contents = $file->openFile()->fread($file->getSize());
        $file->storeAs('documents', $document->filename, 'files');
        $document->save();

        return redirect()->route('document.index')->withSuccess('You have successfully uploaded document(s)!');
    }

    /**
     * @SWG\Post(
     *   path="/values-as-per-user",
     *   tags={"Documents"},
     *   summary="post Documents values as per user",
     *   operationId="get-document-per-user",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function getDataByUserType(Request $request)
    {
        if ($request->selected == 1) {
            $user = User::select('id', 'name', 'email')->get();

            $output = '';

            foreach ($user as $users) {
                $output .= '<option  rel="' . $users['email'] . '" value="' . $users['id'] . '" >' . $users['name'] . '</option>';
            }
            echo $output;
        } elseif ($request->selected == 2) {
            $vendors = Vendor::select('id', 'name', 'email')->get();

            $output = '';

            foreach ($vendors as $vendor) {
                $output .= '<option rel="' . $vendor['email'] . '"  value="' . $vendor['id'] . '">' . $vendor['name'] . '</option>';
            }
            echo $output;
        } elseif ($request->selected == 3) {
            $contact = Contact::select('id', 'name')->get();

            $output = '';

            foreach ($contact as $contacts) {
                $output .= '<option   rel= "" value="' . $contacts['id'] . '">' . $contacts['name'] . '</option>';
            }
            echo $output;
        } else {
            $output .= '<option value="0">Not Founf</option>';

            echo $output;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function email()
    {
        $documents = Document::where('status', 0)->latest()->paginate(Setting::get('pagination'));
        $users = User::select(['id', 'name', 'email', 'agent_role'])->get();
        $category = DocumentCategory::select('id', 'name')->get();
        $api_keys = ApiKey::select('number')->get();
        $emailAddresses = EmailAddress::orderBy('id', 'asc')->pluck('from_address', 'id');

        return view('documents.email', [
            'documents' => $documents,
            'users' => $users,
            'category' => $category,
            'api_keys' => $api_keys,
            'emailAddresses' => $emailAddresses,
        ]);
    }

    public function listShorcut(Request $request)
    {
        $datas = Document::latest()->get();
        
        return response()->json([
            'tbody' => view('partials.modals.list-documentation-shortcut-modal-html', compact('datas'))->render(),
            'count' => $datas->count(),
        ]);
    }
}
