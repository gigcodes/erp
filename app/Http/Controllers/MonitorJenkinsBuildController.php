<?php

namespace App\Http\Controllers;

use App\Models\MonitorJenkinsBuild;
use Illuminate\Http\Request;

class MonitorJenkinsBuildController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $projectId = $request->get('project_id');
        $workerId = $request->get('worker_id');
        
        $monitorJenkinsBuilds = MonitorJenkinsBuild::latest();

        if (!empty($keyword)) {
            $monitorJenkinsBuilds = $monitorJenkinsBuilds->where(function ($q) use ($keyword) {
                $q->orWhere('monitor_jenkins_builds.project', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_jenkins_builds.worker', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_jenkins_builds.store_id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_jenkins_builds.error', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_jenkins_builds.build_number', 'LIKE', '%' . $keyword . '%');
            });
        }

        if ($projectId) {
            $monitorJenkinsBuilds = $monitorJenkinsBuilds->WhereIn('id', $projectId);
        }
        
        if ($workerId) {
            $monitorJenkinsBuilds = $monitorJenkinsBuilds->WhereIn('id', $workerId);
        } 

        $monitorJenkinsBuilds = $monitorJenkinsBuilds->paginate(25);

        return view('monitor.jenkins_build_index', compact('monitorJenkinsBuilds'));
    }

}