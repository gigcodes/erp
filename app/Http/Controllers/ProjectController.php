<?php

namespace App\Http\Controllers;

use App\BuildProcessErrorLog;
use App\Models\Project;
use App\Models\ProjectServerenv;
use App\Models\ProjectType;
use App\StoreWebsite;
use App\BuildProcessStatusHistories;
use Illuminate\Http\Request;
use App\Helpers\GithubTrait;

class ProjectController extends Controller
{
    use GithubTrait;
    
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $projects = Project::latest();

        if ($request->keyword) {
            $projects = $projects->where('name', 'LIKE', '%' . $request->keyword . '%');
        }

        if ($searchStoreWebsite = $request->store_websites_search) {
            $projects->whereHas('storeWebsites', function ($query) use ($searchStoreWebsite) {
                $query->whereIn("store_website_id", $searchStoreWebsite);
            });
        }

        $projects = $projects->paginate(10);

        $store_websites = StoreWebsite::get()->pluck('title', 'id');
        $serverenvs = ProjectServerenv::get()->pluck('name', 'id');
        $projecttype = ProjectType::get()->pluck('name', 'id');
        $repositories = \App\Github\GithubRepository::All();
        $organizations = \App\Github\GithubOrganization::All();
        
        return view('project.index', compact('projects', 'store_websites','repositories', 'organizations', 'serverenvs','projecttype'));
    }

    public function getGithubRepos()
    {
        if ($build_organization = request('build_organization')) {
            $repositories = \App\Github\GithubRepository::where('github_organization_id', $build_organization)->orderBy('created_at', 'desc')->get()->pluck('name', 'id')->toArray();  
            if ($repositories) {
                $options = ['<option value="" >--  Select a Repository --</option>'];
                foreach ($repositories as $key => $value) {
                    $options[] = '<option value="' . $key . '" ' . ($key == request('selected') ? 'selected' : '') . ' >' . $value . '</option>';
                }
            } else {
                $options = ['<option value="" >No records found.</option>'];
            }
        } else {
            $options = ['<option value="" >Please Select a Organizations</option>'];
        }

        return response()->json(['data' => implode('', $options)]);
    }

    public function getGithubBranches()
    {
        if ($build_repository = request('build_repository')) {
            \Log::info("Project getGithubBranches");
            $allBranchNames = [];
            try {
                $repository = \App\Github\GithubRepository::find($build_repository);
                $organization = $repository->organization;

                $githubClient = $this->connectGithubClient($organization->username, $organization->token);
                
                $url = 'https://api.github.com/repositories/'.$build_repository.'/branches';
                $headResponse = $githubClient->head($url);
                $linkHeader = $headResponse->getHeader('Link');
                
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
                    $linkWithAngularBrackets = explode(';', $lastLink)[0];
                    $linkWithAngularBrackets = str_replace('<', '', $linkWithAngularBrackets);
                    $linkWithPageNumber = str_replace('>', '', $linkWithAngularBrackets);
                    $pageNumberString = explode('?', $linkWithPageNumber)[1];
                    $totalPages = explode('=', $pageNumberString)[1];
                    $totalPages = intval($totalPages);
                }
                \Log::info("totalPages : ".$totalPages);

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
                if (!empty($allBranchNames)) {
               
                    $options = ['<option value="" >--  Select a Branch --</option>'];
                    foreach ($allBranchNames as $key => $value) {
                        $options[] = '<option value="' . $value . '">' . $value . '</option>';
                    }
                } else {
                    $options = ['<option value="" >No records found.</option>'];
                }
            }
            catch(\Exception $e) {
                \Log::info("Error : ".$e->getMessage());
                $options = ['<option value="" >Please Select a Repository</option>'];
            }
           
        } else {
            $options = ['<option value="" >Please Select a Repository</option>'];
        }

        return response()->json(['data' => implode('', $options)]);
    }

    public function pullRequestsBuildProcess(Request $request){
        
        $repository_id =$request->build_process_repository;
        $branch_name = $request->build_process_branch;
        $projects=$request->projects;
        if(auth()->user()){
            $user_id=auth()->user()->id;
        }else{
            $user_id=6;
        }
        if($repository_id==''){
            BuildProcessErrorLog::log([
                'project_id' => "",
                'error_message' => 'Repository data can not be empty!',
                'error_code' => "500",
                'github_organization_id' => "",
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => ""
            ]);

            return response()->json(['code' => 500, 'message' => 'Repository data can not be empty!']);
        }
        $repositoryData = \App\Github\GithubRepository::find($repository_id);
        if(!$repositoryData){
            BuildProcessErrorLog::log([
                'project_id' => "",
                'error_message' => 'Repository data not found!',
                'error_code' => "500",
                'github_organization_id' => "",
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => ""
            ]);

            return response()->json(['code' => 500, 'message' => 'Repository data not found!']);
        }
        if($branch_name==''){
            BuildProcessErrorLog::log([
                'project_id' => "",
                'error_message' => 'Branch data can not be empty!',
                'error_code' => "500",
                'github_organization_id' => "",
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => $branch_name
            ]);

            return response()->json(['code' => 500, 'message' => 'Branch data can not be empty!']);
        }
        $initiate_from="Pull Requests Page";
        if($request->has("project_type") && $request->project_type!=''){
            $initiate_from="Call Back URL";
            $project_type=$request->project_type;
            $projects=Project::where('project_type',$project_type)->get()->pluck('id')->toArray();
        }
        $build_pr='';
        if($request->has("build_pr") && $request->build_pr!=''){
            $build_pr=$request->build_pr;
        }
        if(empty($projects)){
            BuildProcessErrorLog::log([
                'project_id' => "",
                'error_message' => 'Please select projects for build process!',
                'error_code' => "500",
                'github_organization_id' => "",
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => $branch_name
            ]);

            return response()->json(['code' => 500, 'message' => 'Please select projects for build process!']);
        }
        
        $repository = $repositoryData->name;
        $organization = $repositoryData->github_organization_id;
        foreach($projects as $proj){
            $project = Project::find($proj);
            if($project){
                $job_name = $project->job_name;
                $serverenv = $project->serverenv;
                if($job_name=='' || $serverenv==''){
                    $record = [
                        'store_website_id' => $proj, 
                        'created_by' =>$user_id, 
                        'text' => 'Job name and serverenv can not be empty!', 
                        'build_name' => '', 
                        'build_number' => '', 
                        'status' => 'ABORTED', 
                        'github_organization_id' => $organization,
                        'github_repository_id' => $repository_id,
                        'github_branch_state_name' => $branch_name,
                        'build_pr' => $build_pr,
                        'initiate_from' => $initiate_from
                    ];
                    \App\BuildProcessHistory::create($record);
                    
                    BuildProcessErrorLog::log([
                        'project_id' => $proj,
                        'error_message' => 'Job name and serverenv can not be empty!',
                        'error_code' => "500",
                        'github_organization_id' => $organization,
                        'github_repository_id' => $repository_id,
                        'github_branch_state_name' => $branch_name
                    ]);

                    continue;
                }

                $jobName = $job_name;
                $branch_name = $branch_name;
                $serverenv = $serverenv;
                $verbosity = 'high';
                
                try{
                    $jenkins = new \JenkinsKhan\Jenkins('http://apibuild:11286d3dbdb6345298c8b6811e016d8b1e@deploy.theluxuryunlimited.com');
                    $job =$jenkins->launchJob($jobName, ['branch_name' => $branch_name, 'repository' => $repository, 'serverenv' => $serverenv, 'verbosity' => $verbosity]);
                    if ($jenkins->getJob($jobName)) {
                        $job = $jenkins->getJob($jobName);
                        // $builds = $job->getBuilds();
                        $lastBuild = $job->getLastBuild();
                        $latestBuildNumber = $latestBuildResult = "";
                        if ($lastBuild) {
                            $latestBuildNumber = $lastBuild->getNumber();
                            $latestBuildResult = $lastBuild->getResult();
                        }
                        
                        $buildDetail = 'Build Name: ' . $jobName . '<br> Build Repository: ' . $repository .'<br> Branch Name: ' . $branch_name;
                        
                        $record = [
                            'store_website_id' => $proj, 
                            'created_by' =>$user_id, 
                            'text' => $buildDetail, 
                            'build_name' => $jobName, 
                            'build_number' => $latestBuildNumber, 
                            'status' => $latestBuildResult, 
                            'github_organization_id' => $organization,
                            'github_repository_id' => $repository_id,
                            'github_branch_state_name' => $branch_name,
                            'build_pr' => $build_pr,
                            'initiate_from' => $initiate_from
                        ];

                        \App\BuildProcessHistory::create($record);

                    } else {
                        $record = [
                            'store_website_id' => $proj, 
                            'created_by' =>$user_id, 
                            'text' => 'Please try again, Jenkins job not created', 
                            'build_name' => '', 
                            'build_number' => '', 
                            'status' => 'ABORTED', 
                            'github_organization_id' => $organization,
                            'github_repository_id' => $repository_id,
                            'github_branch_state_name' => $branch_name
                        ];
                        \App\BuildProcessHistory::create($record);
                        BuildProcessErrorLog::log([
                            'project_id' => $proj,
                            'error_message' => 'Jenkins job not created',
                            'error_code' => "500",
                            'github_organization_id' => $organization,
                            'github_repository_id' => $repository_id,
                            'github_branch_state_name' => $branch_name,
                            'build_pr' => $build_pr,
                            'initiate_from' => $initiate_from
                        ]);
                    }
                }catch (\Exception $e){
                    $record = [
                        'store_website_id' => $proj, 
                        'created_by' =>$user_id, 
                        'text' => $e->getMessage(), 
                        'build_name' => '', 
                        'build_number' => '', 
                        'status' => 'ABORTED', 
                        'github_organization_id' => $organization,
                        'github_repository_id' => $repository_id,
                        'github_branch_state_name' => $branch_name
                    ];
                    \App\BuildProcessHistory::create($record);
                    BuildProcessErrorLog::log([
                        'project_id' => $proj,
                        'error_message' => $e->getMessage(),
                        'error_code' => "500",
                        'github_organization_id' => $organization,
                        'github_repository_id' => $repository_id,
                        'github_branch_state_name' => $branch_name,
                        'build_pr' => $build_pr,
                        'initiate_from' => $initiate_from
                    ]);
                }
            }else{
                $record = [
                    'store_website_id' => $proj, 
                    'created_by' =>$user_id, 
                    'text' => 'Project Data not found', 
                    'build_name' => '', 
                    'build_number' => '', 
                    'status' => 'ABORTED', 
                    'github_organization_id' => $organization,
                    'github_repository_id' => $repository_id,
                    'github_branch_state_name' => $branch_name,
                    'build_pr' => $build_pr,
                    'initiate_from' => $initiate_from
                ];

                \App\BuildProcessHistory::create($record);

                BuildProcessErrorLog::log([
                    'project_id' => $proj,
                    'error_message' => 'Project Data not found',
                    'error_code' => "500",
                    'github_organization_id' => $organization,
                    'github_repository_id' => $repository_id,
                    'github_branch_state_name' => $branch_name
                ]);
            }

        }
        return response()->json(['code' => 200, 'message' => 'Process builed complete successfully. Please check builed process logs for more details']);
    }
    
    public function buildProcess(Request $request)
    {
        $post = $request->all();
        $repository_id = $repository = $request->repository;
        $branch_name = $request->branch_name;
        $job_name = $request->job_name;
        $organization = $request->organization;
        $projectId = $request->project_id;
        $initiate_from = $request->initiate_from;
        
        if($repository==''){
            BuildProcessErrorLog::log([
                'project_id' => $projectId,
                'error_message' => 'Please select repository',
                'error_code' => "500",
                'github_organization_id' => "",
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => ""
            ]);

            return response()->json(['code' => 500, 'message' => 'Please select repository']);
        }
        if($branch_name==''){
            BuildProcessErrorLog::log([
                'project_id' => $projectId,
                'error_message' => 'Please select Branch',
                'error_code' => "500",
                'github_organization_id' => $organization,
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => $branch_name
            ]);
            return response()->json(['code' => 500, 'message' => 'Please select Branch']);
        }
        if($job_name==''){
            BuildProcessErrorLog::log([
                'project_id' => "",
                'error_message' => 'Please Enter Job Name',
                'error_code' => "500",
                'github_organization_id' => $organization,
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => $branch_name
            ]);

            return response()->json(['code' => 500, 'message' => 'Please Enter Job Name']);
        }

        if (! empty($request->project_id)) {
            $project = Project::find($request->project_id);

            if ($project) {
                $repositoryData = \App\Github\GithubRepository::find($request->repository);
                
                $repository = $request->repository;
                if( $repositoryData){
                    $repository = $repositoryData->name;
                }
                
                $jobName = $request->job_name;
                $branch_name = $request->branch_name;
                $serverenv = $project->serverenv;
                $verbosity = 'high';
                //$branch_name="stage";$repository="brands-labels";
                try{
                    $jenkins = new \JenkinsKhan\Jenkins('http://apibuild:11286d3dbdb6345298c8b6811e016d8b1e@deploy.theluxuryunlimited.com');
                    $launchJobStatus =$jenkins->launchJob($jobName, ['branch_name' => $branch_name, 'repository' => $repository, 'serverenv' => $serverenv, 'verbosity' => $verbosity]);
                    if ($launchJobStatus) {
                        
                        $job = $jenkins->getJob($jobName);
                        // $builds = $job->getBuilds();
                        
                        $buildDetail = 'Build Name: ' . $jobName . '<br> Build Repository: ' . $repository .'<br> Branch Name: ' . $branch_name;
                        
                        $lastBuild = $job->getLastBuild();
                        $latestBuildNumber = $latestBuildResult = "";
                        if ($lastBuild) {
                            $latestBuildNumber = $lastBuild->getNumber() + 1;
                            $latestBuildResult = $lastBuild->getResult();
                        }

                        $record = [
                            'store_website_id' => $request->project_id, 
                            'created_by' =>auth()->user()->id, 
                            'text' => $buildDetail, 
                            'build_name' => $jobName, 
                            'build_number' => $latestBuildNumber, 
                            'status' => $latestBuildResult, 
                            'github_organization_id' => $organization,
                            'github_repository_id' => $repository_id,
                            'github_branch_state_name' => $branch_name,
                            'initiate_from' => $initiate_from
                        ];

                        \App\BuildProcessHistory::create($record);

                        return response()->json(['code' => 200, 'message' => 'Process builed complete successfully.']);
                    } else {
                        BuildProcessErrorLog::log([
                            'project_id' => $request->project_id,
                            'error_message' => 'Jenkins job not created',
                            'error_code' => "500",
                            'github_organization_id' => $organization,
                            'github_repository_id' => $repository_id,
                            'github_branch_state_name' => $branch_name
                        ]);

                        return response()->json(['code' => 500, 'message' => 'Please try again, Jenkins job not created']);
                    }
                }catch (\Exception $e){
                    BuildProcessErrorLog::log([
                        'project_id' => $request->project_id,
                        'error_message' => $e->getMessage(),
                        'error_code' => "500",
                        'github_organization_id' => $organization,
                        'github_repository_id' => $repository_id,
                        'github_branch_state_name' => $branch_name
                    ]);

                    return response()->json(['code' => 500, 'message' => $e->getMessage()]);
                }
                catch (\RuntimeException $e){
                    BuildProcessErrorLog::log([
                        'project_id' => $request->project_id,
                        'error_message' => $e->getMessage(),
                        'error_code' => "500",
                        'github_organization_id' => $organization,
                        'github_repository_id' => $repository_id,
                        'github_branch_state_name' => $branch_name
                    ]);

                    return response()->json(['code' => 500, 'message' => $e->getMessage()]);
                }
            }
            
        }
        return response()->json(['code' => 500, 'message' => 'Project Data is not available.']);
        
    }
    
    public function buildProcessStatusLogs(Request $request)
    {
        $histories = \App\BuildProcessStatusHistories::where('build_process_history_id', $request->id)->orderBy('id','desc')->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history',
            'status_name' => 'success',
        ], 200);
    }
    
    public function buildProcessErrorLogs(Request $request)
    {
        $buildProcessErrorLogs = BuildProcessErrorLog::with('project');

        $buildProcessErrorLogs = $buildProcessErrorLogs->orderBy('id','desc')->paginate(10);
        
        return view('project.build-process-error-logs', compact('buildProcessErrorLogs'));
    }

    // New concept in page
    public function buildProcessLogs(Request $request, $id= null)
    {
        $responseLogs = \App\BuildProcessHistory::with('project')->leftJoin('users as u','u.id','=','build_process_histories.created_by')->select('build_process_histories.*','u.name as usersname');

        if($id){
            $responseLogs->where('store_website_id', $id);
        }
        
        if($request->has('branch') && $request->branch!=''){
            $responseLogs->where('github_branch_state_name', $request->branch);
        }
        
        if($request->has('buildby') && $request->buildby!=''){
            $responseLogs->where('created_by', $request->buildby);
        }

        $responseLogs=$responseLogs->orderBy('id','desc')->paginate(10);
       
        foreach($responseLogs as $responseLog){
            
            $github_organization_id=$responseLog->github_organization_id;
            $job_name=$responseLog->build_name;
            $build_number=$responseLog->build_number;
            $project_id=$responseLog->store_website_id;
            if($responseLog->status!='SUCCESS' && $responseLog->status!='ABORTED'){
                try{
                    $jenkins = new \JenkinsKhan\Jenkins('http://apibuild:11286d3dbdb6345298c8b6811e016d8b1e@deploy.theluxuryunlimited.com');
                    $job = $jenkins->getJob($job_name);
                    if($job){
                        $build=$job->getJenkinsBuild($build_number);
                        if($build){
                            $build_status=$build->getResult();
                            
                            if($responseLog->status!=$build_status){
                            
                                $record = [
                                    'project_id' => $project_id, 
                                    'build_process_history_id' => $responseLog->id, 
                                    'build_number' => $build_number, 
                                    'old_status' => $responseLog->status,
                                    'status' => $build_status
                                ];
        
                                \App\BuildProcessStatusHistories::create($record);
                            
                                $responseLog->status=$build_status;
                                $responseLog->save();
                            }
                            
                        }
                    }
                    
                }catch (\Exception $e){
                    
                }
                catch (\RuntimeException $e){
                    
                }
            }

        }
        return view('project.build-process-logs', compact('responseLogs'));
    }

    // Old concept in modal popup
    // public function buildProcessLogs(Request $request, $id)
    // {
    //     try {
    //         $responseLog = \App\BuildProcessHistory::leftJoin('users as u','u.id','=','build_process_histories.created_by')->where('store_website_id', '=', $id)->select('build_process_histories.*','u.name as usersname')->latest()->get();
    //         //dd($responseLog);
    //         if ($responseLog != null) {
    //             $html = '';
    //             foreach ($responseLog as $res) {
    //                 //dd($res->created_at);
    //                 $html .= '<tr>';
    //                 $html .= '<td>' . $res->id . '</td>';
                    
    //                 $html .= '<td>' .  $res->usersname .'</td>';
                    
    //                 $html .= '<td>' . $res->build_number . '</td>';
    //                 $html .= '<td>' . $res->build_name . '</td>';
    //                 $html .= '<td>' . $res->text . '</td>';
    //                 $html .= '<td>' . $res->status . '</td>';
                    
    //                 $html .= '<td>' . $res->created_at . '</td>';
    //                 $html .= '</tr>';
    //             }

    //             return response()->json([
    //                 'code' => 200,
    //                 'data' => $html,
    //                 'message' => '',
    //             ]);
    //         }

    //         return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    //     } catch (\Exception $e) {
    //         $msg = $e->getMessage();

    //         return response()->json(['code' => 500, 'data' => [], 'message' => $msg]);
    //     }
    // }

    public function store(Request $request)
    {
        // Validation Part
        $this->validate(
            $request, [
                'name' => 'required',
                'project_type' => 'required',
                'job_name' => 'required',
                'store_website_id' => 'required',
                'serverenv' => 'required'
            ]
        );

        $data = $request->except('_token');

        // Save Project
        $project = new Project();
        $project->name = $data['name'];
        $project->job_name = $data['job_name'];
        $project->project_type = $data['project_type'];
        $project->serverenv = $data['serverenv'];
        $project->save();

        $project->storeWebsites()->attach($data['store_website_id']);

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Project created successfully!',
            ]
        );
    }

    public function destroy($id)
    {
        $project = Project::find($id);
        $project->storeWebsites()->detach();
        $project->delete();

        return redirect()->route('project.index')
            ->with('success', 'Project deleted successfully');
    }

    public function edit(Request $request, $id)
    {
        $project = Project::with('storeWebsites')->where('id', $id)->first();

        if ($project) {
            return response()->json(['code' => 200, 'data' => $project]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    public function update(Request $request, $id)
    {
        // Validation Part
        $this->validate(
            $request, [
                'name' => 'required',
                'job_name' => 'required',
                'project_type' => 'required',
                'store_website_id' => 'required',
                'serverenv' => 'required'
            ]
        );

        $data = $request->except('_token');

        // Save Project
        $project = Project::find($data['id']);
        $project->name = $data['name'];
        $project->job_name = $data['job_name'];
        $project->project_type = $data['project_type'];
        $project->serverenv = $data['serverenv'];
        $project->save();

        $project->storeWebsites()->detach();
        $project->storeWebsites()->attach($data['store_website_id']);

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Project updated successfully!',
            ]
        );
    }

    public function serverenvStore(Request $request)
    {
        // Validation Part
        $this->validate(
            $request, [
                'name' => 'required',
            ]
        );

        $data = $request->except('_token');

        // Save Project server env
        $projectServerenv = new ProjectServerenv();
        $projectServerenv->name = $data['name'];
        $projectServerenv->save();

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Project server env created successfully!',
            ]
        );
    }
    public function projectTypeStore(Request $request)
    {
        // Validation Part
        $this->validate(
            $request, [
                'name' => 'required',
            ]
        );

        $data = $request->except('_token');

        // Save Project server env
        $ProjectType = new ProjectType();
        $ProjectType->name = $data['name'];
        $ProjectType->save();

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Project type created successfully!',
            ]
        );
    }

}
