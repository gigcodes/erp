<?php

namespace App\Http\Controllers;

use App\WebsiteLog;
use Illuminate\Http\Request;
use File;
use \Carbon\Carbon;
use InstagramAPI\Instagram;

class WebsiteLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fullPath = File::allFiles(env('WEBSITES_LOGS_FOLDER'));
        $dataArr = [];
        foreach ($fullPath as $key => $val) {
            $fileName = $val->getFilename();
            $filePath = env('WEBSITES_LOGS_FOLDER').'/'.$val->getFilename();
            $dataArr[] = array("S_No" => $key+1, "File_name" => $fileName, "File_Path" => $filePath);
        }
        return view('website-logs.index',compact('dataArr')); 
    }

    /**
     * This function used to getting date from the string
     *
     * @param [string] $string
     * @param [string] $start
     * @param [string] $end
     * @return string
     */
    public function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
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
    public function string_between_two_string($str, $starting_word, $ending_word){
        $arr = explode($starting_word, $str);
        if (isset($arr[1])){
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
        //$fullPath = '/Users/satyamtripathi/Work/sololux-erp/public/db.log';
        $fullPath = File::allFiles(env('WEBSITES_LOGS_FOLDER'));
            foreach ($fullPath as $key => $val) {
                if(\Storage::exists(env('WEBSITES_LOGS_FOLDER').'/'.$val->getFilename()) && $val->getFilename() == 'database.log'){
                $content = File::get(env('WEBSITES_LOGS_FOLDER').'/'.$val->getFilename());
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
                            //dd($module);
                        }
                        
                        if(!is_null($sql) && !is_null($time) && !is_null($module)){
                            $totalLogs[] = ['sql_query' => $sql,'time'=>$time,'module' => $module ];
                            $find = WebsiteLog::where([['sql_query', '=', $sql],['time','=',$time],['module', '=', $module]])->first();
                            if(empty($find)){
                                $ins = new WebsiteLog;
                                $ins->sql_query = $sql;
                                $ins->time = $time;
                                $ins->module = $module;
                                $ins->save();
                            }
                        }
                    }
                }
            }
        }
    }

}
