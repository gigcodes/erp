<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MagentoProblem;
use App\Models\MagentoProblemStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MagentoProblemController extends Controller
{
    public function index(Request $request)
    {

        //$magentoProblems = new MagentoProblem();

        $magentoProblems = MagentoProblem::select('error_body', 'created_at', 'updated_at', 'source', 'test', 'severity', 'type', 'status', DB::raw("MAX(id) AS id"))->orderBy('id', 'DESC');

        if ($request->search_source) {
            $magentoProblems = $magentoProblems->where('source', 'LIKE', '%' . $request->search_source . '%');
        }
        if ($request->search_test) {
            $magentoProblems = $magentoProblems->Where('test', 'LIKE', '%' . $request->search_test . '%');
        }
        if ($request->search_severity) {
            $magentoProblems = $magentoProblems->Where('severity', 'LIKE', '%' .$request->search_severity . '%');
        }
        if ($request->error_body) {
            $magentoProblems = $magentoProblems->Where('error_body', 'LIKE', '%' . $request->error_body . '%');
        }
        if ($request->date) {
            $magentoProblems = $magentoProblems->where('created_at', 'LIKE', '%' . $request->date . '%');
        }
        if ($request->has('status')) {
            if ($request->status == "open") {
                $magentoProblems = $magentoProblems->where('status', 1);
            } elseif ($request->status == "closed") {
                $magentoProblems = $magentoProblems->where('status', 0);
            }
        }
        if ($request->type) {
            $magentoProblems = $magentoProblems->where('type',  'LIKE', '%' . $request->type . '%');
        }

        $magentoProblems = $magentoProblems->groupBy('error_body');

        $magentoProblems = $magentoProblems->latest()->paginate(\App\Setting::get('pagination', 10));

        $magento_statuses = MagentoProblemStatus::get();

        return view('magento-problems.index', compact('magentoProblems', 'magento_statuses'));

    }

    public function store(Request $request)
    {
        $decodedErrorMessage = base64_decode($request->input('error_body'));

        try {
            $magentoProblem = new MagentoProblem();

            $magentoProblem->source = $request->input('source');
            $magentoProblem->test = $request->input('test');
            $magentoProblem->severity = $request->input('severity') ?? '';
            $magentoProblem->type = $request->input('type') ?? '';
            $magentoProblem->error_body = $decodedErrorMessage ;
            $magentoProblem->status = $request->input('status');
            $magentoProblem->save();

            return response()->json(['message' => 'Magento Problem Stored Successfully'], 200);
        } catch (\Exception $e) {
            Log::channel('magento_problem_error')->error($e->getMessage());

            return response()->json(['message' => 'An error occurred. Please check the logs.'], 500);
        }
    }

    public function magentoProblemStatusCreate(Request $request)
    {
        try {
            $status = new MagentoProblemStatus();
            $status->status_name = $request->status_name;
            $status->save();

            return response()->json(['code' => 200, 'message' => 'status Create successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
}
