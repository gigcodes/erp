<?php

namespace App\Http\Controllers;

use App\UserLog;
use Illuminate\Http\Request;
use DataTables;
use Input;

class UserLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.url-log');
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
     * @param  \App\UserLog  $userLog
     * @return \Illuminate\Http\Response
     */
    public function show(UserLog $userLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserLog  $userLog
     * @return \Illuminate\Http\Response
     */
    public function edit(UserLog $userLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserLog  $userLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserLog $userLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserLog  $userLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserLog $userLog)
    {
        //
    }

    public function getData(Request $request){
       
        if(!empty($request->from_date)){
            $userslogs = UserLog::select(['id', 'user_id', 'url', 'created_at','user_name', 'updated_at'])->whereBetween('created_at', array($request->from_date, $request->to_date))->get();
        return Datatables::of($userslogs)
        ->addColumn('user_name', function ($userslogs) {
            return '<h6>'. $userslogs->user_name .'</h6>';
        })
        ->rawColumns(['user_name'])
        ->make(true);


        }else{
            $userslogs = UserLog::select(['id', 'user_id', 'url', 'created_at','user_name', 'updated_at']);
        return Datatables::of($userslogs)
        ->addColumn('user_name', function ($userslogs) {
            return '<h6>.'. $userslogs->user_name .'</h6>';
        })
        ->rawColumns(['user_name'])
        ->make(true);

         }
     }






}
