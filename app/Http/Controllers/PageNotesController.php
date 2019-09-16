<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//use Spatie\Permission\Models\Permission;
//use Spatie\Permission\Models\Role;

class PageNotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        // will use after review
        // $this->middleware('permission:role-list');
        // $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    public function create(Request $request)
    {

        $pageNotes          = new \App\PageNotes;
        $pageNotes->user_id = \Auth::user()->id;
        $pageNotes->url     = $request->get("url", "");
        $pageNotes->note    = $request->get("note", "");

        if ($pageNotes->save()) {
        	$list = $pageNotes->getAttributes();
        	$list["name"] = $pageNotes->user->name;
            return response()->json(["code" => 1, "notes" => $list]);
        }

        return response()->json(["code" => -1, "message" => "oops, something went wrong!!"]);

    }

    public function list(Request $request)
    {
    	$pageNotes = \App\PageNotes::join('users', 'users.id', '=', 'page_notes.user_id')
    	->select(["page_notes.*","users.name"])
    	->where("url",$request->get("url"))
        ->orderBy("page_notes.id","desc")
    	->get()
    	->toArray();

    	return response()->json(["code" => 1, "notes" => $pageNotes]);
    }

    public function index(Request $request)
    {
    	return view("pagenotes.index");
    	//return datatables()->of(\App\PageNotes::query())->toJson();
    }

    public function records()
    {
    	return datatables()->of(\App\PageNotes::join('users', 'users.id', '=', 'page_notes.user_id')->orderBy("page_notes.id","desc")->select(["page_notes.*","users.name"])->get())->make();
    }

}
