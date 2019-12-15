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
        $logListMagentos = LogListMagento::orderBy('created_at', 'DESC');

        // Filter
        if ( !empty($request->product_id) ) {
            $logListMagentos->where('product_id', 'LIKE', '%' . $request->product_id . '%');
        }

        // Get paginated result
        $logListMagentos = $logListMagentos->paginate(25);

        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.listmagento_data', compact('logListMagentos'))->render(),
                'links' => (string)$logListMagentos->render()
            ], 200);
        }

        // Show results
        return view('logging.listmagento', compact('logListMagentos'));
    }
}
