<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DatabaseExportCommandLog;
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
        $cmd = 'mysqldump --user=' . env('DB_USERNAME') . ' --password=\'' . env('DB_PASSWORD') . '\' --host=' . env('DB_HOST') . ' --no-data ' . $dbName . '  > ' . $dumpName;
        \Log::info('Executing:' . $cmd);

        // NEW Logic START
        // Command and arguments
        $descriptors = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        // Execute the command
        $process = proc_open($cmd, $descriptors, $pipes);

        if (is_resource($process)) {
            // Close stdin since we don't need it
            fclose($pipes[0]);

            // Capture stdout and stderr
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);

            // Close all pipes
            fclose($pipes[1]);
            fclose($pipes[2]);

            // Get the exit status
            $return_var = proc_close($process);

            if ($return_var === 0) {
                $commandLog           = new DatabaseExportCommandLog();
                $commandLog->user_id  = \Auth::user()->id;
                $commandLog->command  = $cmd;
                $commandLog->response = 'Database exported successfully';
                $commandLog->save();

                chmod($dumpName, 0755);
                header('Content-Type: application/octet-stream');
                header('Content-Transfer-Encoding: Binary');
                header('Content-disposition: attachment; filename=erp_live_schema.sql');
                $dumpUrl = env('APP_URL') . '/' . $dumpName;

                return response()->json(['code' => 200, 'data' => $dumpUrl, 'message' => 'Database exported successfully']);
            } else {
                $errorMessage = "Error exporting database. Exit status: $return_var\nOutput:\n" . $stderr;

                $commandLog           = new DatabaseExportCommandLog();
                $commandLog->user_id  = \Auth::user()->id;
                $commandLog->command  = $cmd;
                $commandLog->response = $errorMessage;
                $commandLog->save();

                return response()->json(['code' => 500, 'message' => 'Database export failed, Please check the logs']);
            }
        } else {
            // Handle the case where proc_open failed to execute the command
            return response()->json(['code' => 500, 'message' => 'Error exporting database']);
        }
    }

    public function commandLogs(Request $request)
    {
        $perPage = 10;

        $histories = DatabaseExportCommandLog::with(['user'])->latest()->paginate($perPage);

        $html = view('database.partial.command-logs-modal-html')->with('histories', $histories)->render();

        return response()->json(['code' => 200, 'data' => $histories, 'html' => $html, 'message' => 'Content render']);
    }
}
