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

class CodeShortcutController extends Controller
{
    public function index(Request $request)
    {
        $data['codeshortcut'] = CodeShortcut::orderBy('id', 'desc')->paginate(Setting::get('pagination'));
        $data['suppliers'] = Supplier::select('id', 'supplier')->get();
        $data['users'] = User::select('id', 'name')->get();
        if ($request->ajax()) {
            $query = CodeShortcut::select('*');
            if($request->term){
                $query = $query->where('code', 'like', '%' . $request->term . '%');
            }
            if($request->id){
                $query = $query->where('supplier_id', '=', $request->id);
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
        $validated->user_id = auth()->user()->id;
        $validated->supplier_id = $request->supplier;
        $validated->code = $request->code;
        $validated->description = $request->description;
        $validated->save();
        return back()->with('success', 'Code Shortcuts successfully saved.');
    }

    public function update(Request $request, $id)
    {
        CodeShortcut::where('id', '=', $id)->update([
            'supplier_id' => $request->supplier,
            'code' => $request->code,
            'description' => $request->description
        ]);
        return back()->with('success', 'Code Shortcuts successfully updated.');
    }

    public function destory($id)
    {
        CodeShortcut::where('id', $id)->delete();
        return back()->with('success', 'Code Shortcuts successfully removed.');
    }
}
