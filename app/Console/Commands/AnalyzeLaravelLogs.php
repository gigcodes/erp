<?php

namespace App\Console\Commands;

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
            $content = Storage::disk('logs')->get($file);

            $matches = [];
            preg_match_all('/^\[([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})\].*' . $escaped . '(\S*):\d*\)$/mU', $content, $matches);

            $timestamps = $matches[1];
            $filenames = $matches[2];

            foreach ($timestamps as $index => $timestamp) {

                $errorData[] = array(
                    'log_file_name' => $file,
                    'timestamp' => $timestamp,
                    'filename' => $filenames[$index]
                );
            }
        }

        foreach ($errorData as $key => $error) {
            $cmdReponse = [];
            $cmd = 'git log -n 1 ' . $path . $error['filename'] . ' 2>&1';
            exec($cmd, $cmdReponse);
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

        foreach ($errorData as $error) {

            LaravelGithubLog::firstOrCreate(
                [
                    'log_time' => $error['timestamp'],
                    'log_file_name' => $error['log_file_name'],
                    'file' => $error['filename'],
                    'author' =>  $error['commit']['author'],
                    'commit_time' => $error['commit']['date']
                ]
            );
        }

        echo 'done';
    }

    private function getDetailsFromCommit($commit)
    {
        foreach ($commit as $line) {
            if ($this->startsWith($line, 'Author: ')) {
                $author = substr($line, strlen('Author: '), -1);
                $author = trim($author);
            } else if ($this->startsWith($line, 'Date: ')) {
                $date = substr($line, strlen('Date: '), -1);
                $date = trim($date);
            }
        }
        if (isset($author) && isset($date)) {
            return array(
                'author' => $author,
                'date' => $date
            );
        }
        return false;
    }

    function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}
