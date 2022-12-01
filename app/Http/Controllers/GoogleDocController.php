<?php

namespace App\Http\Controllers;

use App\GoogleDoc;
use App\Jobs\CreateGoogleDoc;
use App\Jobs\CreateGoogleSpreadsheet;
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
        $data = $data->get();

        return view('googledocs.index', compact('data'))
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
            'type' => ['required', Rule::in('spreadsheet', 'doc')],
            'doc_name' => ['required', 'max:800'],
            'existing_doc_id' => ['sometimes', 'nullable', 'string', 'max:800'],
        ]);

        DB::transaction(function () use ($data) {
            $googleDoc = new GoogleDoc();
            $googleDoc->type = $data['type'];
            $googleDoc->name = $data['doc_name'];
            $googleDoc->save();

            if (! empty($data['existing_doc_id'])) {
                $googleDoc->docId = $data['existing_doc_id'];
                $googleDoc->save();
            } else {
                if ($googleDoc->type === 'spreadsheet') {
                    CreateGoogleSpreadsheet::dispatchNow($googleDoc);
                }

                if ($googleDoc->type === 'doc') {
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
        //
    }
}
