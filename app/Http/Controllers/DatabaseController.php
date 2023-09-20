<?php

namespace App\Http\Controllers;

use App\DatabaseExportCommandLog;
use Illuminate\Http\Request;
use App\DatabaseHistoricalRecord;

class DatabaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $databaseHis = DatabaseHistoricalRecord::latest();

        $customRange = $request->get('customrange');

        if (! empty($customRange)) {
            $range = explode(' - ', $customRange);
            if (! empty($range[0])) {
                $startDate = $range[0];
            }
            if (! empty($range[1])) {
                $endDate = $range[1];
            }
        }

        if (! empty($startDate)) {
            $databaseHis = $databaseHis->whereDate('created_at', '>=', $startDate);
        }

        if (! empty($endDate)) {
            $databaseHis = $databaseHis->whereDate('created_at', '<=', $endDate);
        }

        $databaseHis = $databaseHis->paginate(20);

        $page = $databaseHis->currentPage();

        if ($request->ajax()) {
            $tml = (string) view('database.partial.list', compact('databaseHis', 'page'));

            return response()->json(['code' => 200, 'tpl' => $tml, 'page' => $page]);
        }

        return view('database.index', compact('databaseHis', 'page'));
    }

    public function states(Request $request)
    {
        return view('database.states');
    }

    public function processList()
    {
        return response()->json(['code' => 200, 'records' => \DB::select('show processlist')]);
    }

    public function processKill(Request $request)
    {
        $id = $request->get('id');

        return response()->json(['code' => 200, 'records' => \DB::statement("KILL $id")]);
    }

    public function export(Request $request)
    {
        $dbName = env('DB_DATABASE');
        \Log::info('Database name:' . $dbName);
        $dumpName = str_replace(' ', '_', $dbName) . '_schema.sql';
        \Log::info('Dump name:' . $dumpName);
        //$cmd = 'mysqldump -h erpdb -u erplive -p  --no-data '.$dbName.' > '.$dumpName;
        $cmd = 'mysqldump --user=' . env('DB_USERNAME') . ' --password=' . env('DB_PASSWORD') . ' --host=' . env('DB_HOST') . ' --no-data ' . $dbName . '  > ' . $dumpName . '  2>&1';
        \Log::info('Executing:' . $cmd);

        $allOutput = [];
        exec($cmd, $allOutput, $return_var);

        if ($return_var === 0) {
            $commandLog = new DatabaseExportCommandLog();
            $commandLog->user_id = \Auth::user()->id;
            $commandLog->command = $cmd;
            $commandLog->response = 'Database exported successfully';
            $commandLog->save();
        } else {
            $errorMessage = "Error exporting database. Exit status: $return_var\nOutput:\n" . implode("\n", $allOutput);

            $commandLog = new DatabaseExportCommandLog();
            $commandLog->user_id = \Auth::user()->id;
            $commandLog->command = $cmd;
            $commandLog->response = $errorMessage;
            $commandLog->save();
        }

        if ($return_var === 0) {
            chmod($dumpName, 0755);
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: Binary');
            header('Content-disposition: attachment; filename=erp_live_schema.sql');
            $dumpUrl = env('APP_URL') . '/' . $dumpName;
            return response()->json(['code' => 200, 'data' => $dumpUrl, 'message' => 'Database exported successfully']);
        }

        return response()->json(['code' => 500, 'message' => 'Database export failed, Please check the logs']);
    }

    public function commandLogs(Request $request)
    {
        $perPage = 10;

        $histories = DatabaseExportCommandLog::with(['user'])->latest()->paginate($perPage);

        $html = view('database.partial.command-logs-modal-html')->with('histories', $histories)->render();

        return response()->json(['code' => 200, 'data' => $histories, 'html' => $html, 'message' => 'Content render']);
    }
}
