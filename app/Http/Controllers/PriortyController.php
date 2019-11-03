<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Priority;

class PriortyController extends Controller
{
    public function index()
    {
    	$priorities = Priority::paginate(15);
    	return view('instagram.priority.index',['priorities' => $priorities]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'level' => 'required|numeric',
            'name' => 'required|string|max:255',
        ]);


        $keyword = $request->name;
        $priority = Priority::findorfail($request->id);
        //dd($priority);
        $priority->keyword = $keyword;
        $priority->level = $request->level;
        $priority->description = $request->description;
        $priority->update();

        return redirect()->back()->with('message', 'Prority updated successfully!');
    }
    public function store(Request $request)
    {

    	$this->validate($request, [
            'level' => 'required|numeric',
            'name' => 'required|string|max:255',
        ]);


    	$keyword = $request->name;


    	$priority = new Priority;
    	$priority->keyword = $keyword;
    	$priority->level = $request->level;
    	$priority->description = $request->description;
    	$priority->save();

		return redirect()->back()->with('message', 'Prority created successfully!');
	}

    public function destroy(Request $request)
    {
         $priority = Priority::findorfail($request->id);
         if($priority != null && $priority != ''){
            $priority->delete();
            return redirect()->back()->with('message', 'Prority deleted successfully!');
         }

         return redirect()->back()->with('message', 'Prority cannot be deleted!');

    }
}
