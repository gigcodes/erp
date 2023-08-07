<?php

namespace App\Http\Controllers;

use File;
use App\WebsiteLog;
use App\StoreWebsite;
//use InstagramAPI\Instagram;
use Illuminate\Http\Request;
use App\CodeShortCutPlatform;
use App\CodeShortcut;

class WebsiteLogController extends Controller
{
    public function logsPath()
    {
        return env('WEBSITES_LOGS_FOLDER') ?: '../storage/';
    }

    public function prepareWebsite($filePath)
    {
        $path = $this->logsPath();
        $filePath = trim(str_replace($path, '', $filePath), '/');
        $filePath = explode('/', $filePath);
        if (count($filePath) > 1) {
            return $filePath[0];
        }

        return null;
    }

    public function prepareWebsites($data)
    {
        $return = [];
        foreach ($data as $key => $filePath) {
            $website = $this->prepareWebsite($filePath);
            if ($website) {
                $return[$website] = $website;
            }
        }
        $return = array_unique($return);
        ksort($return);

        return $return;
    }

    public function index()
    {
        $path = $this->logsPath();

        $srchWebsite = request('website', '');
        $srchFilename = request('file_name', '');
        $searchDate = request('date', '');

        $listSrchFiles = [];

        $logFiles = readFullFolders($path);
        foreach ($logFiles as $key => $filePath) {
            $fileName = basename($filePath);
            if ($website = $this->prepareWebsite($filePath)) {
                if ($srchWebsite && $website == $srchWebsite) {
                    $listSrchFiles[$fileName] = $fileName;
                } else {
                    $listSrchFiles[$fileName] = $fileName;
                }

                if ($srchWebsite && $srchFilename) {
                    if (! ($website == $srchWebsite && $fileName == $srchFilename)) {
                        continue;
                    }
                } elseif ($srchWebsite) {
                    if (! ($website == $srchWebsite)) {
                        continue;
                    }
                } elseif ($srchFilename) {
                    if (! ($fileName == $srchFilename)) {
                        continue;
                    }
                }

                $dataArr[] = [
                    'S_No' => $key + 1,
                    'File_name' => $fileName,
                    'Website' => '',
                    'Website' => $website,
                    'File_Path' => $filePath,
                    'date' => date ("F d Y H:i:s.", filemtime($filePath)),
                    'formatedDate' => date("Y-m-d", filemtime($filePath)),
                ];
            }
        }

        usort($dataArr, function ($a, $b) {
            $dateA = strtotime($a["date"]);
            $dateB = strtotime($b["date"]);
            return $dateB - $dateA;
        });

        if($searchDate){
            $dataArr = array_filter($dataArr, function ($item) use ($searchDate) {
                return $item['formatedDate'] === $searchDate;
              });
        }
        
        $directories = readFolders($logFiles);
        // _p($directories);

        $listWebsites = $this->prepareWebsites($directories);

        $listSrchFiles = array_unique($listSrchFiles);
        ksort($listSrchFiles);

        // _p($logFiles, 1);

        // $filesDirectories = scandir(env('WEBSITES_LOGS_FOLDER'));
        // //$filesDirectories = scandir('storage');
        // $dataArr = [];
        // //dd($filesDirectories);
        // foreach ($filesDirectories as $filesDirectory) {
        //     if ($filesDirectory != '.' && $filesDirectory != '..') {
        //         $fullPath = \File::allFiles(env('WEBSITES_LOGS_FOLDER') . $filesDirectory);
        //         //dd(storage_path().'/'.$filesDirectory);
        //         //$fullPath = \File::allFiles(storage_path().'/'.$filesDirectory);
        //         foreach ($fullPath as $key => $val) {
        //             $fileName = $val->getFilename();
        //             $filePath = env('WEBSITES_LOGS_FOLDER') . $filesDirectory . '/' . $val->getFilename();
        //             //$filePath = storage_path().'/'.$filesDirectory.'/'.$val->getFilename();
        //             $website = $filesDirectory;
        //             $dataArr[] = array("S_No" => $key + 1, "File_name" => $fileName, "Website" => $website, "File_Path" => $filePath);
        //         }
        //     }
        // }

        return view('website-logs.index', [
            'dataArr' => $dataArr,
            'listWebsites' => $listWebsites,
            'listSrchFiles' => $listSrchFiles,
        ]);
    }

