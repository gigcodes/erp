<?php

namespace App\Http\Controllers;

use App\Models\Project;
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
        $projects = $projects->paginate(10);

        $store_websites = StoreWebsite::get()->pluck('website', 'id');

        return view('project.index', compact('projects', 'store_websites'));
    }

    public function store(Request $request)
    {
        // Validation Part
        $this->validate(
            $request, [
                'name' => 'required',
                'store_website_id' => 'required'
            ]
        );

        $data = $request->except('_token');

        // Save Project
        $project = new Project();
        $project->name = $data['name'];
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

}
