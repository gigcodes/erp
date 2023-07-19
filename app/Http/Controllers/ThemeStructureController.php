<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ThemeFile;
use App\Models\ThemeStructure;
use Illuminate\Http\Request;


class ThemeStructureController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tree = json_encode($this->buildTree());
        return view('theme-structure.index', compact('tree'));
    }

    public function reloadTree()
    {
        $tree = $this->buildTree();
        return response()->json($tree);
    }

    private function buildTree($parentID = null)
    {
        $tree = [];
        $items = ThemeStructure::where('parent_id', $parentID)->orderBy('position')->get(['id', 'name', 'is_file', 'is_root']);

        foreach ($items as $item) {
            $node = [
                'id' => $item->id,
                'parent_id' => $parentID ?: '#',
                'text' => $item->name,
                'is_root' => $item->is_root
            ];

            if ($item->is_file) {
                $node['icon'] = 'jstree-file';
            } else {
                $node['icon'] = 'jstree-folder';
                $node['children'] = $this->buildTree($item->id);
            }

            $tree[] = $node;
        }

        return $tree;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'is_file' => 'required|boolean',
            'parent_id' => 'nullable|exists:theme_structure,id'
        ]);

        $folder = ThemeStructure::create($validatedData);

        return response()->json($folder);
    }

    public function themeFileStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'is_file' => 'required|boolean',
            'parent_id' => 'required|exists:theme_structure,id'
        ]);

        $file = ThemeFile::create($validatedData);

        return response()->json($file);
    }

    public function deleteItem(Request $request)
    {
        $itemId = $request->input('id');
        $item = ThemeStructure::find($itemId);

        if ($item) {
            if ($item->is_root) {
                return response()->json(['message' => 'Root folder cannot be deleted'], 403);
            }

            $item->delete();
            return response()->json(['message' => 'Item deleted successfully']);
        }

        return response()->json(['message' => 'Item not found'], 404);
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
                'store_website_id' => 'required',
                'serverenv' => 'required'
            ]
        );

        $data = $request->except('_token');

        // Save Project
        $project = Project::find($data['id']);
        $project->name = $data['name'];
        $project->job_name = $data['job_name'];
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

    

}
