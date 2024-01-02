<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTheme;
use Illuminate\Http\Request;
use App\Models\ThemeStructure;

class ProjectThemeController extends Controller
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
        $projectThemes = ProjectTheme::latest();

        if ($request->keyword) {
            $projectThemes = $projectThemes->where('name', 'LIKE', '%' . $request->keyword . '%');
        }

        $search_project_id = $request->get('search_project_id');
        if ($search_project_id) {
            $projectThemes = $projectThemes->where('project_id', $search_project_id);
        }

        $projectThemes = $projectThemes->paginate(20);

        $projects = Project::get()->pluck('name', 'id');

        return view('project-theme.index', compact('projects', 'projectThemes'));
    }

    public function store(Request $request)
    {
        // Validation Part
        $this->validate(
            $request, [
                'project_id' => 'required',
                'name' => 'required',
            ]
        );

        $data = $request->except('_token');

        // Save
        $projectTheme = new ProjectTheme();
        $projectTheme->project_id = $data['project_id'];
        $projectTheme->name = $data['name'];
        $projectTheme->save();

        $themeStructure = new ThemeStructure();
        $themeStructure->theme_id = $projectTheme->id;
        $themeStructure->name = $projectTheme->name;
        $themeStructure->is_file = 0;
        $themeStructure->is_root = 1;
        $themeStructure->save();

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Project theme created successfully!',
            ]
        );
    }

    public function edit(Request $request, $id)
    {
        $projectTheme = ProjectTheme::where('id', $id)->first();

        if ($projectTheme) {
            return response()->json(['code' => 200, 'data' => $projectTheme]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    public function update(Request $request, $id)
    {
        // Validation Part
        $this->validate(
            $request, [
                'project_id' => 'required',
                'name' => 'required',
            ]
        );

        $data = $request->except('_token');

        $projectTheme = ProjectTheme::where('id', $id)->firstOrFail();

        // Save
        $projectTheme->project_id = $data['project_id'];
        $projectTheme->name = $data['name'];
        $projectTheme->save();

        $themeStructure = ThemeStructure::where('theme_id', $id)->where('is_root', 1)->first();

        if ($themeStructure && $themeStructure->name != $projectTheme->name) {
            $themeStructure->name = $projectTheme->name;
            $themeStructure->save();
        }

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Project theme updated successfully!',
            ]
        );
    }

    public function destroy($id)
    {
        $projectTheme = ProjectTheme::findOrFail($id);
        $projectTheme->delete();

        return redirect()->route('project-theme.index')
            ->with('success', 'Project theme deleted successfully');
    }
}
