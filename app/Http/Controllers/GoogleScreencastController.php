<?php

namespace App\Http\Controllers;

use App\GoogleScreencast;
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
        $data = GoogleScreencast::orderBy('created_at', 'desc');

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
        $data = $data->get();

        return view('googledrivescreencast.index', compact('data'))
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
            'doc_name' => ['required', 'max:800'],
            'file' => ['required'],
            'file.*' => ['required'],
        ]);
        DB::transaction(function () use ($request) {
            $googleScreencast = new GoogleScreencast();
            $googleScreencast->extension = $request->file->extension();
            $googleScreencast->file_name = $request->doc_name;
            $googleScreencast->save();
            UploadGoogleDriveScreencast::dispatchNow($googleScreencast,$request->file);
            
        });

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
