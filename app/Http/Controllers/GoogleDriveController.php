<?php

namespace App\Http\Controllers;

use App\User;
use App\DeveloperTask;
use App\Models\GoogleDrive;
use Illuminate\Http\Request;

class GoogleDriveController extends Controller
{
    public function index(Request $request)
    {
        $google_drivedata = GoogleDrive::orderBy('created_at', 'desc');
        $google_drivedata = $google_drivedata->get();

        $user_name = User::select('name')->distinct();
        $user_name = $user_name->get();

        $developer_task = DeveloperTask::select('task')->distinct();
        $developer_task = $developer_task->get();

        return view('googledrive.index', compact('google_drivedata', 'user_name', 'developer_task'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(Request $request)
    {
        $drive_data              = new GoogleDrive();
        $drive_data->date        = $request->google_drive_date;
        $drive_data->user_module = $request->user_module;
        $drive_data->remarks     = $request->remarks;
        $drive_data->dev_task    = $request->dev_task;

        $i     = 0;
        $image = [];
        foreach ($request->file('upload_file') as $file) {
            $name      = $file->getClientOriginalName();
            $filename  = pathinfo($name, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $full_name = 'googledrive/' . $filename . '/' . $filename . '.' . $extension;

            $file->move(public_path('/googledrive/' . $filename), $name);
            $image[] = $full_name;
        }
        $string                  = implode(',', $image);
        $drive_data->upload_file = $string;
        $drive_data->save();

        return redirect()->back();
    }
}
