<?php

namespace App\Http\Controllers\Logging;

use App\ListingPayments;
use App\Loggers\LogListMagento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogListMagentoController extends Controller
{
    public function index(Request $request)
    {
        // Get results
        $logListMagentos = LogListMagento::orderBy('created_at', 'DESC')->paginate(25);

        // Show results
        return view('logging.listmagento', compact('logListMagentos'));
    }
}
