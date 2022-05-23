<?php

namespace App\Http\Controllers;

use App\GTMatrixErrorLog;
use Illuminate\Http\Request;

class GTMatrixErrorLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "GTmetrix Error";
        return view('gtmetrix.gtmetrix-error', compact('title'));
    }

    public function listGTmetrixError(Request $request){
       
        $title = "GTmetrix Error";
        if ($request->ajax()) {
            $items = GTMatrixErrorLog::select('id', 'store_viewGTM_id','error_title', 'error', 'created_at');            
            if (isset($request->name) && !empty($request->name)) {
                $items->where('error_title', 'Like', '%' . $request->name . '%')->orWhere('error', 'Like', '%' . $request->name . '%')->orWhere('store_viewGTM_id', 'Like', '%' . $request->name . '%');
            }
            //$datas = datatables()->eloquent($items)->toJson();    
            //dd($datas);
            return datatables()->eloquent($items)->toJson();    
        }
        return redirect()->back()->with('error', 'Somthing wrong here to access GTMetrix error');

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
     * @param  \App\GTMatrixErrorLog  $gTMatrixErrorLog
     * @return \Illuminate\Http\Response
     */
    public function show(GTMatrixErrorLog $gTMatrixErrorLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\GTMatrixErrorLog  $gTMatrixErrorLog
     * @return \Illuminate\Http\Response
     */
    public function edit(GTMatrixErrorLog $gTMatrixErrorLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GTMatrixErrorLog  $gTMatrixErrorLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GTMatrixErrorLog $gTMatrixErrorLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GTMatrixErrorLog  $gTMatrixErrorLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(GTMatrixErrorLog $gTMatrixErrorLog)
    {
        //
    }
}
