<?php

namespace App\Console\Commands;

use App\Github\GithubBranchState;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class LoadBranchState extends Command
{
    private $githubClient;
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
        $this->githubClient = new Client([
            'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $repositoryIds = $this->getAllRepositoriesIds();

        $repoBranches = [];
        foreach ($repositoryIds as $repoId) {
            $branchNames = $this->getBranchNamesOfRepository($repoId);
            if (sizeof($branchNames) > 0) {
                $repoBranches[$repoId] = $branchNames;
            }
        }

        $comparisons = [];
        foreach ($repoBranches as $repoId => $branches) {
            foreach ($branches as $branch) {
                $comparison = $this->compareRepoBranches($repoId, $branch);
                $comparisons[$repoId][$branch] = $comparison;
            }
        }

        foreach ($comparisons as $repoId => $branches) {
            $branchNames = [];
            foreach ($branches as $branchName => $comparison) {
                GithubBranchState::updateOrCreate(
                    [
                        'repository_id' => $repoId,
                        'branch_name' => $branchName
                    ],
                    [
                        'repository_id' => $repoId,
                        'branch_name' => $branchName,
                        'ahead_by' => $comparison['ahead_by'],
                        'behind_by' => $comparison['behind_by'],
                        'last_commit_author_username' => $comparison['last_commit_author_username'],
                        'last_commit_time' => $comparison['last_commit_time']
                    ]
                );
                $branchNames[] = $branchName;
            }
            GithubBranchState
                ::where('repository_id', $repoId)
                ->whereNotIn('branch_name', $branchNames)
                ->delete();
        }

        //echo print_r($comparisons, true);
    }

    private function getAllRepositoriesIds()
    {
        //https://api.github.com/orgs/ludxb/repos
        $url = 'https://api.github.com/orgs/' . getenv('GITHUB_ORG_ID') . '/repos';
        $response = $this->githubClient->get($url);

        $repositories = json_decode($response->getBody()->getContents());

        return array_map(
            function ($repository) {
                return $repository->id;
            },
            $repositories
        );
    }

    private function getBranchNamesOfRepository(int $repoId)
    {
        //https://api.github.com/repositories/:repoId/branches

        $url = 'https://api.github.com/repositories/' . $repoId . '/branches';

        $headResponse = $this->githubClient->head($url);

        $linkHeader = $headResponse->getHeader("Link");
        /**
         * <https://api.github.com/repositories/231925646/branches?page=4>; rel="prev", <https://api.github.com/repositories/231925646/branches?page=4>; rel="last", <https://api.github.com/repositories/231925646/branches?page=1>; rel="first"
         */
        $lastLink = null;
        $links = explode(',', $linkHeader);
        foreach ($links as $link) {
            if (strpos($link, 'rel="last"') !== false) {
                $lastLink = $link;
                break;
            }
        }
        //<https://api.github.com/repositories/231925646/branches?page=4>; rel="last"
        $linkWithAngularBrackets = explode(";", $lastLink)[0];
        //<https://api.github.com/repositories/231925646/branches?page=4>
        $linkWithAngularBrackets = str_replace('<', '', $linkWithAngularBrackets);
        //https://api.github.com/repositories/231925646/branches?page=4>
        $linkWithPageNumber = str_replace('>', '', $linkWithAngularBrackets);
        //https://api.github.com/repositories/231925646/branches?page=4
        $totalPages = intval(explode('=',  explode('?', $linkWithPageNumber)[1]));

        $branches = [];
        $page = 1;
        while ($page <= $totalPages) {
            $response = $this->githubClient->get($url . '?page=' . $page);

            $branches = json_decode($response->getBody()->getContents());

            $branchNames =  array_map(
                function ($branch) {
                    return $branch->name;
                },
                $branches
            );

            $branches = array_merge(
                $branches,
                array_filter($branchNames, function ($name) {
                    return $name != 'master';
                })
            );

            $page++;
        }

        return $branches;
    }

    private function compareRepoBranches(int $repoId, string $branchName, string $base = 'master')
    {
        //https://api.github.com/repositories/:repoId/compare/:diff

        $url = 'https://api.github.com/repositories/' . $repoId . '/compare/' . $base . '...' . $branchName;
        $response = $this->githubClient->get($url);

        $compare = json_decode($response->getBody()->getContents());

        $lastCommitAuthorUsername = null;
        $lastCommitTime = null;


        if (is_array($compare->commits) && sizeof($compare->commits) > 0) {
            $index = sizeof($compare->commits) - 1;

            try {
                $lastCommitAuthorUsername = $compare->commits[$index]->author->login;
            } catch (Exception $e) {
                // do nothing
                $lastCommitAuthorUsername = $compare->commits[$index]->commit->author->name;
            }
            $lastCommitTime = Carbon::parse($compare->commits[$index]->commit->author->date);
        } else {
            $lastCommitAuthorUsername = $compare->merge_base_commit->commit->author->name;
            $lastCommitTime = Carbon::parse($compare->merge_base_commit->commit->author->date);
        }


        return [
            'ahead_by' => $compare->ahead_by,
            'behind_by' => $compare->behind_by,
            'last_commit_author_username' => $lastCommitAuthorUsername,
            'last_commit_time' => $lastCommitTime
        ];
    }
}
