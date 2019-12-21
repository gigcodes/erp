<?php

namespace App\Http\Controllers\Logging;

use App\Loggers\LogScraper;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;

class LogScraperController extends Controller
{
    public function index(Request $request)
    {
        $scraperLogs = DB::table('log_scraper')->where('validated', 0);

        if (!empty($request->website)) {
            $scraperLogs = $scraperLogs->where('website', 'LIKE', '%' . $request->website . '%');
        }

        if (!empty($request->result)) {
            $scraperLogs = $scraperLogs->where('validation_result', 'LIKE', '%' . $request->result . '%');
        }

        $scraperLogs = $scraperLogs->orderBy('created_at', 'DESC')->paginate(25);

        return view('log.scraper', compact('scraperLogs'));
    }

    public function logSKU(Request $request)
    {
    
         $logScrapper = LogScraper::query();

        // Filters
        if (!empty($request->product_id)) {
            $logScrapper->where('product_id', 'LIKE', '%' . $request->product_id . '%');
        }

        if (!empty($request->sku)) {
            $logScrapper->where('products.sku', 'LIKE', '%' . $request->sku . '%');
        }

        if (!empty($request->brand)) {
            $logScrapper->where('brands.name', 'LIKE', '%' . $request->brand . '%');
        }

        if (!empty($request->category)) {
            $logScrapper->where('categories.title', 'LIKE', '%' . $request->category . '%');
        }

        // Get paginated result
        $logScrappers = $logScrapper->paginate(25);
        
        $selected_categories = $request->category ? $request->category : 1;

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2'])
            ->selected($selected_categories)
            ->renderAsDropdown();

        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.listsku_data', compact('logScrapper','category_selection'))->render(),
                'links' => (string)$logScrapper->render()
            ], 200);
        }

        

        // Show results
        return view('logging.product-sku', compact('logScrappers','category_selection'));
    
    }
}
