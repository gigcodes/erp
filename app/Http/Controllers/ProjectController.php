<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectServerenv;
use App\StoreWebsite;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
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
        $repositories = \App\Github\GithubRepository::All();
        $organizations = \App\Github\GithubOrganization::All();
        
        return view('project.index', compact('projects', 'store_websites','repositories', 'organizations', 'serverenvs'));
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
           
            $branches = \App\Github\GithubBranchState::where('repository_id', $build_repository)->orderBy('created_at', 'desc')->get()->pluck('branch_name', 'branch_name')->toArray();  
            if ($branches) {
               
                $options = ['<option value="" >--  Select a Branch --</option>'];
                foreach ($branches as $key => $value) {
                    $options[] = '<option value="' . $key . '" ' . ($key == request('selected') ? 'selected' : '') . ' >' . $value . '</option>';
                }
            } else {
                $options = ['<option value="" >No records found.</option>'];
            }
        } else {
            $options = ['<option value="" >Please Select a Repository</option>'];
        }

        return response()->json(['data' => implode('', $options)]);
    }

    public function buildProcess(Request $request)
    {
        $post = $request->all();
        $repository = $request->repository;
        $branch_name = $request->branch_name;
        $job_name = $request->job_name;
        if($repository==''){
            return response()->json(['code' => 500, 'message' => 'Please select repository']);
        }
        if($branch_name==''){
            return response()->json(['code' => 500, 'message' => 'Please select Branch']);
        }
        if($job_name==''){
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
                    $job =$jenkins->launchJob($jobName, ['branch_name' => $branch_name, 'repository' => $repository, 'serverenv' => $serverenv, 'verbosity' => $verbosity]);
                    if ($jenkins->getJob($jobName)) {
                        $job = $jenkins->getJob($jobName);
                        $builds = $job->getBuilds();
                        
                        $buildDetail = 'Build Name: ' . $jobName . '<br> Build Repository: ' . $repository .'<br> Branch Name: ' . $branch_name;
                        
                        $record = ['store_website_id' => $request->project_id, 'created_by' =>auth()->user()->id, 'text' => $buildDetail, 'build_name' => $jobName, 'build_number' => $builds[0]->getNumber()];
                        \App\BuildProcessHistory::create($record);

                        return response()->json(['code' => 200, 'message' => 'Process builed complete successfully.']);
                    } else {
                        return response()->json(['code' => 500, 'message' => 'Please try again, Jenkins job not created']);
                    }
                }catch (\Exception $e){
                    return response()->json(['code' => 500, 'message' => $e->getMessage()]);
                }
                catch (\RuntimeException $e){
                    return response()->json(['code' => 500, 'message' => $e->getMessage()]);
                }
            }
            
        }
        return response()->json(['code' => 500, 'message' => 'Project Data is not available.']);
        
    }

    public function buildProcessLogs(Request $request, $id)
    {
        try {
            $responseLog = \App\BuildProcessHistory::leftJoin('users as u','u.id','=','build_process_histories.created_by')->where('store_website_id', '=', $id)->select('build_process_histories.*','u.name as usersname')->latest()->get();
            //dd($responseLog);
            if ($responseLog != null) {
                $html = '';
                foreach ($responseLog as $res) {
                    //dd($res->created_at);
                    $html .= '<tr>';
                    $html .= '<td>' . $res->id . '</td>';
                    
                    $html .= '<td>' .  $res->usersname .'</td>';
                    
                    $html .= '<td>' . $res->build_number . '</td>';
                    $html .= '<td>' . $res->build_name . '</td>';
                    $html .= '<td>' . $res->text . '</td>';
                    $html .= '<td>' . $res->status . '</td>';
                    
                    $html .= '<td>' . $res->created_at . '</td>';
                    $html .= '</tr>';
                }

                return response()->json([
                    'code' => 200,
                    'data' => $html,
                    'message' => '',
                ]);
            }

            return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'data' => [], 'message' => $msg]);
        }
    }

    public function store(Request $request)
    {
        // Validation Part
        $this->validate(
            $request, [
                'name' => 'required',
                'store_website_id' => 'required',
                'serverenv' => 'required'
            ]
        );

        $data = $request->except('_token');

        // Save Project
        $project = new Project();
        $project->name = $data['name'];
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
                'store_website_id' => 'required',
                'serverenv' => 'required'
            ]
        );

        $data = $request->except('_token');

        // Save Project
        $project = Project::find($data['id']);
        $project->name = $data['name'];
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

}
