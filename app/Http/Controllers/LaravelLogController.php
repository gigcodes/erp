<?php

namespace App\Http\Controllers;

use App\LaravelLog;
use App\Setting;
use App\User;
use File;
use Session;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


class LaravelLogController extends Controller
{

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
        try {
            $content = File::get($fullPath);
            preg_match_all("/\[(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\](.*)/", $content, $match);
			$errorTypeArr = ['ERROR','INFO','WARNING'];
			$errorTypeSeparated = implode('|', $errorTypeArr);
			$errSelection = [];

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
					$errors[] = $value;
				}
			}
		    $errors = array_reverse($errors);
        } catch (\Exception $e) {
            $errors = [];

        }
		/* echo "<pre>";
		print_r($errors);
		print_r();

		exit;
 */
		$allErrorTypes = array_values(array_unique($errSelection));

		$users = User::all();
		$currentPage = LengthAwarePaginator::resolveCurrentPage();

        $perPage = Setting::get('pagination');

        $currentItems = array_slice($errors, $perPage * ($currentPage - 1), $perPage);

        $logs = new LengthAwarePaginator($currentItems, count($errors), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('logging.livelaravellog', ['logs' => $logs, 'filename' => str_replace('/', '', $filename), 'errSelection' => $allErrorTypes, 'users' => $users]);

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
}
