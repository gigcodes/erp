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
        $url = $request->url;
        $user_id = $request->user_id;
        $user_name = $request->user_name;

        $user_log = new UserLog();
            $user_log->user_id = $user_id;
            $user_log->url = $url;
            $user_log->user_name = $user_name;
            $user_log->save();
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


        }elseif (!empty($request->id)) {
             $userslogs = UserLog::select(['id', 'user_id', 'url', 'created_at','user_name', 'updated_at'])->where('id',$request->id)->get();
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
            return '<button class="btn btn-sm yellow edit" onclick="usertype('.$userslogs->user_id .')">'.$userslogs->user_name .'</button>';
        })
        ->rawColumns(['user_name'])
        ->make(true);

         }
     }






}
