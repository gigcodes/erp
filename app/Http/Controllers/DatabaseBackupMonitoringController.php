<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DatabaseBackupMonitoring;

class DatabaseBackupMonitoringController extends Controller
{
    public function getDbBackupLists(Request $request)
    {
        $dbLists = new DatabaseBackupMonitoring();
        $dbLists = $dbLists->where('is_resolved', '0');

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
        if ($request->search_status) {
            $dbLists = $dbLists->whereIn('status', 'LIKE', '%' . $request->search_status . '%');
        }
        if ($request->date) {
            $dbLists = $dbLists->where('date', 'LIKE', '%' . $request->date . '%');
        }

        $dbLists = $dbLists->latest()->paginate(25);

        if ($request->ajax()) {
            return response()->json(['code' => 200, 'data' => $dbLists, 'count' => count($dbLists), 'message' => 'Listed successfully!!!']);
        }

        return view('databse-Backup.db-backup-list', compact('dbLists'));
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
}