    public function searchWebsiteLog(Request $request)
    {
        return $this->index();

        // $filesDirectories = scandir(env('WEBSITES_LOGS_FOLDER'));
        // $dataArr = [];

        // foreach ($filesDirectories as $filesDirectory) {
        //     if ($filesDirectory != '.' && $filesDirectory != '..') {
        //         //dd('storage/logs/'.$filesDirectory);
        //         $fullPath = \File::allFiles(env('WEBSITES_LOGS_FOLDER') . $filesDirectory);
        //         foreach ($fullPath as $key => $val) {
        //             $fileName = $val->getFilename();
        //             $filePath = env('WEBSITES_LOGS_FOLDER') . $filesDirectory . '/' . $val->getFilename();
        //             $website = $filesDirectory;
        //             if ($request->file_name == $val->getFilename())
        //                 $dataArr[] = array("S_No" => $key + 1, "File_name" => $fileName, "Website" => $website, "File_Path" => $filePath);
        //             if ($request->website == $website && $request->file_name == $val->getFilename())
        //                 $dataArr[] = array("S_No" => $key + 1, "File_name" => $fileName, "Website" => $website, "File_Path" => $filePath);
        //         }
        //     }
        // }
        // $dataArr = array_map("unserialize", array_unique(array_map("serialize", $dataArr)));;
        // $fileName = $request->file_name;
        // $website = $request->website;
        // return view('website-logs.index', compact('dataArr', 'fileName', 'website'));
    }

    public function runWebsiteLogCommand(Request $request)
    {
        // dd('sdfdsds');
        return \Artisan::call('command:websitelog');
    }

    public function websiteLogFileView(Request $request)
    {
        if (file_exists($request->path)) {
            $path = $request->path;

            return response()->file($path);
        } else {
            return 'File not found';
        }
    }

    /**
     * This function used to getting date from the string
     *
     * @param [string] $string
     * @param [string] $start
     * @param [string] $end
     * @return string
     */
    public function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    /**
     * This function is used to getting string for the spacific string between
     *
     * @param [string] $str
     * @param [string] $starting_word
     * @param [string] $ending_word
     * @return string
     */
    public function string_between_two_string($str, $starting_word, $ending_word)
    {
        $arr = explode($starting_word, $str);
        if (isset($arr[1])) {
            $arr = explode($ending_word, $arr[1]);

            return $arr[0];
        }

        return '';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
    }
    /* public function store()
    {
        //$fullPath = '/Users/satyamtripathi/Work/sololux-erp/public/db.log';
        $websiteFolderArr = array('customers', 'chatapi', 'whatsapp', 'logs');
        foreach($websiteFolderArr as $websiteName) {
            // find the Directory
            $mainPath = env('WEBSITES_LOGS_FOLDER');
            $mainPath = $mainPath.'/'.$websiteName;
            //dd(File::isDirectory($mainPath));
            if(File::isDirectory($mainPath)){
                $website = StoreWebsite::select('website')->where('website',  'like', '%' . $websiteName. '%');
                $fullPath = File::allFiles($mainPath);
                //dd($fullPath);
                foreach ($fullPath as $key => $val) {
                    //echo ($mainPath.'/'.$val->getFilename()).'</br>';
                    ///if(\Storage::exists($mainPath.'/'.$val->getFilename()))
                    if(file_exists($mainPath.'/'.$val->getFilename()) && $val->getFilename() == 'db.log')
                    {
                        if($val->getFilename() == 'db.log')
                            $fileTypeName = 'db';
                        else
                            $fileTypeName = $val->getFilename();
                        $content = File::get($mainPath.'/'.$val->getFilename());
                        //dd($content);
                        $logs = preg_split('/\n\n/', $content);
                        $totalLogs = [];
                        foreach ($logs as $log) {
                            $entries = explode(PHP_EOL, $log);
                            $sql = null;
                            $time = null;
                            $module = null;
                            foreach ($entries as $entry) {
                                if (strpos($entry, 'SQL') !== false) {
                                    //dd($entry);
                                    $sql = $entry;
                                }
                                //if (strpos($entry, 'TIME') !== false) {
                                if (strpos($entry, '[20') !== false) {
                                    $time = $this->string_between_two_string($entry, '[', ']');
                                }
                                if (strpos($entry, '#8') !== false) {
                                    $module = $entry;
                                    //dd($module);l
                                }

                                if(!is_null($sql) && !is_null($time) && !is_null($module)){
                                    $totalLogs[] = ['sql_query' => $sql,'time'=>$time,'module' => $module ];
                                    $find = WebsiteLog::where([['sql_query', '=', $sql],['time','=',$time],['module', '=', $module]])->first();
                                    if(empty($find)){
                                        $ins = new WebsiteLog;
                                        $ins->sql_query = $sql;
                                        $ins->time = $time;
                                        $ins->module = $module;
                                        $ins->website_id = $website->website ?? '';
                                        $ins->type = $fileTypeName;
                                        $ins->save();
                                    }
                                }
                            }
                        }
                    }
                }
            } // if directory exist
        }
    }
    */

