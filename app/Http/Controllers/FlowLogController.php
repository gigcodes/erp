<?php

namespace App\Http\Controllers;

use App\Loggers\FlowLog;

use App\Loggers\FlowLogMessages;
use App\Setting;
use App\User;
use App\LogKeyword;
use File;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use Illuminate\Support\Carbon;

class FlowLogController extends Controller
{
    public $channel_filter = [];
    public function index(Request $request)
    {
        if ($request->flow_name || $request->messages || $request->created_at) {

            $query = FlowLog::orderby('updated_at', 'desc')->select(['flow_logs.*','flows.flow_name'])
            ->join('flows','flows.id','flow_logs.flow_id');

           
            if (request('messages') != null) {
                $query->where('messages', 'LIKE', "%{$request->messages}%");
            }

            if (request('created_at') != null) {
                $query->whereDate('created_at', request('created_at'));
            }
            if (request('flow_name') != null) {
                $query->where('flows.flow_name', 'LIKE', "%{$request->flow_name}%");
            }

            
            $paginate = (Setting::get('pagination') * 10);
            $logs     = $query->paginate($paginate)->appends(request()->except(['page']));
        } else {

            $paginate = (Setting::get('pagination') * 10);
            $logs     = FlowLog::orderby('updated_at', 'desc')->select(['flow_logs.*','flows.flow_name'])
            ->join('flows','flows.id','flow_logs.flow_id')->paginate($paginate);

        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.flowlogdata', compact('logs'))->render(),
                'links' => (string) $logs->render(),
                'count' => $logs->total(),
            ], 200);
        }

        return view('logging.flowlog', compact('logs'));
    }
    public function details(Request $request){

        $messageLogs = FlowLogMessages::where('flow_log_id', $request->id)
        ->leftJoin('store_websites as sw', 'sw.id', '=', 'flow_log_messages.store_website_id')
        ->select('flow_log_messages.*','sw.website as website')->get();
        return view('logging.partials.flow_detail_data', compact('messageLogs'));

    }

    
}