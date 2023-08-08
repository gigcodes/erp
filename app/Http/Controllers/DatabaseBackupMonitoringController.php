<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DatabaseBackupMonitoring;
use Illuminate\Support\Facades\Validator;
use App\Models\DatabaseBackupMonitoringStatus;
use Exception;


class DatabaseBackupMonitoringController extends Controller
{
    public function getDbBackupLists(Request $request)
    {

        $dbLists = New DatabaseBackupMonitoring();
        $dbLists = $dbLists->where('is_resolved','0');

        if ($request->search_instance) {
            $dbLists = $dbLists->where('instance', 'LIKE', '%' . $request->search_instance . '%');
        }
        if ($request->search_error) {
            $dbLists = $dbLists->where('error', 'LIKE', '%' . $request->search_error . '%');
        }  
        if ($request->s_ids) {
            $dbLists = $dbLists->WhereIn('server_name', $request->s_ids);
        }
        if ($request->db_ids) {
            $dbLists = $dbLists->WhereIn('database_name', $request->db_ids);
        }
        if ($request->date) {
            $dbLists = $dbLists->where('date', 'LIKE', '%' . $request->date . '%');
        }

        $dbLists = $dbLists->with(['dbStatusColour'])->latest()->paginate(25);

        $dbStatuses = DatabaseBackupMonitoringStatus::all();

        if ($request->ajax()) {
            return response()->json(['code' => 200, 'data' => $dbLists, 'count'=> count($dbLists), 'message' => 'Listed successfully!!!']);
        }


        return view('databse-Backup.db-backup-list', compact('dbLists','dbStatuses'));
    }


    public function dbErrorShow(Request $request)
    {     
        $id = $request->input('id');
        $errorData = DatabaseBackupMonitoring::where('id', $id)->value('error');
        $htmlContent = '<tr><td>' . $errorData . '</td></tr>';

        return $htmlContent;
    }

    public function updateIsResolved(Request $request) 
    {
        $dbList = DatabaseBackupMonitoring::findOrFail($request->get('id'));
        $dbList->is_resolved = 1;
        $dbList->update();

        return response()->json(['code' => 200, 'data' => $dbList, 'message' => 'Resolved successfully!!!']);     
    }

    public function storeDbStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:database_backup_monitoring_statuses,name',
            'color' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'status_name' => 'error',
            ], 422);
        }

        $input = $request->except(['_token']);

        $data = DatabaseBackupMonitoringStatus::create($input);

        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Status Created successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
    }

    public function statusDbColorUpdate(Request $request)
    {
        $statusColor = $request->all();
        $data = $request->except('_token');
        foreach ($statusColor['color_name'] as $key => $value) {
            $magentoModuleVerifiedStatus = DatabaseBackupMonitoringStatus::find($key);
            $magentoModuleVerifiedStatus->color = $value;
            $magentoModuleVerifiedStatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function dbUpdateStatus(Request $request)
    {
        try {
            $dbMonitoring = DatabaseBackupMonitoring::findOrFail($request->db_backup_id);
            $dbMonitoring->db_status_id = $request->status;
            $dbMonitoring->save();

            $statusColour =  DatabaseBackupMonitoringStatus::find($request->status);
            $statusColour =  $statusColour->color;

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Status updated successfully',
                    'colourCode' => $statusColour,
                ], 200
            );
        } catch(Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Status not updated.',
                ], 500
            );
        }
    }
}
