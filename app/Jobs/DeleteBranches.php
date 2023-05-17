<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\DeletedGithubBranchLog;
use App\Helpers\GithubTrait;
use App\Github\GithubBranchState;

class DeleteBranches implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,GithubTrait;

    private $branches;
    private $error;
    private $response;
    private $repositoryId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($branches,$repositoryId)
    {
        $this->branches = $branches;
        $this->repositoryId = $repositoryId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->branches as $branch){
            $this->response = $this->deleteBranch($this->repositoryId, $branch);

            if($this->response['status']){
                \Log::info('Branch ' .$branch.' has been deleted successfully.');
                DeletedGithubBranchLog::create([
                    'branch_name' => $branch,
                    'repository_id' => $this->repositoryId,
                    'deleted_by'    => \Auth::id(),
                    'status'    => 'success'
                ]);
            }

            $githubBranchState = GithubBranchState::where('repository_id', $this->repositoryId)->where('branch_name', $branch)->first();
            if (! empty($githubBranchState) && $this->response['status']) {
                $githubBranchState->delete();
            }else{
                \Log::info('ERROR : while delete the branch - ' .$branch);
                DeletedGithubBranchLog::create([
                    'branch_name' => $branch,
                    'repository_id' => $this->repositoryId,
                    'deleted_by'    => \Auth::id(),
                    'status'    => 'failed',
                    'error_message' => $this->response['error']
                ]);
                $this->error .= $this->response['error'] .",";
            }

        }

        return $this->response;
    }
}
