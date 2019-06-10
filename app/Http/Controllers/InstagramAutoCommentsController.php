<?php

namespace App\Http\Controllers;

use App\InstagramAutoComments;
use App\TargetLocation;
use Illuminate\Http\Request;

class InstagramAutoCommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = InstagramAutoComments::orderBy('updated_at', 'DESC')->get();
        $countries = TargetLocation::all();


        return view('instagram.auto_comments.index', compact('comments', 'countries'));
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
            'text' => 'required'
        ]);

        $comment = new InstagramAutoComments();
        $comment->comment = $request->get('text');
        $comment->source = $request->get('comment');
        $comment->country = $request->get('country');
        $comment->gender = $request->get('gender');
        $comment->save();

        return redirect()->back()->with('message', 'Comment added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InstagramAutoComments  $instagramAutoComments
     * @return \Illuminate\Http\Response
     */
    public function show($action, Request $request)
    {
        $this->validate($request, [
            'comments' => 'required'
        ]);

        InstagramAutoComments::whereIn('id', $request->get('comments'))->delete();

        return redirect()->back()->with('message', 'Deleted successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InstagramAutoComments  $instagramAutoComments
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $comment = InstagramAutoComments::findOrFail($id);

        return view('instagram.auto_comments.edit', compact('comment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InstagramAutoComments  $instagramAutoComments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $comment = InstagramAutoComments::findOrFail($id);
        $comment->comment = $request->get('text');
        $comment->source = $request->get('source');
        $comment->save();

        return redirect()->action('InstagramAutoCommentsController@index')->with('message', 'Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InstagramAutoComments  $instagramAutoComments
     * @return \Illuminate\Http\Response
     */
    public function destroy(InstagramAutoComments $instagramAutoComments)
    {
        //
    }
}
