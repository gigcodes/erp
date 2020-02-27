<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LaravelLog;
use App\Setting;
use File;
use Illuminate\Pagination\LengthAwarePaginator;

class LaravelLogController extends Controller
{

    public function index(Request $request)
    {
    	 if ($request->filename || $request->log || $request->log_created  || $request->created || $request->updated || $request->orderCreated || $request->orderUpdated) {

            $query = LaravelLog::query();

            

            if (request('filename') != null) {
                $query->where('filename', 'LIKE', "%{$request->filename}%");
            }

            if (request('log') != null) {
                $query->where('log', 'LIKE', "%{$request->log}%");
            }

             if (request('log_created') != null) {
                $query->whereDate('log_created',request('log_created'));
            }

            if (request('created') != null) {
                $query->whereDate('created_at', request('created'));
            }

            if (request('updated') != null) {
                $query->whereDate('updated_at', request('updated'));
            }

            if(request('orderCreated') != null){
                if(request('orderCreated') == 0){
                    $query->orderby('created_at','asc');
                }else{
                    $query->orderby('created_at','desc');
                }
            }

            if(request('orderUpdated') != null){
                if(request('orderUpdated') == 0){
                    $query->orderby('updated_at','asc');
                }else{
                    $query->orderby('updated_at','desc');
                }
            }

            if(request('orderCreated') == null && request('orderUpdated') == null){
                $query->orderby('log_created','desc');
            }

            $paginate = (Setting::get('pagination') * 10);
            $logs = $query->paginate($paginate)->appends(request()->except(['page']));
        }
        else {

             $paginate = (Setting::get('pagination') * 10);
            $logs = LaravelLog::orderby('log_created','desc')->paginate($paginate);

        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.laraveldata', compact('logs'))->render(),
                'links' => (string)$logs->render(),
                'count' => $logs->total(),
            ], 200);
        }

    	return view('logging.laravellog',compact('logs'));
    }

    public function liveLogs()
    {
        $filename = '/laravel-'.now()->format('Y-m-d').'.log';

        $path = storage_path('logs');
        $fullPath = $path.$filename;
        try {
            $content = File::get($fullPath);

            preg_match_all("/\[(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\](.*)/", $content, $match);

            foreach ($match[0] as $value) {
                $errors[] = $value;
            }
            $errors = array_reverse($errors);
        } catch (\Exception $e) {
            $errors = [];
        }
            

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        
        $perPage = Setting::get('pagination'); 
        
        $currentItems = array_slice($errors, $perPage * ($currentPage - 1), $perPage);

        $logs = new LengthAwarePaginator($currentItems, count($errors), $perPage, $currentPage, [
            'path'  => LengthAwarePaginator::resolveCurrentPath()
        ]);

        return view('logging.livelaravellog',['logs' => $logs , 'filename' => str_replace('/','',$filename)]);
        
        
    }
}
