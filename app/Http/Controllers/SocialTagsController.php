<?php

namespace App\Http\Controllers;

use App\SocialTags;
use Illuminate\Http\Request;

class SocialTagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = SocialTags::all();

        return view('socialtags.index', compact('tags'));
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
            'name' => 'required'
        ]);

        $tag = new SocialTags();
        $tag->name = $request->get('name');
        $tag->save();

        return redirect()->back()->with('message', 'Tag added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tag = SocialTags::findOrFail($id);

        return view('socialtags.scraped_images', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tag = SocialTags::findOrFail($id);

        return view('socialtags.edit', compact('tag'));

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
        $this->validate($request, [
            'name' => 'required'
        ]);

        $tag = SocialTags::findOrFail($id);
        $tag->name = $request->get('name');
        $tag->save();

        return redirect()->back()->with('message', 'Tag updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tag = SocialTags::findOrFail($id);
        $tag->delete();

        return redirect()->back()->with('message', 'Tag deleted successfully!');
    }
}
