<?php

namespace App\Console\Commands;

use App\Github\GithubBranchState;
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
        foreach($repositoryIds as $repoId){
            $branchNames = $this->getBranchNamesOfRepository($repoId);
            if(sizeof($branchNames) > 0){
                $repoBranches[$repoId] = $branchNames;
            }
        }

        $comparisons = [];
        foreach($repoBranches as $repoId => $branches){
            foreach($branches as $branch){
                $comparison = $this->compareRepoBranches($repoId, $branch);
                $comparisons[$repoId][$branch] = $comparison;
            }
        }

        foreach($comparisons as $repoId => $branches){
            $branchNames = [];
            foreach($branches as $branchName => $comparison){
                GithubBranchState::updateOrCreate(
                    [
                        'repository_id' => $repoId,
                        'branch_name' => $branchName
                    ],
                    [
                        'repository_id' => $repoId,
                        'branch_name' => $branchName,
                        'ahead_by' => $comparison['ahead_by'],
                        'behind_by' => $comparison['behind_by']
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

    private function getBranchNamesOfRepository(int $repoId){
        //https://api.github.com/repositories/:repoId/branches
        $url = 'https://api.github.com/repositories/'.$repoId.'/branches';
        $response = $this->githubClient->get($url);

        $branches = json_decode($response->getBody()->getContents());

        $branchNames =  array_map(
            function($branch){
                return $branch->name;
            },
            $branches
        );

        return array_filter($branchNames, function($name){
            return $name != 'master';
        });
    }

    private function compareRepoBranches(int $repoId, string $branchName, string $base='master'){
        //https://api.github.com/repositories/:repoId/compare/:diff

        $url = 'https://api.github.com/repositories/'.$repoId.'/compare/'.$base.'...'.$branchName;
        $response = $this->githubClient->get($url);

        $compare = json_decode($response->getBody()->getContents());

        return [
            'ahead_by' => $compare->ahead_by,
            'behind_by' => $compare->behind_by
        ];

    }
}
