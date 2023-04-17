<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Support\Carbon;
use App\User;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $users = User::get();
        $allTag= Tag::get()->toArray();
        $tagName= array_column($allTag, 'tag');
        $tagName = implode(",", $tagName);
        $tagName = "['".str_replace(",","','",$tagName)."']";
        return view('blogs.create', compact('users','tagName'));
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
            'user_id' => 'required',
            // 'idea' => 'required',
            // 'content' => 'required',
            // 'plaglarism' => 'required',
            // 'internal_link' => 'required',
            // 'external_link' => 'required',
            'publish_blog_date' => 'date_format:Y-m-d|after:' . Carbon::now()->format('Y-m-d')

        ]);

        


    
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
