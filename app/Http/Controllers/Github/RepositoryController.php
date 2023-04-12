<?php

namespace App\Http\Controllers\Github;

use App\DeveloperTask;
use App\DeveoperTaskPullRequestMerge;
use App\Github\GithubBranchState;
use App\Github\GithubRepository;
use App\GitMigrationErrorLog;
use App\Helpers\GithubTrait;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use Artisan;
use Carbon\Carbon;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RepositoryController extends Controller
{
    use GithubTrait;

    private $client;

    public function __construct()
    {
        $this->client = new Client([
            // 'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
            'auth' => [config('env.GITHUB_USERNAME'), config('env.GITHUB_TOKEN')],
        ]);
    }

    private function refreshGithubRepos()
    {
        // $url = "https://api.github.com/orgs/" . getenv('GITHUB_ORG_ID') . "/repos?per_page=100";
        $url = 'https://api.github.com/orgs/'.config('env.GITHUB_ORG_ID').'/repos?per_page=100';

        $response = $this->client->get($url);

        $repositories = json_decode($response->getBody()->getContents());

        $dbRepositories = [];

        foreach ($repositories as $repository) {
            $data = [
                'id' => $repository->id,
                'name' => $repository->name,
                'html' => $repository->html_url,
                'webhook' => $repository->hooks_url,
                'created_at' => Carbon::createFromFormat(DateTime::ISO8601, $repository->created_at),
                'updated_at' => Carbon::createFromFormat(DateTime::ISO8601, $repository->updated_at),
            ];

            GithubRepository::updateOrCreate(
                [
                    'id' => $repository->id,
                ],
                $data
            );
            $dbRepositories[] = $data;
        }

        return $dbRepositories;
    }

    //
    public function listRepositories()
    {
        $repositories = $this->refreshGithubRepos();

        return view('github.repositories', [
            'repositories' => $repositories,
        ]);
    }

    public function getRepositoryDetails($repositoryId)
    {
        $repository = GithubRepository::find($repositoryId);
        $branches = $repository->branches;

        $currentBranch = exec('/usr/bin/sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').$repository->name.'/get_current_deployment.sh');

        //exec('sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh master');

        //exit;
        return view('github.repository_settings', [
            'repository' => $repository,
            'branches' => $branches,
            'current_branch' => $currentBranch,
        ]);

        //print_r($repository);
    }

    public function deployBranch($repoId, Request $request)
    {
        //dd($repoId);
        $source = 'master';
        $destination = $request->branch;
        $pullOnly = request('pull_only', 0);

        $url = 'https://api.github.com/repositories/'.$repoId.'/merges';

        try {
            // Merge master into branch
            if (empty($pullOnly) || $pullOnly != 1) {
                $this->client->post(
                    $url,
                    [
                        RequestOptions::BODY => json_encode([
                            'base' => $destination,
                            'head' => $source,
                        ]),
                    ]
                );
                //Artisan::call('github:load_branch_state');
                if ($source == 'master') {
                    $this->updateBranchState($repoId, $destination);
                } elseif ($destination == 'master') {
                    $this->updateBranchState($repoId, $source);
                }
            }

            // Deploy branch
            $repository = GithubRepository::find($repoId);

            $branch = $request->branch;
            $composerupdate = request('composer', false);

            $cmd = 'sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'/'.$repository->name.'/deploy_branch.sh '.$branch.' '.$composerupdate.' 2>&1';
            //echo 'sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh '.$branch;

            $allOutput = [];
            $allOutput[] = $cmd;
            $result = exec($cmd, $allOutput);

            $migrationError = is_array($result) ? json_encode($result) : $result;
            if (Str::contains($migrationError, 'database/migrations') || Str::contains($migrationError, 'migrations') || Str::contains($migrationError, 'Database/Migrations') || Str::contains($migrationError, 'Migrations')) {
                if ($source == 'master') {
                    $this->createGitMigrationErrorLog($repoId, $destination, $migrationError);
                } elseif ($destination == 'master') {
                    $this->createGitMigrationErrorLog($repoId, $source, $migrationError);
                } else {
                    $this->createGitMigrationErrorLog($repoId, $source, $migrationError);
                }
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
            $errorArr = [];
            $errorArr = $e->getMessage();
            if (! is_array($errorArr)) {
                $arrErr[] = $errorArr;
                $errorArr = implode(' ', $arrErr);
            } else {
                $arrErr = $errorArr;
                $errorArr = $errorArr;
            }
            $migrationError = is_array($result) ? json_encode($errorArr) : $errorArr;
            if (Str::contains($migrationError, 'database/migrations') || Str::contains($migrationError, 'migrations') || Str::contains($migrationError, 'Database/Migrations') || Str::contains($migrationError, 'Migrations')) {
                if ($source == 'master') {
                    $this->createGitMigrationErrorLog($repoId, $destination, $migrationError);
                } elseif ($destination == 'master') {
                    $this->createGitMigrationErrorLog($repoId, $source, $migrationError);
                } else {
                    $this->createGitMigrationErrorLog($repoId, $source, $migrationError);
                }
            }

            return redirect(url('/github/pullRequests'))->with(
                [
                    'message' => $e->getMessage(),
                    'alert-type' => 'error',
                ]
            );
        }

        return redirect(url('/github/pullRequests'))->with([
            'message' => print_r($result, true),
            'alert-type' => 'success',
        ]);
    }

    /**
     * Undocumented function
     *
     * @param [mix] $repoId
     * @param [mix] $branchName
     * @param [array] $errorLog
     * @return void
     */
    public function createGitMigrationErrorLog($repoId, $branchName, $errorLog)
    {
        $comparison = $this->compareRepoBranches($repoId, $branchName);
        GitMigrationErrorLog::create([
            'repository_id' => $repoId,
            'branch_name' => $branchName,
            'ahead_by' => $comparison['ahead_by'],
            'behind_by' => $comparison['behind_by'],
            'last_commit_author_username' => $comparison['last_commit_author_username'],
            'last_commit_time' => $comparison['last_commit_time'],
            'error' => $errorLog,
        ]);
    }

    public function getGitMigrationErrorLog()
    {
        try {
            $gitDbError = GitMigrationErrorLog::orderBy('id', 'desc')->take(100)->get();

            return view('github.deploy_branch_error', compact('gitDbError'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    private function updateBranchState($repoId, $branchName)
    {
        $comparison = $this->compareRepoBranches($repoId, $branchName);
        \Log::info("Add entry to GithubBranchState");
        GithubBranchState::updateOrCreate(
            [
                'repository_id' => $repoId,
                'branch_name' => $branchName,
            ],
            [
                'repository_id' => $repoId,
                'branch_name' => $branchName,
                'ahead_by' => $comparison['ahead_by'],
                'behind_by' => $comparison['behind_by'],
                'last_commit_author_username' => $comparison['last_commit_author_username'],
                'last_commit_time' => $comparison['last_commit_time'],
            ]
        );
    }

    private function findDeveloperTask($branchName)
    {
        $devTaskId = null;
        $usIt = explode('-', $branchName);

        if (count($usIt) > 1) {
            $devTaskId = $usIt[1];
        } else {
            $usIt = explode(' ', $branchName);
            if (count($usIt) > 1) {
                $devTaskId = $usIt[1];
            }
        }

        return  DeveloperTask::find($devTaskId);
    }

    private function updateDevTask($branchName, $pull_request_id)
    {
        $devTask = $this->findDeveloperTask($branchName); //DeveloperTask::find($devTaskId);

        \Log::info('updateDevTask call '.$branchName);

        if ($devTask) {
            \Log::info('updateDevTask find success '.$branchName);
            try {
                \Log::info('updateDevTask :: PR merge msg send .'.json_encode($devTask->user));

                $message = $branchName.':: PR has been merged';

                $requestData = new Request();
                $requestData->setMethod('POST');
                $requestData->request->add(['issue_id' => $devTask->id, 'message' => $message, 'status' => 1]);
                app(\App\Http\Controllers\WhatsAppController::class)->sendMessage($requestData, 'issue');

                MessageHelper::sendEmailOrWebhookNotification([$devTask->assigned_to, $devTask->team_lead_id, $devTask->tester_id], $message.'. kindly test task in live if possible and put test result as comment in task.');
                $devTask->update(['is_pr_merged' => 1]);

                $request = new DeveoperTaskPullRequestMerge;
                $request->task_id = $devTask->id;
                $request->pull_request_id = $pull_request_id;
                $request->user_id = auth()->user()->id;
                $request->save();

                //app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($devTask->user->phone, $devTask->user->whatsapp_number, $branchName.':: PR has been merged', false);
            } catch (Exception $e) {
                \Log::info('updateDevTask ::'.$e->getMessage());
                \Log::error('updateDevTask ::'.$e->getMessage());
            }

            $devTask->status = 'In Review';
            $devTask->save();
        }
    }

    public function mergeBranch($id, Request $request)
    {
        $source = $request->source;
        $destination = $request->destination;
        $pull_request_id = $request->task_id;

        $url = 'https://api.github.com/repositories/'.$id.'/merges';
        
        try {
            $this->client->post(
                $url,
                [
                    RequestOptions::BODY => json_encode([
                        'base' => $destination,
                        'head' => $source,
                    ]),
                ]
            );
            //Artisan::call('github:load_branch_state');
            if ($source == 'master') {
                $this->updateBranchState($id, $destination);
            } elseif ($destination == 'master') {
                $this->updateBranchState($id, $source);
            }

            \Log::info('updateDevTask calling...'.$source);
            $this->updateDevTask($source, $pull_request_id);

            // Deploy branch
            $repository = GithubRepository::find($id);

            $branch = 'master';
            //echo 'sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh '.$branch;

            $cmd = 'sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').$repository->name.'/deploy_branch.sh '.$branch.' 2>&1';
            $allOutput = [];
            $allOutput[] = $cmd;
            $result = exec($cmd, $allOutput);
            \Log::info(print_r($allOutput, true));

            // Used to reset php cache after merge.
            // opcache_reset();

            $sqlIssue = false;
            if (! empty($allOutput) && is_array($allOutput)) {
                foreach ($allOutput as $output) {
                    if (strpos(strtolower($output), 'sqlstate') !== false) {
                        $sqlIssue = true;
                    }
                }
            }
            if ($sqlIssue) {
                $devTask = $this->findDeveloperTask($source);
                if ($devTask) {
                    $message = $source.':: there is some issue while running migration please check migration or contact administrator';
                    $requestData = new Request();
                    $requestData->setMethod('POST');
                    $requestData->request->add(['issue_id' => $devTask->id, 'message' => $message, 'status' => 1]);
                    app(\App\Http\Controllers\WhatsAppController::class)->sendMessage($requestData, 'issue');
                }

                //Merged to master get migration error
                $migrationError = is_array($allOutput) ? json_encode($allOutput) : $allOutput;
                $this->createGitMigrationErrorLog($id, $source, $migrationError);

                return redirect(url('/github/pullRequests'))->with([
                    'message' => 'Branch merged successfully but migration failed',
                    'alert-type' => 'error',
                ]);
            }
        } catch (Exception $e) {
            \Log::error($e);
            print_r($e->getMessage());
            $devTask = $this->findDeveloperTask($source);
            if ($devTask) {
                $message = $source.':: Failed to Merge please check branch has not any conflict or contact administrator';
                $requestData = new Request();
                $requestData->setMethod('POST');
                $requestData->request->add(['issue_id' => $devTask->id, 'message' => $message, 'status' => 1]);
                app(\App\Http\Controllers\WhatsAppController::class)->sendMessage($requestData, 'issue');
            }

            return redirect(url('/github/pullRequests'))->with(
                [
                    'message' => 'Failed to Merge please check branch has not any conflict !',
                    'alert-type' => 'error',
                ]
            );
        }

        return redirect(url('/github/pullRequests'))->with([
            'message' => 'Branch merged successfully',
            'alert-type' => 'success',
        ]);
    }

    private function getPullRequests($repoId)
    {
        $pullRequests = [];
        $url = 'https://api.github.com/repositories/'.$repoId.'/pulls?per_page=200';
        try {
            $response = $this->client->get($url);
            $decodedJson = json_decode($response->getBody()->getContents());
            foreach ($decodedJson as $pullRequest) {
                $pullRequests[] = [
                    'id' => $pullRequest->number,
                    'title' => $pullRequest->title,
                    'number' => $pullRequest->number,
                    'username' => $pullRequest->user->login,
                    'userId' => $pullRequest->user->id,
                    'updated_at' => $pullRequest->updated_at,
                    'source' => $pullRequest->head->ref,
                    'destination' => $pullRequest->base->ref,
                ];
            }
        } catch (Exception $e) {
        }

        return $pullRequests;
    }

    public function listPullRequests($repoId)
    {
        $repository = GithubRepository::find($repoId);

        $pullRequests = $this->getPullRequests($repoId);

        $branchNames = array_map(
            function ($pullRequest) {
                return $pullRequest['source'];
            },
            $pullRequests
        );

        $branchStates = GithubBranchState::whereIn('branch_name', $branchNames)->get();

        foreach ($pullRequests as $pullRequest) {
            $pullRequest['branchState'] = $branchStates->first(
                function ($value, $key) use ($pullRequest) {
                    return $value->branch_name == $pullRequest['source'];
                }
            );
        }

        return view('github.repository_pull_requests', [
            'pullRequests' => $pullRequests,
            'repository' => $repository,
        ]);
    }

    public function closePullRequestFromRepo($repositoryId, $pullRequestNumber){
        return $this->closePullRequest($repositoryId, $pullRequestNumber);
    }

    public function actionWorkflows(Request $request, $repositoryId){
        $githubActionRuns = $this->githubActionResult($repositoryId,$request->page);
        return view('github.action_workflows', [
            'githubActionRuns' => $githubActionRuns,
            'repositoryId' => $repositoryId
        ]);
    }

    public function ajaxActionWorkflows(Request $request, $repositoryId){
        return $this->githubActionResult($repositoryId,$request->page);
    }

    public function githubActionResult($repositoryId, $page, $date = null){
        $githubActionRuns = $this->getGithubActionRuns($repositoryId, $page, $date);
        foreach($githubActionRuns->workflow_runs as $key => $runs){
            $githubActionRuns->workflow_runs[$key]->failure_reason = "";
            if($runs->conclusion == "failure"){
                $githubActionRunJobs = $this->getGithubActionRunJobs($repositoryId,$runs->id);
                foreach($githubActionRunJobs->jobs as $job){
                    foreach($job->steps as $step){
                        if($step->conclusion == "failure"){
                            $githubActionRuns->workflow_runs[$key]->failure_reason = $step->name;
                        }
                    }
                }
            }
        }
        return $githubActionRuns;
    }

    public function listAllPullRequests()
    {
        $repositories = GithubRepository::all(['id', 'name']);
        $allPullRequests = [];
        foreach ($repositories as $repository) {
            $pullRequests = $this->getPullRequests($repository->id);
            foreach($pullRequests as $key =>  $pullRequest){
                //Need to execute the detail API as we require the mergeable_state which is only return in the PR detail API.
                $pr = $this->getPullRequestDetail($repository->id,$pullRequest['id']);
                $pullRequests[$key]['mergeable_state'] = $pr['mergeable_state'];
                $pullRequests[$key]['conflict_exist'] = $pr['mergeable_state'] == "dirty" ? true : false;
            }
            $pullRequests = array_map(
                function ($pullRequest) use ($repository) {
                    $pullRequest['repository'] = $repository;

                    return $pullRequest;
                },
                $pullRequests
            );

            $allPullRequests = array_merge($allPullRequests, $pullRequests);
        }

        //echo print_r($allPullRequests, true);

        //exit;
        return view(
            'github.all_pull_requests',
            [
                'pullRequests' => $allPullRequests,
            ]
        );
    }

    public function deployNodeScrapers()
    {
        return $this->getRepositoryDetails(231924853);
    }

    /**
     * Githjub Branch Page
     */
    public function branchIndex(Request $request)
    {
        if($request->ajax()) {
            $data = $this->getAjaxBranches($request);
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }
        $data['repos'] = GithubRepository::all();
        return view('github.branches', $data);
    }

    public function getAjaxBranches(Request $request)
    {
        $branches = GithubBranchState::where('repository_id', $request->repoId)->get();
        return $branches;
    }

    /**
     * Github Actions Page
     */
    public function actionIndex(Request $request)
    {
        if($request->ajax()) {
            $data = $this->getAjaxActions($request);
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }
        $data['repos'] = GithubRepository::all();
        return view('github.actions', $data);
    }

    public function getAjaxActions(Request $request)
    {
        $data = $this->githubActionResult($request->repoId, $request->page, $request->date);
        return $data;
    }
}