    public function websiteLogStoreView()
    {
        try {
            $dataArr = WebsiteLog::latest()->paginate(25);

            return view('website-logs.website-log-view', compact('dataArr'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function searchWebsiteLogStoreView(Request $request)
    {
        try {

            $dataArr = new WebsiteLog();
            if ($request->search_error) {
                $dataArr = $dataArr->where('error', 'LIKE', '%' . $request->search_error . '%');
            }
            if ($request->search_type) {
                $dataArr = $dataArr->where('type', 'LIKE', '%' . $request->search_type . '%');
            }  
            if ($request->website_ids) {
                $dataArr = $dataArr->WhereIn('website_id', $request->website_ids);
            }
            if ($request->date) {
                $dataArr = $dataArr->where('created_at', 'LIKE', '%' . $request->date . '%');
            }
            $dataArr = $dataArr->latest()->paginate(\App\Setting::get('pagination',10));
            $search_error = $request->search_error;
            $search_type = $request->search_type;
            $website_id = $request->website_ids;
            $date = $request->date;

            return view('website-logs.website-log-view', compact('dataArr', 'search_error', 'search_type', 'website_id', 'date'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function websiteErrorShow(Request $request)
    {     
        $id = $request->input('id');
        $errorData = WebsiteLog::where('id', $id)->value('error');
        $htmlContent = '<tr><td>' . $errorData . '</td></tr>';

        return $htmlContent;
    }

    public function WebsiteLogTruncate()
    {
        WebsiteLog::truncate();

        return redirect()->route('website.log.view')->withSuccess('data Removed succesfully!');
    }

    public function websiteInsertCodeShortcut(Request $request)
    {
        $websiteLog = WebsiteLog::find($request->id);

        $checkAlredyExist = CodeShortcut::where('website_log_view_id',$request->id)->first();

        if($checkAlredyExist) {
            return response()->json(['code' => 200, 'message' => 'Alreday Insert Into CodeShortcut!!!']);
        } else {
            $platform = CodeShortCutPlatform::firstOrCreate(['name' => 'magnetoCron']);
            $platformId = $platform->id;
        
            $codeShortcut =  new CodeShortcut();
            $codeShortcut->code_shortcuts_platform_id = $platformId;
            $codeShortcut->description = $websiteLog->file_path;
            $codeShortcut->title = $websiteLog->error;
            $codeShortcut->website = $websiteLog->website_id;
            $codeShortcut->user_id = auth()->user()->id;
            $codeShortcut->website_log_view_id = $request->id;
            $codeShortcut->type = "website-log-view";
            $codeShortcut->save();

            return response()->json(['code' => 200, 'message' => 'CodeShortcut Insert successfully!!!']);
        }
    
    }
}
