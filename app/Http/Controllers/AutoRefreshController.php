<?php

namespace App\Http\Controllers;

use App\AutoRefreshPage;
use Illuminate\Http\Request;
use Response;

class AutoRefreshController extends Controller
{
    public function index()
    {
        $pages = \App\AutoRefreshPage::paginate(10);

        return view('auto-refresh-page.index', compact('pages'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'page' => 'required',
            'time' => 'required',
        ]);

        $params = [
            "page"    => $request->get("page"),
            "time"    => $request->get("time"),
            "user_id" => \Auth::user()->id,
        ];

        AutoRefreshPage::create($params);

        return redirect()->back();
    }
    public function update(Request $request)
    {
        $this->validate($request, [
            'page' => 'required',
            'time' => 'required',
        ]);

        $page = AutoRefreshPage::find($request->input('id'));
        $page->fill($request->all());

        if ($page->save()) {
            return response()->json(['success' => true, 'message' => "Auto refresh page update successfully"]);
        }
        return response()->json(['success' => false, 'message' => "Something went wrong!"]);
    }
    public function delete(Request $request)
    {
        $page = AutoRefreshPage::find($request->input('id'));
        if ($page) {
            $page->delete();
            return response()->json(['success' => true, 'message' => "System size delete successfully"]);
        }
        return response()->json(['success' => false, 'message' => "Something went wrong!"]);
    }
}
