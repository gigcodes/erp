<?php

namespace App\Http\Controllers;

use App\Models\GitHubAction;
use Illuminate\Http\Request;

class GitHubActionController extends Controller
{
    public function store(Request $request)
    {
        $githubAction = new GitHubAction();

        $githubAction->github_actor = $request->input('github_actor');
        $githubAction->github_api_url = $request->input('github_api_url');
        $githubAction->github_base_ref = $request->input('github_base_ref');
        $githubAction->github_event_name = $request->input('github_event_name');
        $githubAction->github_job = $request->input('github_job');
        $githubAction->github_ref = $request->input('github_ref');
        $githubAction->github_ref_name = $request->input('github_ref_name');
        $githubAction->github_ref_type = $request->input('github_ref_type');
        $githubAction->github_repository = $request->input('github_repository');
        $githubAction->github_repository_id = $request->input('github_repository_id');
        $githubAction->github_run_attempt = $request->input('github_run_attempt');
        $githubAction->github_run_id = $request->input('github_run_id');
        $githubAction->github_workflow = $request->input('github_workflow');
        $githubAction->runner_name = $request->input('runner_name');
        $githubAction->save();

        return response()->json(['message' => 'GitHub Action Stored Successfully'], 200);
    }
}
