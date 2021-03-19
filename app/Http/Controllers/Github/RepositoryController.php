<?php

namespace App\Http\Controllers\Github;

use App\DeveloperTask;
use App\Github\GithubBranchState;
use App\Github\GithubRepository;
use App\Helpers\githubTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Artisan;
use Carbon\Carbon;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Input;

class RepositoryController extends Controller
{

    use githubTrait;

    private $client;

    function __construct()
    {
        $this->client = new Client([
            'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
        ]);
    }

    private function refreshGithubRepos()
    {
        $url = "https://api.github.com/orgs/" . getenv('GITHUB_ORG_ID') . "/repos?per_page=100";
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
                'updated_at' => Carbon::createFromFormat(DateTime::ISO8601, $repository->updated_at)
            ];

            GithubRepository::updateOrCreate(
                [
                    'id' => $repository->id
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
            'repositories' => $repositories
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
            'current_branch' => $currentBranch
        ]);

        //print_r($repository);
    }

    public function deployBranch($repoId)
    {
        $source = 'master';
        $destination = Input::get('branch');

        $url = "https://api.github.com/repositories/" . $repoId . "/merges";

        try {
            // Merge master into branch
            $this->client->post(
                $url,
                [
                    RequestOptions::BODY => json_encode([
                        'base' => $destination,
                        'head' => $source,
                    ])
                ]
            );
            echo 'done';
            //Artisan::call('github:load_branch_state');
            if($source == 'master'){
                $this->updateBranchState($repoId, $destination);
            }else if($destination == 'master'){
                $this->updateBranchState($repoId, $source);
            }

            // Deploy branch
            $repository = GithubRepository::find($repoId);

            $branch = Input::get('branch');
            $composerupdate = request("composer",false);
            //echo 'sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh '.$branch;

            $cmd = 'sh ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . $repository->name . '/deploy_branch.sh ' . $branch . ' '.$composerupdate. ' 2>&1';
            //echo 'sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh '.$branch;

            $allOutput = array();
            $allOutput[] = $cmd;
            $result = exec($cmd, $allOutput);

        } catch (Exception $e) {
            print_r($e->getMessage());
            return redirect(url('/github/pullRequests'))->with(
                [
                    'message' => 'Failed to Merge!',
                    'alert-type' => 'error'
                ]
            );
        }

        return redirect(url('/github/pullRequests'))->with([
            'message' => print_r($allOutput, true),
            'alert-type' => 'success'
        ]);
    }

    private function updateBranchState($repoId, $branchName){
        $comparison = $this->compareRepoBranches($repoId, $branchName);

        GithubBranchState::updateOrCreate(
            [
                'repository_id' => $repoId,
                'branch_name'   => $branchName,
            ],
            [
                'repository_id'               => $repoId,
                'branch_name'                 => $branchName,
                'ahead_by'                    => $comparison['ahead_by'],
                'behind_by'                   => $comparison['behind_by'],
                'last_commit_author_username' => $comparison['last_commit_author_username'],
                'last_commit_time'            => $comparison['last_commit_time'],
            ]
        );
    }

    private function updateDevTask($branchName){
        $devTaskId = null;
        $usIt = explode($branchName, '-');

        if (count($usIt) > 1) {
            $devTaskId = $usIt[1];
        }

        $devTask = DeveloperTask::find($devTaskId);

        if ($devTask) {
            $devTask->status = 'In Review';
            $devTask->save();
        }
    }

    public function mergeBranch($id)
    {
        $source = Input::get('source');
        $destination = Input::get('destination');

        $url = "https://api.github.com/repositories/" . $id . "/merges";

        try {
            $this->client->post(
                $url,
                [
                    RequestOptions::BODY => json_encode([
                        'base' => $destination,
                        'head' => $source,
                    ])
                ]
            );
            echo 'done';
            //Artisan::call('github:load_branch_state');
            if ($source == 'master') {
                $this->updateBranchState($id, $destination);
            } else if ($destination == 'master') {
                $this->updateBranchState($id, $source);
            }

            $this->updateDevTask($source);

            // Deploy branch
            $repository = GithubRepository::find($id);

            $branch = 'master';
            //echo 'sh '.getenv('DEPLOYMENT_SCRIPTS_PATH').'erp/deploy_branch.sh '.$branch;

            $cmd = 'sh ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . $repository->name . '/deploy_branch.sh ' . $branch . ' 2>&1';

            $allOutput = array();
            $allOutput[] = $cmd;
            $result = exec($cmd, $allOutput);

        } catch (Exception $e) {
            \Log::error($e);
            print_r($e->getMessage());
            return redirect(url('/github/pullRequests'))->with(
                [
                    'message' => 'Failed to Merge!',
                    'alert-type' => 'error'
                ]
            );
        }
        return redirect(url('/github/pullRequests'))->with([
            'message' => 'Branch merged successfully',
            'alert-type' => 'success'
        ]);
    }

    private function getPullRequests($repoId)
    {
        $pullRequests = array();
        $url = "https://api.github.com/repositories/" . $repoId . "/pulls";
        try{
            $response = $this->client->get($url);
            $decodedJson = json_decode($response->getBody()->getContents());
            foreach ($decodedJson as $pullRequest) {
                $pullRequests[] = array(
                    'id' => $pullRequest->number,
                    'title' => $pullRequest->title,
                    'number' => $pullRequest->number,
                    'username' => $pullRequest->user->login,
                    'userId' => $pullRequest->user->id,
                    'updated_at' => $pullRequest->updated_at,
                    'source' => $pullRequest->head->ref,
                    'destination' => $pullRequest->base->ref
                );
            }
        }catch(Exception $e) {

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
            'repository' => $repository
        ]);
    }

    public function listAllPullRequests()
    {
        $repositories = GithubRepository::all(['id', 'name']);

        $allPullRequests = [];
        foreach ($repositories as $repository) {
            $pullRequests = $this->getPullRequests($repository->id);

            $pullRequests = array_map(
                function($pullRequest) use($repository){
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
                'pullRequests' =>  $allPullRequests
            ]
        );
    }

    function deployNodeScrapers(){
        return $this->getRepositoryDetails(231924853);
    }
}
