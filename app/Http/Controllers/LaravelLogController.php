<?php

namespace App\Http\Controllers;

use App\LaravelLog;
use App\Setting;
use App\User;
use File;
use Session;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;


class LaravelLogController extends Controller
{
    public $channel_filter = [];
    public function index(Request $request)
    {
        if ($request->filename || $request->log || $request->log_created || $request->created || $request->updated || $request->orderCreated || $request->orderUpdated) {

            $query = LaravelLog::query();

            if (request('filename') != null) {
                $query->where('filename', 'LIKE', "%{$request->filename}%");
            }

            if (request('log') != null) {
                $query->where('log', 'LIKE', "%{$request->log}%");
            }

            if (request('log_created') != null) {
                $query->whereDate('log_created', request('log_created'));
            }

            if (request('created') != null) {
                $query->whereDate('created_at', request('created'));
            }

            if (request('updated') != null) {
                $query->whereDate('updated_at', request('updated'));
            }

            if (request('orderCreated') != null) {
                if (request('orderCreated') == 0) {
                    $query->orderby('created_at', 'asc');
                } else {
                    $query->orderby('created_at', 'desc');
                }
            }

            if (request('orderUpdated') != null) {
                if (request('orderUpdated') == 0) {
                    $query->orderby('updated_at', 'asc');
                } else {
                    $query->orderby('updated_at', 'desc');
                }
            }

            if (request('orderCreated') == null && request('orderUpdated') == null) {
                $query->orderby('log_created', 'desc');
            }

            $paginate = (Setting::get('pagination') * 10);
            $logs     = $query->paginate($paginate)->appends(request()->except(['page']));
        } else {

            $paginate = (Setting::get('pagination') * 10);
            $logs     = LaravelLog::orderby('updated_at', 'desc')->paginate($paginate);

        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.laraveldata', compact('logs'))->render(),
                'links' => (string) $logs->render(),
                'count' => $logs->total(),
            ], 200);
        }

        return view('logging.laravellog', compact('logs'));
    }

    public function liveLogs(Request $request)
    {
        
        $filename = '/laravel-' . now()->format('Y-m-d') . '.log';
        //$filename = '/laravel-2020-09-10.log';
        $path     = storage_path('logs');
        $fullPath = $path . $filename;
        $errSelection = [];
        try {
            $content = File::get($fullPath);
            preg_match_all("/\[(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\](.*)/", $content, $match);
			$errorTypeArr = ['ERROR','INFO','WARNING'];
			$errorTypeSeparated = implode('|', $errorTypeArr);
			

			$defaultSearchTerm = 'ERROR';
			if($request->get('type'))
			{
				$defaultSearchTerm = $request->get('type');
			}

            foreach ($match[0] as $value) {
				foreach($errorTypeArr as $errType)
				{
					if(preg_match("/".$errType."/", $value))
					{
						$errSelection[] = $errType;

						break;
					}
				}
				if(preg_match("/".$defaultSearchTerm."/", $value))
				{
                    $str = $value;
                    $temp1 = explode(".",$str);
                    $temp2 = explode(" ",$temp1[0]);
                    $type = $temp2[2];
                    array_push($this->channel_filter,$type);
                    
					$errors[] = $value."===".str_replace('/', '', $filename);
				}
            }
            //if(isset($_GET['channel']) && $_GET['channel'] == "local"){
                $errors = array_reverse($errors);
            //}
        } catch (\Exception $e) {
            $errors = [];

        }
		
        $other_channel_data = $this->getDirContents($path);
        foreach($other_channel_data as $other){
            array_push($errors,$other);
        }
		$allErrorTypes = array_values(array_unique($errSelection));

		$users = User::all();
		$currentPage = LengthAwarePaginator::resolveCurrentPage();

        $perPage = Setting::get('pagination');

        
        

        $final = $key =  [];
        if(isset($_GET['channel']) ){
            session(['channel' => $_GET['channel']]);
        }
        foreach($errors as $key => $error){
            
            
            $str = $error;
            $temp1 = explode(".",$str);
            $temp2 = explode(" ",$temp1[0]);
            $type = $temp2[2];
            if(isset($_GET['channel']) && $_GET['channel'] == $type ){
                // echo "<pre>";
                // print_r($key);
                array_push($final,$error);
               
            }

            if(!isset($_GET['channel'])){

                // echo "<pre>";
                // print_r($key);
                array_push($final,$error);
            }
        }
    //     dd($final);
    //    exit;

        $errors = [];
        $errors = $final;
        $currentItems = array_slice($errors, $perPage * ($currentPage - 1), $perPage);
        //dd($currentItems);

        $logs = new LengthAwarePaginator($currentItems, count($errors), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);
        //dd($errors);
        
        //$this->channel_filter;
        $filter_channel = [];
        foreach($this->channel_filter as $ch){
            if(!in_array($ch,$filter_channel)){
                array_push($filter_channel,$ch);
            }
        }
        
        
        return view('logging.livelaravellog', ['logs' => $logs, 'filename' => str_replace('/', '', $filename), 'errSelection' => $allErrorTypes, 'users' => $users,'filter_channel' => $filter_channel]);

    }

    /**
     * to get relelated records for scraper
     *
     *
     */

    public function scraperLiveLogs()
    {
        $filename = '/scraper-' . now()->format('Y-m-d') . '.log';
        $path     = storage_path('logs').DIRECTORY_SEPARATOR."scraper";
        $fullPath = $path . $filename;
        $errors   = self::getErrors($fullPath);

        $currentPage    = LengthAwarePaginator::resolveCurrentPage();
        $perPage        = Setting::get('pagination');
        $currentItems   = array_slice($errors, $perPage * ($currentPage - 1), $perPage);

        $logs = new LengthAwarePaginator($currentItems, count($errors), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('logging.scraperlog', ['logs' => $logs, 'filename' => str_replace('/', '', $filename)]);

    }


	public function assign(Request $request)
	{
		if($request->get('issue') && $request->get('assign_to'))
		{
			$error = html_entity_decode($request->get('issue'), ENT_QUOTES, 'UTF-8');
			$issueName = substr($error, 0, 150);
			$requestData = new Request();
			$requestData->setMethod('POST');
			$requestData->request->add([
				'priority'    => 1,
				'issue'       => $error,
				'status'      => 'Planned',
				'module'      => 'Cron',
				'subject'     => $issueName."...",
				'assigned_to' => $request->get('assign_to'),
			]);

			app('App\Http\Controllers\DevelopmentController')->issueStore($requestData, 'issue');

			return redirect()->route('logging.live.logs');
		}

        return back()->with('error', '"issue" or "assign_to" not found in request.');
	}

    public static function getErrors($fullPath)
    {
        $errors = [];

        try {
            $content = File::get($fullPath);
            preg_match_all("/\[(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\](.*)/", $content, $match);
            foreach ($match[0] as $value) {
                $errors[] = str_replace("##!!##", "", $value);
            }
            $errors = array_reverse($errors);
        } catch (\Exception $e) {
            $errors = [];
        }

        return $errors;

    }

    public function liveLogDownloads() {
        $filename = '/laravel-' . now()->format('Y-m-d') . '.log';

        $path     = storage_path('logs');
        $fullPath = $path . $filename;
        return response()->download($fullPath,str_replace('/', '', $filename));
    }

    public function liveMagentoDownloads()
    {
        $filename = '/list-magento-' . now()->format('Y-m-d') . '.log';

        $path     = storage_path('logs');
        $fullPath = $path . $filename;
        return response()->download($fullPath,str_replace('/', '', $filename));
    }
    
    public function saveNewLogData(Request $request){
        
        $url = $request->url;
        $message = $request->message;
        $website = $request->website;
    	
        if($url==''){
            return response()->json(['status' => 'failed', 'message' => 'URL is required'], 400);
        }
        if($message==''){
            return response()->json(['status' => 'failed', 'message' => 'Message is required'], 400);
        }
        $laravelLog = new LaravelLog();
        $laravelLog->filename=$url;
        $laravelLog->log=$message;
        $laravelLog->website=$website;
        $laravelLog->save();
		 return response()->json(['status' => 'success', 'message' => 'Log data Saved'], 200);
    }
    
    public function getDirContents($dir, $results = array()) {
        $directories = glob($dir . '/*' , GLOB_ONLYDIR);
        $allErrorTypes = [];
        $final_result = [];
        foreach($directories as $dir){
            
            if ($handle = opendir($dir)) {

                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        $current_date = explode('-',date('Y-m-d'));
                        $temp = explode('-',$entry);
                        $errors = [];
                        $errSelection = [];
                        if($current_date[0] == $temp[1] && $current_date[1] == $temp[2] && $current_date[2] == str_replace('.log','',$temp[3])){
                            
                            $fullPath = $dir."/".$entry;
                            $content = File::get($fullPath);
                            preg_match_all("/\[(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\](.*)/", $content, $match);
                            $errorTypeArr = ['ERROR','INFO','WARNING'];
                            $errorTypeSeparated = implode('|', $errorTypeArr);
                            

                            $defaultSearchTerm = 'ERROR';
                            if(isset($_GET['type']))
                            {
                                $defaultSearchTerm = $_GET['type'];
                            }
                            
                            
                            foreach ($match[0] as $value) {
                                foreach($errorTypeArr as $errType)
                                {
                                    if(preg_match("/".$errType."/", $value))
                                    {
                                        $errSelection[] = $errType;
                                        break;
                                    }
                                }
                                if(preg_match("/".$defaultSearchTerm."/", $value))
                                {
                                    $str = $value;
                                    $temp1 = explode(".",$str);
                                    $temp2 = explode(" ",$temp1[0]);
                                    $type = $temp2[2];
                                    array_push($this->channel_filter,$type);
                                    $errors[] = $value."===".str_replace('/', '', $entry);
                                }
                            }
                            $errors = array_reverse($errors);
                            $allErrorTypes[] = array_values(array_unique($errSelection));
                            foreach($errors as $er){
                                array_push($final_result,$er);
                            }
                            //$final_result[] = $errors;   
                        }
                    }
                }
                closedir($handle);
            }
        }
        
        return $final_result;
    }

    
}
