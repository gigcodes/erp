<?php

namespace App\Http\Controllers;

use App\CompetitorPage;
use App\CompetitorFollowers;
use Illuminate\Http\Request;

class CompetitorPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = CompetitorPage::all();

        return view('instagram.comp.index', compact('pages'));
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
            'name'     => 'required',
            'username' => 'required',
            'platform' => 'required',
        ]);

        $com           = new CompetitorPage();
        $com->name     = $request->get('name');
        $com->username = $request->get('username');
        $com->platform = $request->get('platform');
        $com->save();

        return redirect()->back()->with('message', 'Competitor page added!');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(CompetitorPage $competitorPage)
    {
        $username = $competitorPage->username;

        $item = $this->getInstagramUserData($username);

        return view('instagram.comp.grid', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\CompetitorPage $competitorPage
     * @param mixed               $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $followers = CompetitorFollowers::where('competitor_id', $id)->paginate(10);

        $processedFollowers = [];

        foreach ($followers as $follower) {
            $processedFollowers[] = $this->getInstagramUserDataWithoutFollowers($follower->username, $follower->id);
        }

        return view('instagram.comp.followers', compact('processedFollowers', 'followers'));
    }

    public function hideLead($id)
    {
        $c         = CompetitorFollowers::findOrFail($id);
        $c->status = 0;
        $c->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function approveLead($id)
    {
        $c         = CompetitorFollowers::findOrFail($id);
        $c->status = 2;
        $c->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompetitorPage $competitorPage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompetitorPage $competitorPage)
    {
        //
    }
}
