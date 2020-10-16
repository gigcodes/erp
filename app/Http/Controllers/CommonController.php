<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\MailinglistTemplate;
class CommonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function sendCommonEmail(request $request){

    }
    public function getMailTemplate(request $request){
        if(isset($request->mailtemplateid)){
            $data = MailinglistTemplate::select('static_template')->where('id',$request->mailtemplateid)->first();
            $static_template = $data->static_template;
            if(!$static_template){ return response()->json(['error'=>'unable to get template','success'=>false]); }
            return response()->json(['template'=>$static_template,'success'=>true]);
        }
        return response()->json(['error'=>'unable to get template','success'=>false]);
    }
}
