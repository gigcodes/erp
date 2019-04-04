<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Setting;
use App\Document;
use App\User;
use Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 56) {
        $documents = Document::latest()->paginate(Setting::get('pagination'));
        $users = User::select(['id', 'name', 'email'])->get();

        return view('documents.index', [
          'documents' => $documents,
          'users' => $users
        ]);
      } else {
        return redirect()->back();
      }
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'user_id'   => 'required|numeric',
        'name'      => 'required|string|max:255',
        'file'      => 'required'
      ]);

      $data = $request->except(['_token', 'file']);

      foreach ($request->file('file') as $file) {
        $data['filename'] = $file->getClientOriginalName();

        $file->storeAs("documents", $data['filename'], 'uploads');

        Document::create($data);
      }

      return redirect()->route('document.index')->withSuccess('You have successfully uploaded document(s)!');
    }

    public function download($id)
    {
      $document = Document::find($id);

      return Storage::disk('uploads')->download('documents/' . $document->filename);
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
      $document = Document::find($id);

      Storage::disk('uploads')->delete("documents/$document->filename");

      $document->delete();

      return redirect()->route('document.index')->withSuccess('You have successfully deleted document');
    }
}
