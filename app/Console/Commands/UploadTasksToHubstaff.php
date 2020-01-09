<?php

namespace App\Console\Commands;

use App\DeveloperTask;
use DB;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use Storage;



class UploadTasksToHubstaff extends Command
{

    var $HUBSTAFF_TOKEN_FILE_NAME;
    var $SEED_REFRESH_TOKEN;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:upload_tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload all the tasks to hubstaff';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->HUBSTAFF_TOKEN_FILE_NAME = 'hubstaff_tokens.json';
        $this->SEED_REFRESH_TOKEN  = getenv('HUBSTAFF_SEED_PERSONAL_TOKEN');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //

        $assignedTasks = DB::table('developer_tasks')
            ->leftJoin('hubstaff_members', 'hubstaff_members.user_id', '=', 'developer_tasks.user_id')
            ->select(['developer_tasks.id', 'developer_tasks.subject as summary', 'developer_tasks.task as task', 'hubstaff_members.hubstaff_user_id as assignee_id'])
            //->whereNotNull('hubstaff_members.hubstaff_user_id')
            ->get();


        //echo $assignedTasks[0]->summary;
        echo "Total Dev tasks: " . sizeof($assignedTasks) . PHP_EOL;
        $this->uploadTasks($assignedTasks);
        echo "DONE";
    }

    private function uploadTasks($tasks)
    {
        foreach ($tasks as $index => $task) {
            $taskId = $this->uploadTask($task);
            if ($taskId) {
                echo "(" . ($index + 1) . "/" . sizeof($tasks) . ") Created Hubstaff Task: " . $taskId . ' for task: ' . $task->id . PHP_EOL;
            } else {
                echo "(" . ($index + 1) . "/" . sizeof($tasks) . ")Failed to create task for task ID: " . $task->id . PHP_EOL;
            }
        }
    }

    private function uploadTask($task, $shouldRetry = true)
    {

        $tokens = $this->getTokens();

        $url = 'https://api.hubstaff.com/v2/projects/' . getenv('HUBSTAFF_BULK_IMPORT_PROJECT_ID') . '/tasks';
        $httpClient = new Client();
        try {

            $summary = $task->summary.'=>'.$task->task;

            $response = $httpClient->post(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $tokens->access_token,
                        'Content-Type' => 'application/json'
                    ],

                    RequestOptions::BODY => json_encode([
                        'summary' => $summary,
                        'assignee_id' => isset($task->assignee_id) ? $task->assignee_id : getenv('HUBSTAFF_DEFAULT_ASSIGNEE_ID')
                    ])
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());
            return $parsedResponse->task->id;
        } catch (Exception $e) {
            if ($e instanceof ClientException) {
                $this->refreshTokens();
                if ($shouldRetry) {
                    return $this->addTaskToHubstaff(
                        $task,
                        false
                    );
                }
            }
        }
        return false;
    }

    private function getTokens()
    {
        if (!Storage::disk('local')->exists($this->HUBSTAFF_TOKEN_FILE_NAME)) {
            $this->generateAccessToken($this->SEED_REFRESH_TOKEN);
        }
        $tokens = json_decode(Storage::disk('local')->get($this->HUBSTAFF_TOKEN_FILE_NAME));
        return $tokens;
    }

    /**
     * returns boolean
     */
    private function generateAccessToken(string $refreshToken)
    {
        $httpClient = new Client();
        try {
            $response = $httpClient->post(
                'https://account.hubstaff.com/access_tokens',
                [
                    RequestOptions::FORM_PARAMS => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $refreshToken
                    ]
                ]
            );

            $responseJson = json_decode($response->getBody()->getContents());

            $tokens = [
                'access_token' => $responseJson->access_token,
                'refresh_token' => $responseJson->refresh_token
            ];

            return Storage::disk('local')->put($this->HUBSTAFF_TOKEN_FILE_NAME, json_encode($tokens));
        } catch (Exception $e) {
            return false;
        }
    }
}
