<?php

namespace App\Http\Controllers\Github;

use Auth;
use Artisan;
use App\Task;
use App\User;
use DateTime;
use Exception;
use App\Message;
use Carbon\Carbon;
use App\ChatMessage;
use App\DeveloperTask;
use GuzzleHttp\Client;
use App\Models\Project;
use App\Github\GithubTask;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\BuildProcessHistory;
use App\Helpers\GithubTrait;
use App\Jobs\DeleteBranches;
use Illuminate\Http\Request;
use App\GitMigrationErrorLog;
use App\ChatMessagesQuickData;
use App\Helpers\MessageHelper;
use GuzzleHttp\RequestOptions;
use App\Github\GithubPrActivity;
use App\Github\GithubPrErrorLog;
use App\Github\GithubRepository;
use App\Github\GithubBranchState;
use App\Github\GithubOrganization;
use Illuminate\Support\Facades\DB;
use App\Github\GithubRepositoryJob;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\DeveoperTaskPullRequestMerge;
use App\Github\GithubPullRequest;
use App\Github\GithubRepositoryLabel;
use App\Github\GithubTaskPullRequest;
use App\Models\DeletedGithubBranchLog;
use App\Http\Requests\DeleteBranchRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use App\Models\GitPullRequestErrorLog;
use App\Models\GithubToken;

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

    private function connectGithubClient($userName, $token)
    {
        $githubClient = new Client([
            // 'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')],
            'auth' => [$userName, $token],
        ]);

        return $githubClient;
    }

    private function refreshGithubRepos($organizationId)
    {
        if (strlen($organizationId) > 0) {
            $organization = GithubOrganization::find($organizationId);
        } else {
            $organization = GithubOrganization::where('name', 'MMMagento')->first();
        }

        $dbRepositories = [];

        try {
            if (! empty($organization)) {
                // $url = "https://api.github.com/orgs/" . getenv('GITHUB_ORG_ID') . "/repos?per_page=100";
                $url = 'https://api.github.com/orgs/' . $organization->name . '/repos?per_page=100';

                $githubClient = $this->connectGithubClient($organization->username, $organization->token);

                $response = $githubClient->get($url);

                $repositories = json_decode($response->getBody()->getContents());

                foreach ($repositories as $repository) {
                    $data = [
                        'id' => $repository->id,
                        'github_organization_id' => $organization->id,
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
            }
        } catch(\Exception $e) {
        }

        return $dbRepositories;
    }

    //
    public function listRepositories(Request $request, $organizationId = '')
    {
        $repositories = $this->refreshGithubRepos($organizationId);

        $githubOrganizations = GithubOrganization::get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('github.include.repository-list', compact('repositories'))->render(),
                'count' => count($repositories),
            ], 200);
        }

        return view('github.repositories', [
            'repositories' => $repositories,
            'githubOrganizations' => $githubOrganizations,
            'organizationId' => $organizationId,
        ]);
    }

    public function getRepositoryDetails($repositoryId)
    {
        $repository = GithubRepository::find($repositoryId);
        $branches = $repository->branches;

        $currentBranch = exec('/usr/bin/sh ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . $repository->name . '/get_current_deployment.sh');

        //exec('sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh master');

        //exit;
        return view('github.repository_settings', [
            'repository' => $repository,
            'branches' => $branches,
            'current_branch' => $currentBranch,
        ]);
    }

    public function deployBranch($repoId, Request $request)
    {
        $repository = GithubRepository::find($repoId);
        $organization = $repository->organization;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        //dd($repoId);
        $source = 'master';
        $destination = $request->branch;
        $pullOnly = request('pull_only', 0);

        $url = 'https://api.github.com/repositories/' . $repoId . '/merges';

        try {
            // Merge master into branch
            if (empty($pullOnly) || $pullOnly != 1) {
                $githubClient->post(
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

            $cmd = 'sh ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . '/' . $repository->name . '/deploy_branch.sh ' . $branch . ' ' . $composerupdate . ' 2>&1';
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
            'message' => print_r($allOutput, true),
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
        $repository = GithubRepository::find($repoId);
        $organization = $repository->organization;

        $comparison = $this->compareRepoBranches($organization->username, $organization->token, $repoId, $branchName);
        GitMigrationErrorLog::create([
            'github_organization_id' => $organization->id,
            'repository_id' => $repoId,
            'branch_name' => $branchName,
            'ahead_by' => $comparison['ahead_by'],
            'behind_by' => $comparison['behind_by'],
            'last_commit_author_username' => $comparison['last_commit_author_username'],
            'last_commit_time' => $comparison['last_commit_time'],
            'error' => $errorLog,
        ]);
    }

    public function getGitMigrationErrorLog(Request $request)
    {
        if ($request->ajax()) {
            $gitDbError = GitMigrationErrorLog::where('repository_id', $request->repoId)->orderBy('id', 'desc')->take(100)->get();

            return response()->json([
                'tbody' => view('github.include.migration-error-logs-list', compact('gitDbError'))->render(),
                'count' => count($gitDbError),
            ], 200);
        }

        try {
            $githubOrganizations = GithubOrganization::with('repos')->get();

            return view('github.deploy_branch_error', compact('githubOrganizations'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    private function updateBranchState($repoId, $branchName)
    {
        $repository = GithubRepository::find($repoId);
        $organization = $repository->organization;

        $comparison = $this->compareRepoBranches($organization->username, $organization->token, $repoId, $branchName);
        $filters = [
            'state' => 'all',
            'head' => $organization->name . ':' . $branchName,
        ];
        $pullRequests = $this->pullRequests($organization->username, $organization->token, $repoId, $filters);
        if (! empty($pullRequests) && count($pullRequests) > 0) {
            $pullRequest = $pullRequests[0];
        }
        \Log::info('Add entry to GithubBranchState');
        GithubBranchState::updateOrCreate(
            [
                'repository_id' => $repoId,
                'branch_name' => $branchName,
            ],
            [
                'github_organization_id' => $organization->id,
                'repository_id' => $repoId,
                'branch_name' => $branchName,
                'status' => ! empty($pullRequest) ? $pullRequest['state'] : '',
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

        \Log::info('updateDevTask call ' . $branchName);

        if ($devTask) {
            \Log::info('updateDevTask find success ' . $branchName);
            try {
                \Log::info('updateDevTask :: PR merge msg send .' . json_encode($devTask->user));

                $message = $branchName . ':: PR has been merged';

                $requestData = new Request();
                $requestData->setMethod('POST');
                $requestData->request->add(['issue_id' => $devTask->id, 'message' => $message, 'status' => 1]);
                app(\App\Http\Controllers\WhatsAppController::class)->sendMessage($requestData, 'issue');

                MessageHelper::sendEmailOrWebhookNotification([$devTask->assigned_to, $devTask->team_lead_id, $devTask->tester_id], $message . '. kindly test task in live if possible and put test result as comment in task.');
                $devTask->update(['is_pr_merged' => 1]);

                $request = new DeveoperTaskPullRequestMerge;
                $request->task_id = $devTask->id;
                $request->pull_request_id = $pull_request_id;
                $request->user_id = auth()->user()->id;
                $request->save();

                //app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($devTask->user->phone, $devTask->user->whatsapp_number, $branchName.':: PR has been merged', false);
            } catch (Exception $e) {
                \Log::info('updateDevTask ::' . $e->getMessage());
                \Log::error('updateDevTask ::' . $e->getMessage());
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

        $repository = GithubRepository::find($id);
        $organization = $repository->organization;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        $url = 'https://api.github.com/repositories/' . $id . '/merges';

        try {
            $githubClient->post(
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

            \Log::info('updateDevTask calling...' . $source);
            $this->updateDevTask($source, $pull_request_id);

            // Deploy branch
            $repository = GithubRepository::find($id);

            $branch = 'master';
            //echo 'sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh '.$branch;

            $cmd = 'sh ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . $repository->name . '/deploy_branch.sh ' . $branch . ' 2>&1';
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
                    $message = $source . ':: there is some issue while running migration please check migration or contact administrator';
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
                $message = $source . ':: Failed to Merge please check branch has not any conflict or contact administrator';
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

    private function getPullRequests($userName, $token, $repoId, $filters = [])
    {
        $addedFilters = ! empty($filters) ? Arr::query($filters) : '';
        $pullRequests = [];
        $url = 'https://api.github.com/repositories/' . $repoId . '/pulls?per_page=200';
        if (! empty($addedFilters)) {
            $url .= '&' . $addedFilters;
        }
        try {
            $githubClient = $this->connectGithubClient($userName, $token);

            $response = $githubClient->get($url);

            $decodedJson = json_decode($response->getBody()->getContents());
            foreach ($decodedJson as $pullRequest) {
                $pullRequests[] = [
                    'id' => $pullRequest->number,
                    'title' => $pullRequest->title,
                    'number' => $pullRequest->number,
                    'state' => $pullRequest->state,
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
        $organization = $repository->organization;

        $pullRequests = $this->getPullRequests($organization->username, $organization->token, $repoId);

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

    public function closePullRequestFromRepo($repositoryId, $pullRequestNumber)
    {
        return $this->closePullRequest($repositoryId, $pullRequestNumber);
    }

    public function deleteBranchFromRepo($repositoryId, DeleteBranchRequest $request)
    {
        $response = $this->deleteBranch($repositoryId, $request->branch_name);
        $githubBranchState = GithubBranchState::where('repository_id', $repositoryId)->where('branch_name', $request->branch_name)->first();
        if (! empty($githubBranchState) && $response['status']) {
            DeletedGithubBranchLog::create([
                'branch_name' => $request->branch_name,
                'repository_id' => $repositoryId,
                'deleted_by' => \Auth::id(),
                'status' => 'success',
            ]);

            $githubBranchState->delete();
        } else {
            DeletedGithubBranchLog::create([
                'branch_name' => $request->branch_name,
                'repository_id' => $repositoryId,
                'deleted_by' => \Auth::id(),
                'status' => 'failed',
                'error_message' => $response['error'],
            ]);
        }

        return $response;
    }

    // devtask - 23311
    public function deleteNumberOfBranchesFromRepo($repositoryId, Request $request)
    {
        $branches = $this->getGithubBranches($repositoryId, []);
        $numberOfBranchesToDelete = $request->number_of_branches;
        $branchArr = [];

        for ($i = 0; $i < $numberOfBranchesToDelete; $i++) {
            if (isset($branches[$i]->name)) {
                $branchArr[$i] = $branches[$i]->name;
            }
        }

        $response = DeleteBranches::dispatch($branchArr, $repositoryId)->onQueue('delete_github_branches');

        return response()->json(['type' => 'success'], 200);
    }

    public function actionWorkflows(Request $request, $repositoryId)
    {
        $status = $date = $branchName = null;
        if ($request->status) {
            $status = $request->status;
        }

        if ($request->repoId) {
            $selectedRepositoryId = $request->repoId;
        } else {
            $selectedRepositoryId = $repositoryId;
        }

        if ($request->branchName) {
            $branchName = $request->branchName;
        }

        $selectedRepository = GithubRepository::where('id', $selectedRepositoryId)->first();
        $selectedOrganizationID = $selectedRepository->organization->id;
        $githubActionRuns = $this->githubActionResult($selectedRepositoryId, $request->page, $date, $status, $branchName);

        $githubOrganizations = GithubOrganization::with('repos')->get();
        // Get Repo Jobs from DB & Prepare the status.
        $githubRepositoryJobs = GithubRepositoryJob::where('github_repository_id', $selectedRepositoryId)->pluck('job_name')->toArray();

        // Paginate
        // Set the current page number
        $currentPage = request()->query('page', 1);
        $githubActionRunsPaginated = new LengthAwarePaginator(
            $githubActionRuns,
            $githubActionRuns->total_count,
            30, // Default count API
            $currentPage,
            ['path' => request()->url()]
        );

        // Preserve existing query parameters
        $githubActionRunsPaginated->appends($request->query());

        // Customize the pagination output (optional)
        $githubActionRunsPaginated->withPath(Paginator::resolveCurrentPath());

        return view('github.action_workflows', [
            'githubActionRuns' => $githubActionRunsPaginated,
            'repositoryId' => $repositoryId,
            'selectedRepositoryId' => $selectedRepositoryId,
            'githubOrganizations' => $githubOrganizations,
            'selectedOrganizationID' => $selectedOrganizationID,
            'selectedRepoBranches' => $selectedRepository->branches,
            'branchName' => $branchName,
            'githubRepositoryJobs' => $githubRepositoryJobs,
        ]);
    }

    public function ajaxActionWorkflows(Request $request, $repositoryId)
    {
        return $this->githubActionResult($repositoryId, $request->page);
    }

    public function githubActionResult($repositoryId, $page, $date = null, $status = null, $branchName = null)
    {
        ini_set('max_execution_time', -1);

        $githubActionRuns = $this->getGithubActionRuns($repositoryId, $page, $date, $status, $branchName);
        // Get Repo Jobs from DB & Prepare the status.
        $githubRepositoryJobs = GithubRepositoryJob::where('github_repository_id', $repositoryId)->pluck('job_name')->toArray();

        foreach ($githubActionRuns->workflow_runs as $key => $runs) {
            $githubActionRuns->workflow_runs[$key]->failure_reason = '';
            $githubActionRunJobs = $this->getGithubActionRunJobs($repositoryId, $runs->id);
            if ($runs->conclusion == 'failure') {
                foreach ($githubActionRunJobs->jobs as $job) {
                    foreach ($job->steps as $step) {
                        if ($step->conclusion == 'failure') {
                            $githubActionRuns->workflow_runs[$key]->failure_reason = $step->name;
                        }
                    }
                }
            }
            // Prepareing job status for every actions
            $githubActionRuns->workflow_runs[$key]->job_status = [];
            foreach ($githubActionRunJobs->jobs as $job) {
                if (in_array($job->name, $githubRepositoryJobs)) {
                    $githubActionRuns->workflow_runs[$key]->job_status[$job->name] = $job->status;
                }
            }
        }

        return $githubActionRuns;
    }

    public function getGithubJobs(Request $request)
    {
        // Get the action ID from the query parameter
        $actionId = $request->query('action_id');
        $repositoryId = $request->query('selectedRepositoryId');

        $githubActionRunJobs = $this->getGithubActionRunJobs($repositoryId, $actionId);

        return view('github.jobs', ['githubActionRunJobs' => $githubActionRunJobs]);
    }

    public function getGithubActionsAndJobs(Request $request)
    {
        // Get the action ID from the query parameter
        $repositoryId = $request->query('selectedRepositoryId');
        $branchName = $request->query('selectedBranchName');

        // Prepare the actions & jobs
        $githubActionRuns = $this->getGithubActionRuns($repositoryId, 1, null, null, $branchName);
        $actions = [];
        if ($githubActionRuns) {
            foreach ($githubActionRuns->workflow_runs as $action) {
                $actionName = $action->name;
                $jobs = [];

                $githubActionRunJobs = $this->getGithubActionRunJobs($repositoryId, $action->id);
                if ($githubActionRunJobs) {
                    foreach ($githubActionRunJobs->jobs as $job) {
                        if ($job->run_id === $action->id) {
                            $jobSteps = [];

                            // Assuming you have the steps data available, either from the $githubActionRunJobs or another source
                            // Replace 'steps' with the actual key containing steps data
                            foreach ($job->steps as $step) {
                                if ($step->conclusion != 'success') {
                                    $jobSteps[] = [
                                        'name' => $step->name,
                                        'conclusion' => $step->conclusion,
                                    ];
                                }
                            }

                            // Add the job to the jobs array
                            $jobs[] = [
                                'id' => $job->id,
                                'name' => $job->name,
                                'status' => $job->status,
                                'conclusion' => $job->conclusion,
                                'steps' => $jobSteps,
                            ];
                        }
                    }
                }

                // Add the action with its jobs to the actions array
                $actions[] = [
                    'name' => $actionName,
                    'jobs' => $jobs,
                ];
            }
        }

        return view('github.actions-jobs', ['actions' => $actions]);
    }

    public function listAllPullRequests(Request $request)
    {
        $projects = Project::get();
        if ($request->ajax()) {
            ini_set('max_execution_time', -1);

            $repositories = GithubRepository::where('id', $request->repoId)->get();
            $allPullRequests = [];

            foreach ($repositories as $repository) {
                $organization = $repository->organization;
                $pullRequests = $this->getPullRequests($organization->username, $organization->token, $repository->id);

                foreach ($pullRequests as $key => $pullRequest) {
                    //Need to execute the detail API as we require the mergeable_state which is only return in the PR detail API.
                    $pr = $this->getPullRequestDetail($organization->username, $organization->token, $repository->id, $pullRequest['id']);
                    $pullRequests[$key]['mergeable_state'] = $pr['mergeable_state'];
                    $pullRequests[$key]['conflict_exist'] = $pr['mergeable_state'] == 'dirty' ? true : false;
                    // Get Latest Activity for this PR
                    $pullRequests[$key]['latest_activity'] = [];
                    $latestGithubPrActivity = GithubPrActivity::latest('activity_id')
                        ->where('github_organization_id', $organization->id)
                        ->where('github_repository_id', $repository->id)
                        ->where('pull_number', $pullRequest['id'])
                        ->first();
                    if ($latestGithubPrActivity) {
                        $pullRequests[$key]['latest_activity'] = [
                            'activity_id' => $latestGithubPrActivity->activity_id,
                            'user' => $latestGithubPrActivity->user,
                            'event' => $latestGithubPrActivity->event,
                            'label_name' => $latestGithubPrActivity->label_name,
                            'label_color' => $latestGithubPrActivity->label_color,
                        ];
                    }

                    // check build process logs
                    $totalBuildProcessHistoryCount = BuildProcessHistory::where('github_organization_id', $organization->id)
                        ->where('github_repository_id', $repository->id)
                        ->where('github_branch_state_name', $pullRequest['source'])
                        ->count();

                    $totalBuildProcessSuccessHistoryCount = BuildProcessHistory::where('github_organization_id', $organization->id)
                        ->where('github_repository_id', $repository->id)
                        ->where('github_branch_state_name', $pullRequest['source'])
                        ->where('status', 'SUCCESS')
                        ->count();

                    $pullRequests[$key]['build_process_history_status'] = '';
                    if ($totalBuildProcessHistoryCount > 0) {
                        if ($totalBuildProcessHistoryCount == $totalBuildProcessSuccessHistoryCount) {
                            $pullRequests[$key]['build_process_history_status'] = 'Success';
                        } else {
                            $pullRequests[$key]['build_process_history_status'] = 'Danger';
                        }
                    }
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

            return response()->json([
                'tbody' => view('github.include.pull-request-list', compact(['pullRequests', 'projects']))->render(),
                'count' => count($pullRequests),
            ], 200);
        }

        $githubOrganizations = GithubOrganization::with('repos')->get();

        return view('github.all_pull_requests', compact(['githubOrganizations', 'projects']));
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
        if ($request->ajax()) {
            $data = $this->getAjaxBranches($request);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }
        $githubOrganizations = GithubOrganization::with('repos')->get();

        return view('github.branches', compact('githubOrganizations'));
    }

    public function getAjaxBranches(Request $request)
    {
        $branches = GithubBranchState::where('repository_id', $request->repoId)->orderBy('created_at', 'desc');
        if ($request->status) {
            $branches = $branches->where('status', $request->status);
        }

        return $branches->get();
    }

    /**
     * Github Actions Page
     */
    public function actionIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->getAjaxActions($request);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        $githubOrganizations = GithubOrganization::with('repos')->get();

        return view('github.actions', compact('githubOrganizations'));
    }

    public function getAjaxActions(Request $request)
    {
        $data = $this->githubActionResult($request->repoId, $request->page, $request->date);

        return $data;
    }

    public function rerunGithubAction($repoId, $jobId)
    {
        $data = $this->rerunAction($repoId, $jobId);

        return $data;
    }

    public function getPullRequestReviewComments($repo, $pullNumber)
    {
        $repository = GithubRepository::where('id', $repo)->first();
        $organization = $repository->organization;

        // Set the username and token
        $userName = $organization->username;
        $token = $organization->token;

        // Set the repository owner and name
        $owner = $organization->name;
        $repo = $repository->name;

        // Set the number of comments per page
        $perPage = 10;

        // Set the current page number
        $currentPage = request()->query('page', 1);

        // Set the API endpoint for the specific page
        $url = "https://api.github.com/repos/{$owner}/{$repo}/pulls/{$pullNumber}/comments?per_page={$perPage}&page={$currentPage}";

        $totalCount = $commentsPaginated = '';
        try {
            // Send a GET request to the GitHub API
            $githubClient = $this->connectGithubClient($userName, $token);
            $response = $githubClient->get($url);
            $comments = json_decode($response->getBody()->getContents(), true);
            \Log::info(print_r($comments, true));
            $totalCount = $this->getPullRequestReviewTotalCommentsCount($userName, $token, $owner, $repo, $pullNumber);

            // Paginate the comments
            $commentsPaginated = new LengthAwarePaginator(
                $comments,
                $totalCount,
                $perPage,
                $currentPage,
                ['path' => request()->url()]
            );
        } catch (Exception $e) {
            \Log::error($e);
            $errorArr = [];
            $errorArr = $e->getMessage();
            if (! is_array($errorArr)) {
                $arrErr[] = $errorArr;
                $errorArr = implode(' ', $arrErr);
            } else {
                $arrErr = $errorArr;
                $errorArr = $errorArr;
            }

            // Save Error Log in DB
            $githubPrErrorLog = new GithubPrErrorLog();
            $githubPrErrorLog->type = GithubPrErrorLog::TYPE_PR_REVIEW_COMMENTS;
            $githubPrErrorLog->log = $errorArr;
            $githubPrErrorLog->github_organization_id = $organization->id;
            $githubPrErrorLog->github_repository_id = $repository->id;
            $githubPrErrorLog->pull_number = $pullNumber;
            $githubPrErrorLog->save();

            return "<div class='modal-header'><p><strong>Message:</strong> Something went wrong !</p></div><div class='modal-body'><p><strong>Error:</strong> {$errorArr}</p></div>";
        }

        // Return the comments to the view
        return View::make('github.pull-request-review-comments', [
            'comments' => $commentsPaginated,
            'totalCount' => $totalCount,
        ]);
    }

    private function getPullRequestReviewTotalCommentsCount($userName, $token, $owner, $repo, $pullRequestNumber)
    {
        // Set the API endpoint
        $url = "https://api.github.com/repos/{$owner}/{$repo}/pulls/{$pullRequestNumber}";

        // Send a GET request to the GitHub API
        $githubClient = $this->connectGithubClient($userName, $token);
        $response = $githubClient->get($url);

        // Get the response body
        $pullRequest = json_decode($response->getBody(), true);

        // Return the total comment count
        return $pullRequest['review_comments'];
    }

    public function getPullRequestActivities($repo, $pullNumber)
    {
        $repository = GithubRepository::where('id', $repo)->first();
        $organization = $repository->organization;

        // Set the number of activities per page
        $perPage = 10;

        $githubPrActivities = GithubPrActivity::latest('id');
        $githubPrActivities = $githubPrActivities
            ->where('github_organization_id', $organization->id)
            ->where('github_repository_id', $repo)
            ->where('pull_number', $pullNumber);

        $githubPrActivities = $githubPrActivities->paginate($perPage);

        // Return the activities to the view
        return View::make('github.pull-request-activities', [
            'activities' => $githubPrActivities,
        ]);
    }

    // This is not need now.
    // private function getPullRequestActivitiesTotalCount($userName, $token, $owner, $repo, $pullRequestNumber)
    // {
    //     // Set the API endpoint
    //     $url = "https://api.github.com/repos/{$owner}/{$repo}/issues/{$pullRequestNumber}/timeline";

    //     // Send a GET request to the GitHub API
    //     $githubClient = $this->connectGithubClient($userName, $token);
    //     $response = $githubClient->get($url);

    //     // Get the response body
    //     $pullRequest = json_decode($response->getBody(), true);
    //     // Return the total comment count
    //     return count($pullRequest);
    // }

    public function getPrErrorLogs($repoId, $pullNumber)
    {
        $repository = GithubRepository::where('id', $repoId)->first();
        $organization = $repository->organization;

        // Set the number of activities per page
        $perPage = 10;

        $githubPrErrorLogs = GithubPrErrorLog::latest();
        $githubPrErrorLogs = $githubPrErrorLogs
            ->where('github_organization_id', $organization->id)
            ->where('github_repository_id', $repoId)
            ->where('pull_number', $pullNumber);

        $githubPrErrorLogs = $githubPrErrorLogs->paginate($perPage);

        // Return the activities to the view
        return View::make('github.pr-error-logs', [
            'githubPrErrorLogs' => $githubPrErrorLogs,
        ]);
    }

    public function listCreatedTasks()
    {
        // Set the number of activities per page
        $perPage = 10;
        $page = request('page', 1);

        $githubTaskPullRequests = GithubTaskPullRequest::with('task')
            ->select('*', DB::raw('GROUP_CONCAT(pull_number SEPARATOR ",") as pull_number_concatenated'))
            ->groupBy('task_id')
            ->paginate($perPage, ['*'], 'page', $page);

        // Return the activities to the view
        return View::make('github.list-created-tasks', [
            'githubTaskPullRequests' => $githubTaskPullRequests,
        ]);
    }

    public function repoStatusCheck(Request $request)
    {
        $gitRepo = GithubRepository::find($request->get('repoId'));
        $gitRepo->repo_status = 1;
        $gitRepo->save();

        GithubRepository::whereNotIn('id', [$request->get('repoId')])->update(['repo_status' => 0]);

        $message = 'Repository Status updated successfully.';

        return response()->json([
            'message' => $message,
        ]);
    }

    public function getLatestPullRequests(Request $request)
    {
        $repo = \App\Github\GithubRepository::where('repo_status', '=', 1)->first();

        if ($repo) {
            $repobranches = \App\Github\GithubBranchState::where('repository_id', '=', $repo->id)->take(5)->get();

            $organization = $repo->organization;

            $pullRequests = $this->getPullRequests($organization->username, $organization->token, $repo->id);

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

            return response()->json([
                'tbody' => view('partials.modals.pull-request-alerts-modal-html', compact('pullRequests', 'repo'))->render(),
                'count' => count($repobranches),
            ]);
        }
    }

    public function jobNameStore(Request $request)
    {
        // Validation Part
        $this->validate(
            $request, [
                'job_name' => 'required',
                'organization' => 'required',
                'repository' => 'required',
            ]
        );

        $data = $request->except('_token');

        // Store job name
        $githubRepositoryJob = new GithubRepositoryJob();
        $githubRepositoryJob->github_organization_id = $data['organization'];
        $githubRepositoryJob->github_repository_id = $data['repository'];
        $githubRepositoryJob->job_name = $data['job_name'];
        $githubRepositoryJob->save();

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Job name created successfully!',
            ]
        );
    }

    public function pullRequestActivitiesUpdate(Request $request)
    {
        // Validation Part
        $this->validate(
            $request, [
                'prIds' => 'required',
                'repoId' => 'required',
            ]
        );

        $data = $request->except('_token');

        $repository = GithubRepository::where('id', $data['repoId'])->first();
        $organization = $repository->organization;

        // Set the username and token
        $userName = $organization->username;
        $token = $organization->token;

        // Set the repository owner and name
        $owner = $organization->name;
        $repo = $repository->name;

        foreach ($data['prIds'] as $pullNumber) {
            try {
                // Set the API endpoint for the specific page
                $url = "https://api.github.com/repos/{$owner}/{$repo}/issues/{$pullNumber}/timeline";

                // Send a GET request to the GitHub API
                $githubClient = $this->connectGithubClient($userName, $token);
                $response = $githubClient->get($url);
                $activities = json_decode($response->getBody()->getContents(), true);
                \Log::info(print_r($activities, true));
                if ($activities) {
                    foreach ($activities as $activity) {
                        if (isset($activity['id']) && $activity['event']) {
                            // Check if the event is a "labeled" event and contains label information
                            $labelName = $labelColor = $commentText = '' ;
                            if ($activity['event'] === 'labeled' && isset($activity['label'])) {
                                // Add the label name to the array
                                $labelName = $activity['label']['name'];
                                $labelColor = '#' . $activity['label']['color'];
                            }

                            if ($activity['event'] === 'commented' && isset($activity['body'])) {
                                $commentText = $activity['body'];
                            }

                            $activity_created_at = null;
                            if (isset($activity['created_at'])) {
                                $activity_created_at = $activity['created_at'];
                            }

                            $user = '';
                            if (isset($activity['user'])) {
                                $user = $activity['user']['login'];
                            } elseif (isset($activity['actor'])) {
                                $user = $activity['actor']['login'];
                            }

                            GithubPrActivity::updateOrCreate([
                                'github_organization_id' => $organization->id,
                                'github_repository_id' => $repository->id,
                                'pull_number' => $pullNumber,
                                'activity_id' => $activity['id'],
                            ], [
                                'user' => $user,
                                'event' => $activity['event'],
                                'label_name' => $labelName,
                                'label_color' => $labelColor,
                                'comment_text' => $commentText,
                                'activity_created_at' => $activity_created_at
                            ]);
                        }
                    }

                    // START - If any task assigned for this PR, then send the label mapped message to that task
                    // 1. Get all the labeled activities & mapped message.
                    $activitiesWithLabels = GithubPrActivity::select('github_pr_activities.*', 'github_repository_labels.message AS label_mapped_message')
                        ->leftJoin('github_repository_labels', function ($join) {
                            $join->on('github_pr_activities.github_organization_id', '=', 'github_repository_labels.github_organization_id')
                                ->on('github_pr_activities.github_repository_id', '=', 'github_repository_labels.github_repository_id')
                                ->on('github_pr_activities.label_name', '=', 'github_repository_labels.label_name');
                        })
                        ->where('github_pr_activities.github_organization_id', $organization->id)
                        ->where('github_pr_activities.github_repository_id', $repository->id)
                        ->where('github_pr_activities.pull_number', $pullNumber)
                        ->whereNotNull('github_pr_activities.label_name')
                        ->where('github_pr_activities.label_name', '!=', '')
                        ->whereNotNull('github_repository_labels.message') // Filter out rows with NULL message
                        ->where('github_repository_labels.message', '!=', '') // Filter out rows with empty message
                        ->get();

                    // 2. Check Any task available for this PR
                    if ($activitiesWithLabels->count() > 0) {
                        $createdPrTasks = GithubTaskPullRequest::where('github_organization_id', $organization->id)
                            ->where('github_repository_id', $repository->id)
                            ->where('pull_number', $pullNumber)
                            ->get();

                        if ($createdPrTasks->count() > 0) {
                            foreach ($createdPrTasks as $createdPrTask) {
                                foreach ($activitiesWithLabels as $activitiesWithLabel) {
                                    // 3. Save the label mapped messages for that task
                                    // WhatsAppController private function createTask($data) Getting code from here.
                                    $task = Task::find($createdPrTask->task_id);
                                    $default_user_id = \App\User::USER_ADMIN_ID;
                                    $message = "#{$task->id}. {$task->task_subject}. PR {$pullNumber}. {$activitiesWithLabel->label_mapped_message}";
                                    $params = [
                                        'number' => null,
                                        'user_id' => $default_user_id,
                                        'approved' => 1,
                                        'status' => 1,
                                        'task_id' => $task->id,
                                        'message' => $message,
                                    ];

                                    $user = User::find($task->assign_to);
                                    app(\App\Http\Controllers\WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                                    $chat_message = ChatMessage::create($params);
                                    ChatMessagesQuickData::updateOrCreate([
                                        'model' => \App\Task::class,
                                        'model_id' => $params['task_id'],
                                    ], [
                                        'last_communicated_message' => @$params['message'],
                                        'last_communicated_message_at' => $chat_message->created_at,
                                        'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
                                    ]);
                                }
                            }
                        }
                    }
                    // END - If any task assigned for this PR, then send the label mapped message to that task
                }
            } catch (Exception $e) {
                \Log::error($e);
                $errorArr = [];
                $errorArr = $e->getMessage();
                if (! is_array($errorArr)) {
                    $arrErr[] = $errorArr;
                    $errorArr = implode(' ', $arrErr);
                } else {
                    $arrErr = $errorArr;
                    $errorArr = $errorArr;
                }

                // Save Error Log in DB
                $githubPrErrorLog = new GithubPrErrorLog();
                $githubPrErrorLog->type = GithubPrErrorLog::TYPE_PR_ACTIVITY_TIMELINE;
                $githubPrErrorLog->log = $errorArr;
                $githubPrErrorLog->github_organization_id = $organization->id;
                $githubPrErrorLog->github_repository_id = $repository->id;
                $githubPrErrorLog->pull_number = $pullNumber;
                $githubPrErrorLog->save();

                return "<div class='modal-header'><p><strong>Message:</strong> Something went wrong !</p></div><div class='modal-body'><p><strong>Error:</strong> {$errorArr}</p></div>";
            }
        }

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Activities update successfully!',
            ]
        );
    }

    public function githubTaskStore(Request $request)
    {
        // Validation Part
        $this->validate(
            $request, [
                'task_name' => 'required',
                'task_details' => 'required',
                'selected_rows' => 'required',
                'selected_repo_id' => 'required',
                'assign_to' => 'required',
            ]
        );

        $data = $request->except('_token');

        $repository = GithubRepository::where('id', $data['selected_repo_id'])->first();
        if (! $repository) {
            return response()->json(
                [
                    'code' => 404,
                    'data' => [],
                    'message' => 'Repository not found',
                ]
            );
        }

        $selectedPRs = explode(',', $data['selected_rows']);
        if (! $selectedPRs) {
            return response()->json(
                [
                    'code' => 404,
                    'data' => [],
                    'message' => 'Rows not selected',
                ]
            );
        }

        $organization = $repository->organization;

        // Save Task
        // $githubTask = GithubTask::updateOrCreate([
        //     'task_name' => $data['task_name'],
        //     'assign_to' => $data['assign_to']
        // ]);
        $task = Task::updateOrCreate(
            ['task_subject' => $data['task_name'], 'assign_to' => $data['assign_to']],
            ['assign_from' => Auth::id(), 'is_statutory' => 0, 'task_details' => $data['task_details']]
        );

        if ($task) {
            if ($data['assign_to']) {
                $task->users()->attach([$data['assign_to'] => ['type' => User::class]]);
            }

            // Save task PR's
            foreach ($selectedPRs as $selectedPR) {
                GithubTaskPullRequest::UpdateOrCreate([
                    'github_organization_id' => $organization->id,
                    'github_repository_id' => $repository->id,
                    'pull_number' => $selectedPR,
                    'task_id' => $task->id,
                ]);
            }

            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Task created successfully!',
                ]
            );
        }

        return response()->json(
            [
                'code' => 500,
                'data' => [],
                'message' => 'Error when creating a task',
            ]
        );
    }

    public function syncRepoLabels(Request $request)
    {
        $repo_id = $request->input('repo_id');
        $repository = GithubRepository::where('id', $repo_id)->first();
        $organization = $repository->organization;

        // Set the username and token
        $userName = $organization->username;
        $token = $organization->token;

        // Set the repository owner and name
        $owner = $organization->name;
        $repo = $repository->name;

        // Fetch labels for the specific repository from the GitHub API
        $url = "https://api.github.com/repos/{$owner}/{$repo}/labels";

        // Send a GET request to the GitHub API
        try {
            $githubClient = $this->connectGithubClient($userName, $token);
            $response = $githubClient->get($url);
            $labels = json_decode($response->getBody()->getContents(), true);

            if ($labels) {
                // Save labels in the database
                foreach ($labels as $label) {
                    GithubRepositoryLabel::updateOrCreate(
                        ['label_name' => $label['name'], 'github_organization_id' => $organization->id, 'github_repository_id' => $repository->id],
                        ['label_color' => $label['color']]
                    );
                }

                return response()->json(
                    [
                        'code' => 200,
                        'data' => [],
                        'message' => 'Labels synced successfully!',
                    ]
                );
            } else {
                return response()->json(
                    [
                        'code' => 200,
                        'data' => [],
                        'message' => 'Labels Not Found',
                    ]
                );
            }
        } catch (\Exception $e) {
            // Handle any exceptions or errors
            return response()->json(
                [
                    'code' => 500,
                    'data' => [],
                    'message' => 'Labels sync failed!' . $e->getMessage(),
                ]
            );
        }
    }

    public function listRepoLabels(Request $request)
    {
        $repo_id = $request->get('repo_id');
        // Fetch the saved labels for the specific repository from the database
        $labels = GithubRepositoryLabel::where('github_repository_id', $repo_id)->get();

        return response()->json($labels);
    }

    public function updateRepoLabelMessage(Request $request)
    {
        $labelId = $request->input('label_id');
        $message = $request->input('message');

        $label = GithubRepositoryLabel::findOrFail($labelId);
        $label->message = $message;
        $label->save();

        return response()->json(['message' => 'Label message updated successfully!']);
    }

    public function githubPRAndActivityStore(Request $request)
    {
        try {
            $requestData = $request->all();
            $eventHeader = "";
            if ($request->hasHeader('X-GitHub-Event')) {
                $eventHeader = $request->header('X-GitHub-Event');
            }

            if ($requestData && isset($requestData['action'])) {
                // Find first or Create PR
                $pullNumber = $source = $destination = $mergeable_state = '' ;
                if (isset($requestData['pull_request'])) {
                    $pullNumber = $requestData['pull_request']['number'];
                    $prTitle = $requestData['pull_request']['title'];
                    $prUrl = $requestData['pull_request']['url'];
                    $state = $requestData['pull_request']['state'];
                    $createdBy = $requestData['pull_request']['user']['login'];
                    $body = $requestData['pull_request']['body'];
                    $activityCreatedAt = $requestData['pull_request']['created_at'];
                    $source = $requestData['pull_request']['head']['ref'];
                    $destination = $requestData['pull_request']['base']['ref'];
                    $mergeable_state = $requestData['pull_request']['mergeable_state'];
                } elseif(isset($requestData['issue'])) {
                    $pullNumber = $requestData['issue']['number'];
                    $prTitle = $requestData['issue']['title'];
                    $prUrl = $requestData['issue']['url'];
                    $state = $requestData['issue']['state'];
                    $createdBy = $requestData['issue']['user']['login'];
                    $body = $requestData['issue']['body'];
                    $activityCreatedAt = $requestData['issue']['created_at'];
                }

                if ($pullNumber == "") {
                    return response()->json(['message' => 'GitHub Pull Request Number is missing'], 200);
                }

                $githubPullRequest = GithubPullRequest::updateOrCreate([
                    'pull_number' => $pullNumber,
                    'github_repository_id' => $requestData['repository']['id'],
                ], [
                    'repo_name' => $requestData['repository']['name'],
                    'pr_title' => $prTitle,
                    'pr_url' => $prUrl,
                    'state' => $state,
                    'created_by' => $createdBy,
                    'source' => $source,
                    'destination' => $destination,
                    'mergeable_state' => $mergeable_state
                ]);

                // Create PR activity
                if ($githubPullRequest) {
                    $githubPRActivity = new GithubPrActivity();

                    $githubPRActivity->pull_number = $pullNumber;
                    $githubPRActivity->github_repository_id = $requestData['repository']['id'];
                    $githubPRActivity->event = $requestData['action'];
                    $githubPRActivity->event_header = $eventHeader;

                    if ($requestData['action'] === 'labeled' && isset($requestData['label'])) {
                        // Add the label name to the array
                        $labelName = $requestData['label']['name'];
                        $labelColor = '#' . $requestData['label']['color'];
                        $githubPRActivity->label_name = $labelName;
                        $githubPRActivity->label_color = $labelColor;
                    }

                    if ($eventHeader == 'issue_comment' && $requestData['action'] == 'created' && $body == '') {
                        $body = $requestData['comment']['body'];
                    }

                    $githubPRActivity->body = $body;
                    $githubPRActivity->user = $createdBy;
                    $githubPRActivity->activity_created_at = $activityCreatedAt;
                    $githubPRActivity->save();
                }
            }

            return response()->json(['message' => 'GitHub Pull Request Stored Successfully'], 200);
        } catch (\Exception $e) {
            $errorLog = new GitPullRequestErrorLog();   
            $errorLog->github_repository_id = $requestData['repository']['id'];
            $errorLog->pull_number = $pullNumber;
            $errorLog->type = "pull request";
            $errorLog->error_message = $e->getMessage();
            Log::channel('github_error')->error($e->getMessage());

            return response()->json(['message' => 'An error occurred. Please check the logs.'], 500);
        }
    }

    public function listAllNewPullRequests(Request $request)
    {
        $projects = Project::get();
       
        $perPage = 20; // Number of items per page
    
        $repo_names = GithubPullRequest::distinct()->pluck('repo_name');
        $users = GithubPullRequest::distinct()->pluck('created_by');

        if ($request->ajax()) {
            ini_set('max_execution_time', -1);
            $allPullRequests = [];
    
        if ($request->repoId !== null) {
            $pullRequests = GithubPullRequest::where('github_repository_id', $request->repoId)->get();
        } else {
            $pullRequests = GithubPullRequest::get();
        }
    
        foreach ($pullRequests as $key => $pullRequest) {
            $getRepo = GithubRepository::find($request->repoId); 
            $organizationId = $getRepo->github_organization_id ?? '';

            $pullRequests[$key]['mergeable_state'] = $pullRequest->mergeable_state;
            $pullRequests[$key]['conflict_exist'] = $pullRequest->mergeable_state == 'dirty' ? true : false;
            // Get Latest Activity for this PR
            $pullRequests[$key]['latest_activity'] = [];
            $latestGithubPrActivity = GithubPrActivity::latest('activity_id')
                ->where('github_organization_id', $organizationId)
                ->where('github_repository_id', $request->repoId) 
                ->where('pull_number', $pullRequest->pull_number)
                ->first();
            if ($latestGithubPrActivity) {
                $pullRequests[$key]['latest_activity'] = [
                    'activity_id' => $latestGithubPrActivity->activity_id,
                    'user' => $latestGithubPrActivity->user,
                    'event' => $latestGithubPrActivity->event,
                    'label_name' => $latestGithubPrActivity->label_name,
                    'label_color' => $latestGithubPrActivity->label_color,
                ];
            }

            // Check build process logs
            $totalBuildProcessHistoryCount = BuildProcessHistory::where('github_organization_id', $organizationId)
                ->where('github_repository_id', $request->repoId)
                ->where('github_branch_state_name', $pullRequest->source)
                ->count();

            $totalBuildProcessSuccessHistoryCount = BuildProcessHistory::where('github_organization_id', $organizationId)
                ->where('github_repository_id', $request->repoId)
                ->where('github_branch_state_name', $pullRequest->source)
                ->where('status', 'SUCCESS')
                ->count();

            $pullRequests[$key]['build_process_history_status'] = '';
            if ($totalBuildProcessHistoryCount > 0) {
                if ($totalBuildProcessHistoryCount == $totalBuildProcessSuccessHistoryCount) {
                    $pullRequests[$key]['build_process_history_status'] = 'Success';
                } else {
                    $pullRequests[$key]['build_process_history_status'] = 'Danger';
                }
            }
            
            // Add the pull request key to the array
            $pullRequests[$key]['pull_request_key'] = 'Your Pull Request Key Data Here';
            
            // Add build process data to the array
            $pullRequests[$key]['build_process_data'] = 'Your Build Process Data Here';
        }
    
            $pullRequests = $pullRequests->toArray();

            $currentPage = request()->query('page', 1);
            $start = ($currentPage - 1) * $perPage;
            $slicedData = array_slice($pullRequests, $start, $perPage);
    
            $githubPullPaginated = new LengthAwarePaginator(
                $slicedData,
                count($pullRequests),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
            // Preserve existing query parameters
            $pullRequests = $slicedData;
    
            $allPullRequests = array_merge($allPullRequests, $pullRequests);
            $githubPullPaginated->appends(request()->query());
            $githubPullPaginated->withPath(Paginator::resolveCurrentPath());
            $links = $githubPullPaginated->links();

            return response()->json([
                'tbody' => view('github.include.new-pull-request-list', compact(['pullRequests', 'projects', 'githubPullPaginated', 'links']))->render(),
                'count' => count($pullRequests),
                'links' => $links,
            ], 200);
        }

        $githubOrganizations = GithubOrganization::with('repos')->get();

        return view('github.include.new_all_pull_resquest', compact('githubOrganizations','projects','repo_names','users'));
     }

    public function listAllNewPrActivities(request $request)
    {
        $prActivities = new GithubPrActivity();

        $orgs = GithubOrganization::distinct()->pluck('name','id');
        $repos = GithubRepository::distinct()->pluck('name','id');
        $users = GithubPrActivity::distinct()->pluck('user');
        $events = GithubPrActivity::distinct()->pluck('event');
        $eventHeaders = GithubPrActivity::distinct()->pluck('event_header');
        $labelNames = GithubPrActivity::distinct()->pluck('label_name');
        $pullNumbers = GithubPrActivity::distinct()->pluck('pull_number');

        $repositories = \App\Github\GithubRepository::All();
        $organizations = \App\Github\GithubOrganization::All();
        $projects = \App\Models\Project::All();
        $branches = \App\Github\GithubBranchState::All();


        if ($request->org) {
            $prActivities = $prActivities->WhereIn('github_organization_id', $request->org);
        }
        if ($request->user) {
            $prActivities = $prActivities->WhereIn('user', $request->user);
        }
        if ($request->repo) {
            $prActivities = $prActivities->WhereIn('github_repository_id', $request->repo);
        }
        if ($request->pull_number) {
            $prActivities = $prActivities->WhereIn('pull_number', $request->pull_number);
        }
        if ($request->event) {
            $prActivities = $prActivities->whereIn('event', $request->event);
        }
        if ($request->event_header) {
            $prActivities = $prActivities->whereIn('event_header', $request->event_header);
        }
        if ($request->label_name) {
            $prActivities = $prActivities->whereIn('label_name', $request->label_name);
        }
        if ($request->description) {
            $prActivities = $prActivities->where('description', 'LIKE', '%' . $request->description . '%');
        }
        if ($request->body) {
            $prActivities = $prActivities->where('body', 'LIKE', '%' . $request->body . '%');
        }
        if ($request->activity_date) {
            $prActivities = $prActivities->where('activity_created_at', 'LIKE', '%' . $request->activity_date . '%');
        }
        if ($request->date) {
            $prActivities = $prActivities->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        $prActivities = $prActivities->latest()->paginate(\App\Setting::get('pagination', 25));

        return view('github.include.pr-activities-list', compact('prActivities','orgs','repos','users','events','eventHeaders','labelNames','pullNumbers','organizations','repositories','projects','branches'));
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    public function githubAddToken(Request $request)
    {
        // Validation Part
        $this->validate(
            $request, [
                'github_repositories_id' => 'required',
                'github_type' => 'required',
                'token_key' => 'required',
                'expiry_date' => 'required',
            ]
        );

        $data = $request->except('_token');

        $repository = GithubRepository::where('id', $data['github_repositories_id'])->first();
        if (! $repository) {
            return response()->json(
                [
                    'code' => 404,
                    'data' => [],
                    'message' => 'Repository not found',
                ]
            );
        }


        $GithubToken = GithubToken::where('github_repositories_id', $data['github_repositories_id'])->first();
        if (! $GithubToken) {
            $GithubToken = new GithubToken();
            $GithubToken->github_repositories_id = $data['github_repositories_id'];
        }
        $GithubToken->created_by = auth()->user()->id;
        $GithubToken->github_type = $data['github_type'];
        $GithubToken->token_key = $data['token_key'];
        $GithubToken->expiry_date = $data['expiry_date'];
        $GithubToken->save();
        
        if ($GithubToken) {
            
            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Token updated successfully!',
                ]
            );
        }

        return response()->json(
            [
                'code' => 500,
                'data' => [],
                'message' => 'Error when creating a token',
            ]
        );
    }

    public function githubTokenHistory($id)
    {
        $datas = GithubToken::with('user', 'githubrepository')
            ->where('github_repositories_id', $id)
            ->orderBy('created_at', 'DESC')
            ->take(10)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getRepositoryDara(Request $request)
    {
        $GithubToken = GithubToken::where('github_repositories_id', $request->repo_id)->first();
        
        return response()->json([
            'status' => true,
            'data' => $GithubToken,
            'message' => 'repository get successfully',
            'status_name' => 'success',
        ], 200);
    }
}
