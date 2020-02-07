<?php

namespace App\Console\Commands;

use App\Issue;
use App\LaravelGithubLog;
use Illuminate\Console\Command;
use Storage;

class AnalyzeLaravelLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:analyze';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze all the log files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $path =  base_path() . '/';

        $escaped = str_replace('/', '\/', $path);


        $errorData = array();

        $files = Storage::disk('logs')->files();
        foreach ($files as $file) {

            echo '====== Getting logs from file:' . $file . ' ======' . PHP_EOL;

            $content = Storage::disk('logs')->get($file);

            $matches = [];
            //preg_match_all('/\[([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})\].*' . $escaped . '(\S*):\d*\)(.|\\s)*[stacktrace](.|\\s)*main/U', $content, $matches);

            preg_match_all('/\[([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})\].*?'.$escaped. '(\S*?):\d*?\)\n.*?(#0.*?)main/s', $content, $matches);

            $timestamps = $matches[1];
            $filenames = $matches[2];
            $errorStackTrace = $matches[3];

            foreach ($timestamps as $index => $timestamp) {

                $data =  array(
                    'log_file_name' => $file,
                    'timestamp' => $timestamp,
                    'filename' => $filenames[$index],
                    'stacktrace' => $errorStackTrace[$index]
                );

                echo 'Got error: ';
                echo print_r($data, true) . PHP_EOL;

                $errorData[] = $data;
            }
        }

        foreach ($errorData as $key => $error) {
            $cmdReponse = [];
            $cmd = 'git log -n 1 ' . $path . $error['filename'] . ' 2>&1';
            echo 'git command: '.$cmd;
            exec($cmd, $cmdReponse);
            echo 'Command execution response :' . print_r($cmdReponse, true) . PHP_EOL;
            $commitDetails = $this->getDetailsFromCommit($cmdReponse);
            if ($commitDetails) {
                $errorData[$key]['commit'] = $commitDetails;
            }
        }

        $errorData = array_filter(
            $errorData,
            function ($data) {
                //echo print_r($data, true);
                return isset($data['commit']);
            }
        );

        echo '== DATA ENTRIES == '.PHP_EOL;
        echo print_r($errorData, true);

        $newlyCreatedLogs  = [];

        foreach ($errorData as $error) {

            $log = LaravelGithubLog::firstOrCreate(
                [
                    'log_time' => $error['timestamp'],
                    'log_file_name' => $error['log_file_name'],
                    'file' => $error['filename']
                ],
                [
                    'commit' => $error['commit']['commit'],
                    'author' =>  $error['commit']['author'],
                    'commit_time' => $error['commit']['date'],
                    'stacktrace' => $error['stacktrace']
                ]
            );

            if($log->wasRecentlyCreated){
                $newlyCreatedLogs[] = $log;
            }
        }

        // create issue for the newly create log
        foreach($newlyCreatedLogs as $log){

            $issue = $log->file.PHP_EOL.PHP_EOL.$log->stacktrce;
            $subject = 'Exception in '.$log->file;



            Issue::create([
                'user_id' => , 
                'issue' => $issue, 
                'priority' => 0, 
                'module' => '', 
                'subject' => $subject
            ]);

        }

        echo 'done';
    }

    private function getDetailsFromCommit($commit)
    {
        foreach ($commit as $line) {
            if ($this->startsWith($line, 'Author: ')) {
                $author = substr($line, strlen('Author: '));
                $author = trim($author);
            } else if ($this->startsWith($line, 'Date: ')) {
                $date = substr($line, strlen('Date: '));
                $date = trim($date);
            } else if($this->startsWith($line, 'commit')) {
                $commit = substr($line, strlen('commit'));
                $commit = trim($commit);
            }
        }
        if (isset($author) && isset($date)) {
            echo print_r(
                array(
                    'author' => $author,
                    'date' => $date,
                    'commit' => $commit
                ),
                true
            );
            return array(
                'author' => $author,
                'date' => $date,
                'commit' => $commit
            );
        }
        return false;
    }

    private function getUserDetailsFromCommit($commit){
        
    }

    function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}
