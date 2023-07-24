<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeploymentVersion;

class DeploymentVersionController extends Controller
{
    public function listDeploymentVersion(Request $request)
    {
        $deploymentVersions = new DeploymentVersion();
        
        if ($request->search_versionNumber) {
            $deploymentVersions = $deploymentVersions->where('version_number', 'LIKE', '%' . $request->search_versionNumber . '%');
        }
        if ($request->search_jobName) {
            $deploymentVersions = $deploymentVersions->where('job_name', 'LIKE', '%' . $request->search_jobName . '%');
        }  
        if ($request->search_branchName) {
            $deploymentVersions = $deploymentVersions->where('branch_name', 'LIKE', '%' . $request->search_branchName . '%');
        }  
        if ($request->deployment_date) {
            $deploymentVersions = $deploymentVersions->where('deployment_date', 'LIKE', '%' . $request->deployment_date . '%');
        }
        if ($request->pr_date) {
            $deploymentVersions = $deploymentVersions->where('pr_date', 'LIKE', '%' . $request->pr_date . '%');
        }
        $deploymentVersions = $deploymentVersions->latest()->paginate(25);

        $search_versionNumber = $request->search_versionNumber;
        $search_jobName = $request->search_jobName;
        $search_branchName = $request->search_branchName;
        $deployment_date = $request->deployment_date;
        $pr_date = $request->pr_date;

        return view('deployment-versions.deployment-version-listing', compact('deploymentVersions', 'search_versionNumber', 'search_jobName', 'search_branchName','deployment_date', 'pr_date'));
    }
}
