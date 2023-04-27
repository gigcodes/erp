<?php

namespace App\Console\Commands;

use App\Github\GitHubBranchState;
use App\Github\GithubOrganization;
use App\Helpers\GithubTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class LoadBranchState extends Command
{
    use GithubTrait;

    // private $githubClient;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'github:load_branch_state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets state of all branch for all repositories';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // $this->githubClient = new Client([
        //     // 'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')],
        //     'auth' => [config('env.GITHUB_USERNAME'), config('env.GITHUB_TOKEN')],
        // ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $report = \App\CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $organizations = GithubOrganization::get();
            
            foreach($organizations as $organization){
                $organizationId = $organization->name;
                $userName = $organization->username;
                $token = $organization->token;
                
                $repositoryIds = $this->getAllRepositoriesIds($organizationId, $userName, $token);

                $repoBranches = [];
                foreach ($repositoryIds as $repoId) {
                    $branchNames = $this->getBranchNamesOfRepository($userName, $token, $repoId);

                    if (count($branchNames) > 0) {
                        $repoBranches[$repoId] = $branchNames;
                    }
                }

                $comparisons = [];
                foreach ($repoBranches as $repoId => $branches) {
                    foreach ($branches as $branch) {
                        $comparison = $this->compareRepoBranches($userName, $token, $repoId, $branch);
                        $comparisons[$repoId][$branch] = $comparison;
                    }
                }

                foreach ($comparisons as $repoId => $branches) {
                    $branchNames = [];
                    foreach ($branches as $branchName => $comparison) {
                        GithubBranchState::updateOrCreate(
                            [
                                'repository_id' => $repoId,
                                'branch_name' => $branchName,
                            ],
                            [
                                'github_organization_id' => $organizationId,
                                'repository_id' => $repoId,
                                'branch_name' => $branchName,
                                'ahead_by' => $comparison['ahead_by'],
                                'behind_by' => $comparison['behind_by'],
                                'last_commit_author_username' => $comparison['last_commit_author_username'],
                                'last_commit_time' => $comparison['last_commit_time'],
                            ]
                        );
                        $branchNames[] = $branchName;
                    }
                    GithubBranchState::where('repository_id', $repoId)
                        ->whereNotIn('branch_name', $branchNames)
                        ->delete();
                }
            }
            
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }

        //echo print_r($comparisons, true);
    }

    private function connectGithubClient($userName, $token)
    {
        $githubClient = new Client([
                // 'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')],
                'auth' => [$userName, $token],
            ]);

        return $githubClient;
    }

    private function getAllRepositoriesIds($organizationId, $userName, $token)
    {
        $repositories = [];

        try{
            //https://api.github.com/orgs/ludxb/repos
            // $url      = 'https://api.github.com/orgs/' . getenv('GITHUB_ORG_ID') . '/repos';
            $url = 'https://api.github.com/orgs/'.$organizationId.'/repos';

            $githubClient = $this->connectGithubClient($userName, $token);

            $response = $githubClient->get($url);

            $repositories = json_decode($response->getBody()->getContents());

            return array_map(
                function ($repository) {
                    return $repository->id;
                },
                $repositories
            );
        }
        catch(\Exception $e){
            //
        }

        return $repositories;
    }

    private function getBranchNamesOfRepository($userName, $token, int $repoId)
    {
        //https://api.github.com/repositories/:repoId/branches

        $url = 'https://api.github.com/repositories/'.$repoId.'/branches';

        $githubClient = $this->connectGithubClient($userName, $token);

        $headResponse = $githubClient->head($url);

        $linkHeader = $headResponse->getHeader('Link');
        /**
         * <https://api.github.com/repositories/231925646/branches?page=4>; rel="prev", <https://api.github.com/repositories/231925646/branches?page=4>; rel="last", <https://api.github.com/repositories/231925646/branches?page=1>; rel="first"
         */
        $totalPages = 1;
        if (count($linkHeader) > 0) {
            $lastLink = null;
            $links = explode(',', $linkHeader[0]);
            foreach ($links as $link) {
                if (strpos($link, 'rel="last"') !== false) {
                    $lastLink = $link;
                    break;
                }
            }

            //<https://api.github.com/repositories/231925646/branches?page=4>; rel="last"
            $linkWithAngularBrackets = explode(';', $lastLink)[0];
            //<https://api.github.com/repositories/231925646/branches?page=4>
            $linkWithAngularBrackets = str_replace('<', '', $linkWithAngularBrackets);
            //https://api.github.com/repositories/231925646/branches?page=4>
            $linkWithPageNumber = str_replace('>', '', $linkWithAngularBrackets);
            //https://api.github.com/repositories/231925646/branches?page=4
            $pageNumberString = explode('?', $linkWithPageNumber)[1];
            //page=4
            $totalPages = explode('=', $pageNumberString)[1];

            $totalPages = intval($totalPages);
        }

        $allBranchNames = [];
        $page = 1;
        while ($page <= $totalPages) {
            $response = $githubClient->get($url.'?page='.$page);

            $branches = json_decode($response->getBody()->getContents());

            $branchNames = array_map(
                function ($branch) {
                    return $branch->name;
                },
                $branches
            );

            $allBranchNames = array_merge(
                $allBranchNames,
                array_filter($branchNames, function ($name) {
                    return $name != 'master';
                })
            );

            $page++;
        }

        return $allBranchNames;
    }
}
