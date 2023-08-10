<?php

namespace App\Http\Controllers;

use App\User;
use App\Setting;
use App\Supplier;
use App\CodeShortcut;
use Illuminate\Http\Request;
use App\CodeShortCutPlatform;
use App\Models\CodeShortcutFolder;

class CodeShortcutController extends Controller
{
    public function index(Request $request)
    {
        $data['codeshortcut'] = CodeShortcut::orderBy('id', 'desc')->paginate(Setting::get('pagination'));
        $data['suppliers'] = Supplier::select('id', 'supplier')->get();
        $data['users'] = User::select('id', 'name')->get();
        $data['platforms'] = CodeShortCutPlatform::select('id', 'name')->get();
        $data['folders'] = CodeShortcutFolder::select('id', 'name')->get();

        if ($request->ajax()) {
            $query = CodeShortcut::query();
            if ($request->term) {
                $query = $query->where('code', 'like', '%' . $request->term . '%');
            }
            if ($request->id) {
                $query = $query->where('supplier_id', '=', $request->id);
            }
            if ($request->platformIds) {
                $query = $query->whereIn('code_shortcuts_platform_id', $request->platformIds);
            }
            if ($request->codeTitle) {
                $query = $query->where('title', 'like', '%' . $request->codeTitle . '%');
            }
            if ($request->websites) {
                $query = $query->whereIn('website', $request->websites);
            }

            if ($request->createdAt === 'asc') {
                $query = $query->orderBy('created_at', 'asc');
            }
            if ($request->createdAt === 'desc') {
                $query = $query->orderBy('created_at', 'desc');
            }

            $data['codeshortcut'] = $query->orderBy('id', 'desc')->paginate(Setting::get('pagination'));

            return response()->json([
                'tbody' => view('code-shortcut.partials.list-code', $data)->render(),
            ], 200);
        }

        return view('code-shortcut.index', $data);
    }

    public function store(Request $request)
    {
        $validated = new CodeShortcut();
        if ($request->supplier) {
            $validated->supplier_id = $request->supplier;
        }
        if ($request->hasFile('notesfile')) {
            $file = $request->file('notesfile');
            $name = uniqid() . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/codeshortcut-image');
            $file->move($destinationPath, $name);
            $validated->filename = $name;
        }
        $validated->user_id = auth()->user()->id;
        $validated->code = $request->code;
        $validated->description = $request->description;
        $validated->code = $request->code;
        $validated->solution = $request->solution;
        $validated->title = $request->title;
        $validated->code_shortcuts_platform_id = $request->platform_id;
        $validated->folder_id = $request->folder_id;
        $validated->save();

        return back()->with('success', 'Code Shortcuts successfully saved.');
    }

    public function update(Request $request, $id)
    {
        if ($request->hasFile('notesfile')) {
            $file = $request->file('notesfile');
            $name = uniqid() . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/codeshortcut-image');
            $file->move($destinationPath, $name);
        } else {
            $name = null;
        }

        $updateData = [
            'supplier_id' => $request->supplier,
            'code' => $request->code,
            'description' => $request->description,
            'code_shortcuts_platform_id' => $request->platform_id,
            'title' => $request->title,
            'solution' => $request->solution,
            'folder_id' => $request->folder_id,
        ];

        if (! is_null($name)) {
            $updateData['filename'] = $name;
        }

        CodeShortcut::where('id', $id)->update($updateData);

        return back()->with('success', 'Code Shortcuts successfully updated.');
    }

    public function destory($id)
    {
        CodeShortcut::where('id', $id)->delete();

        return back()->with('success', 'Code Shortcuts successfully removed.');
    }

    public function shortcutPlatformStore(Request $request)
    {
        $platform = new CodeShortCutPlatform();
        $platform->name = $request->platform_name;
        $platform->save();

        return back()->with('success', 'Platform successfully created.');
    }

    public function getShortcutnotes(Request $request)
    {
        $data = CodeShortcut::with('platform', 'user_detail', 'supplier_detail')->orderBy('id', 'desc')->get();

        return response()->json(['code' => 200, 'data' => $data, 'message' => 'Listed successfully!!!']);
    }

    public function shortcutListFolder()
    {
        try {
            $folders = CodeShortcutFolder::paginate(15);

            return view('code-shortcut.code-shorcut-folder-list', compact('folders'));
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            \Log::error('Postman controller folderIndex method error => ' . json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function shortcutCreateFolder(Request $request)
    {
        try {
            if (isset($request->id) && $request->id > 0) {
                $folder = CodeShortcutFolder::find($request->id);
            } else {
                $folder = new CodeShortcutFolder();
            }
            $folder->name = $request->folder_name;
            $folder->save();

            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function shortcutEditFolder(Request $request)
    {
        try {
            $folders = CodeShortcutFolder::find($request->id);

            return response()->json(['code' => 200, 'data' => $folders, 'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function shortcutDeleteFolder(Request $request)
    {
        try {
            $folders = CodeShortcutFolder::where('id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $folders, 'message' => 'Deleted successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function shortcutUserPermission(Request $request)
    {
        try {
            $codeShortCuts = CodeShortcut::where('folder_id', $request->per_folder_name)->get();

            foreach ($codeShortCuts as $codeShortCut) {
                $user_permission = $codeShortCut->user_permission . ',' . $request->per_user_name;
                $user_permission = array_unique(explode(',', $user_permission));
                $user_permission = implode(',', $user_permission);
                $postman = CodeShortcut::where('id', '=', $codeShortCut->id)->update(
                    [
                        'user_permission' => $user_permission,
                    ]
                );
            }

            return response()->json(['code' => 200, 'message' => 'Permission Updated successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function CodeShortCutTruncate(Request $request)
    {
        CodeShortcut::truncate();

        return redirect()->route('code-shortcuts')->withSuccess('data Removed succesfully!');
    }
   
}
