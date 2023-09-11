<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MagentoProblem;

class MagentoProblemController extends Controller
{
    public function index(Request $request)
    {

        $magentoProblems = new MagentoProblem();

        if ($request->search_source) {
            $magentoProblems = $magentoProblems->where('source', 'LIKE', '%' . $request->search_source . '%');
        }
        if ($request->search_test) {
            $magentoProblems = $magentoProblems->Where('test', $request->search_test);
        }
        if ($request->search_severity) {
            $magentoProblems = $magentoProblems->Where('severity', $request->search_severity);
        }
        if ($request->error_body) {
            $magentoProblems = $magentoProblems->Where('error_body', $request->error_body);
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

        $magentoProblems = $magentoProblems->latest()->paginate(\App\Setting::get('pagination', 10));

        return view('magento-problems.index', compact('magentoProblems'));

    }
}
