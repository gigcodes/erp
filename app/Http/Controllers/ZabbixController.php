<?php

namespace App\Http\Controllers;

use App\Account;
use GuzzleHttp\Client;
use Auth;
use Carbon\Carbon;
use DB;
use App\Host;
use App\HostItem;
use App\Problem;

use Illuminate\Http\Request;


class ZabbixController extends Controller
{

   
    public function index(Request $request)
    {
       if($request->ajax()){
        $query = Host::with('items');
       
        return datatables()->eloquent($query)->toJson();
       }
       //dd($query);
        return view('zabbix.index');

    }

    public function problems(Request $request)
    {
       if($request->ajax()){
        $query = Problem::select('eventid','objectid', 'name');       
        return datatables()->eloquent($query)->toJson();
       }
       //dd($query);
        return view('zabbix.problem');
    }


}
