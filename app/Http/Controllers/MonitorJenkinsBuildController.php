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
        
        $monitorJenkinsBuilds = MonitorJenkinsBuild::orderBy('id', 'desc')->latest();

        if (!empty($keyword)) {
            $monitorJenkinsBuilds = $monitorJenkinsBuilds->where(function ($q) use ($keyword) {
                $q->orWhere('monitor_jenkins_builds.project', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_jenkins_builds.worker', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_jenkins_builds.store_id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_jenkins_builds.error', 'LIKE', '%' . $keyword . '%');
            });
        }

        $monitorJenkinsBuilds = $monitorJenkinsBuilds->paginate(3);

        return view('monitor.jenkins_build_index', compact('monitorJenkinsBuilds'));
    }

}