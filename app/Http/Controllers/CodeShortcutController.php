<?php

namespace App\Http\Controllers;

use App\Helpers\GuzzleHelper;
use App\Setting;
use App\CodeShortcut;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Supplier;
use App\User;
use App\CodeShortCutPlatform;

class CodeShortcutController extends Controller
{
    public function index(Request $request)
    {
        $data['codeshortcut'] = CodeShortcut::orderBy('id', 'desc')->paginate(Setting::get('pagination'));
        $data['suppliers'] = Supplier::select('id', 'supplier')->get();
        $data['users'] = User::select('id', 'name')->get();
        $data['platforms'] = CodeShortCutPlatform::select('id', 'name')->get();

        if ($request->ajax()) {
            $query = CodeShortcut::select('*');
            if($request->term){
                $query = $query->where('code', 'like', '%' . $request->term . '%');
            }
            if($request->id){
                $query = $query->where('supplier_id', '=', $request->id);
            }
            if($request->platformIds){
                $query = $query->whereIn('code_shortcuts_platform_id', $request->platformIds);
            }
            if($request->codeTitle){
                $query = $query->where('title', 'like', '%' . $request->codeTitle . '%');
            }
            if($request->createdAt === "asc"){
                $query = $query->orderBy('created_at', 'asc');
            }
            if($request->createdAt === "desc"){
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
        if($request->supplier) {
            $validated->supplier_id = $request->supplier;  
        }
        if ($request->hasFile('notesfile')) {
            $file = $request->file('notesfile');
            $name = uniqid() . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/codeshortcut-image');
            $file->move($destinationPath, $name); 
            $validated->filename  = $name;   
        }
        $validated->user_id = auth()->user()->id;
        $validated->code = $request->code;
        $validated->description = $request->description;
        $validated->code = $request->code;
        $validated->solution = $request->solution;
        $validated->title = $request->title;
        $validated->code_shortcuts_platform_id = $request->platform_id;
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
            'solution' => $request->solution
        ];
        
        if (!is_null($name)) {
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
        $data = CodeShortcut::with('platform','user_detail','supplier_detail')->orderBy('id', 'desc')->get();

        return response()->json(['code' => 200, 'data' => $data, 'message' => 'Listed successfully!!!']);
    }
}
