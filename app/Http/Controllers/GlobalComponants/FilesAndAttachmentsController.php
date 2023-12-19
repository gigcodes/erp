<?php

namespace App\Http\Controllers\GlobalComponants;

use App\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GlobalFilesAndAttachments;
use Auth;
use DataTables;

class FilesAndAttachmentsController extends Controller
{
	public function get_data(Request $request)
	{
		$data = GlobalFilesAndAttachments::where('module_id',$request->module_id)->where('module',$request->module)->with('user')->get();
		
        return view('global_componants.files_and_attachments.files_and_attachments_list',compact('data'));
	}

	public function store_data(Request $request)
	{
		$error = $this->validate($request, [
            'title' => 'required',
            'filename' => 'required',
        ]);

		$store = $request->all();
        if (!empty($store)) {

	        $file = $request->file('filename');
	        if(isset($file)){
				$file->move(base_path('/storage/global_files_and_attachments_file'), $file->getClientOriginalName());
			}

			if(isset($store['filename']))
			{
				$filename = $store['filename']->getClientOriginalName();
			} else {
				$filename = '';
			}

            $storeHistory = GlobalFilesAndAttachments::create([
                'module_id' => $store['module_id'],
                'module' => $store['module'],
                'title' => $store['title'],
                'filename' => $filename,
                'created_by' => ! empty(Auth::user()->id) ? Auth::user()->id : null,
            ]);

            return response()->json(['storeHistory' => $storeHistory]);
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
	}

}