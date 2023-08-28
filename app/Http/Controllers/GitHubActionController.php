<?php

namespace App\Http\Controllers;

use App\Models\GitHubAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitHubActionController extends Controller
{
    public function store(Request $request)
    {

        try {    
            $githubAction = new GitHubAction();

            $githubAction->github_actor = $request->input('GITHUB_ACTOR') ?? "";
            $githubAction->github_api_url = $request->input('GITHUB_API_URL') ?? "";
            $githubAction->github_base_ref = $request->input('GITHUB_BASE_REF') ?? "" ;
            $githubAction->github_event_name = $request->input('GITHUB_EVENT_NAME')?? "" ;
            $githubAction->github_job = $request->input('GITHUB_JOB')?? "" ;
            $githubAction->github_ref = $request->input('GITHUB_REF')?? "" ;
            $githubAction->github_ref_name = $request->input('GITHUB_REF_NAME')?? "" ;
            $githubAction->github_ref_type = $request->input('GITHUB_REF_TYPE')?? "" ;
            $githubAction->github_repository = $request->input('GITHUB_REPOSITORY')?? "" ;
            $githubAction->github_repository_id = $request->input('GITHUB_REPOSITORY_ID')?? "" ;
            $githubAction->github_run_attempt = $request->input('GITHUB_RUN_ATTEMPT')?? "" ;
            $githubAction->github_run_id = $request->input('GITHUB_RUN_ID')?? "" ;
            $githubAction->github_workflow = $request->input('GITHUB_WORKFLOW')?? "" ;
            $githubAction->runner_name = $request->input('RUNNER_NAME')?? "" ;
            $githubAction->save();

            return response()->json(['message' => 'GitHub Action Stored Successfully'], 200);
        } catch (\Exception $e) {
            Log::channel('github_error')->error($e->getMessage());

            return response()->json(['message' => 'An error occurred. Please check the logs.'], 500);
        }
    }
}
