<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use App\Image;
use App\Imports\ProductsImport;
use App\Loggers\LogScraper;
use App\Product;
use App\ScrapActivity;
use App\ScrapCounts;
use App\ScrapedProducts;
use App\ScrapEntries;
use App\ScrapeQueues;
use App\Scraper;
use App\ScraperResult;
use App\ScrapStatistics;
use App\Services\Products\AttachSupplier;
use App\Services\Products\GnbProductsCreator;
use App\Services\Products\ProductsCreator;
use App\Services\Scrap\GoogleImageScraper;
use App\Services\Scrap\PinterestScraper;
use App\Setting;
use App\ScraperMapping;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Storage;
use Validator;

class ScrapController extends Controller
{
    private $googleImageScraper;
    private $pinterestScraper;
    private $gnbCreator;

    public function __construct(GoogleImageScraper $googleImageScraper, PinterestScraper $pinterestScraper, GnbProductsCreator $gnbCreator)
    {
        $this->googleImageScraper = $googleImageScraper;
        $this->pinterestScraper = $pinterestScraper;
        $this->gnbCreator = $gnbCreator;
    }

    public function index()
    {
        return view('scrap.index');
    }

    public function scrapGoogleImages(Request $request)
    {
        $this->validate($request, [
            'query' => 'required',
            'noi' => 'required',
        ]);

        $q = $request->get('query');
        $noi = $request->get('noi');
        $chip = $request->get('chip');

        $pinterestData = [];
        $googleData = [];

        if ($request->get('pinterest') === 'on') {
            $pinterestData = $this->pinterestScraper->scrapPinterestImages($q, $chip, $noi);
        }

        if ($request->get('google') === 'on') {
            $googleData = $this->googleImageScraper->scrapGoogleImages($q, $chip, $noi);
        }

        return view('scrap.extracted_images', compact('googleData', 'pinterestData'));

    }

    public function downloadImages(Request $request)
    {
        $this->validate($request, [
            'data' => 'required|array'
        ]);
        $data = $request->get('data');

        $images = [];

        foreach ($data as $key => $datum) {
            try {
                $imgData = file_get_contents($datum);
            } catch (\Exception $exception) {
                continue;
            }

            $fileName = md5(time()) . '.png';
            Storage::disk('uploads')->put('social-media/' . $fileName, $imgData);

            $i = new Image();
            $i->filename = $fileName;
            $i->save();

            $images[] = $fileName;
        }

        $downloaded = true;


        return view('scrap.extracted_images', compact('images', 'downloaded'));

    }

    public function syncProductsFromNodeApp(Request $request)
    {

        // Update request data with common mistakes
        $request = ProductHelper::fixCommonMistakesInRequest($request);

        // Log before validating
        $errorLog = LogScraper::LogScrapeValidationUsingRequest($request);

        // Return error
        if (!empty($errorLog)) {
            return response()->json([
                'error' => $errorLog
            ]);
        }

        // Validate input
        $this->validate($request, [
            'sku' => 'required|min:5',
            'url' => 'required',
            'images' => 'required|array',
            'properties' => 'required',
            'website' => 'required',
            'price' => 'required',
            'brand' => 'required'
        ]);

        // Get SKU
        $sku = ProductHelper::getSku($request->get('sku'));

        // Get brand
        $brand = Brand::where('name', $request->get('brand'))->first();

        // No brand found?
        if (!$brand) {
            // Check for reference
            $brand = Brand::where('references', 'LIKE', '%' . $request->get('brand') . '%')->first();

            if (!$brand) {
                return response()->json([
                    'status' => 'invalid_brand'
                ]);
            }
        }
        // remove categories if it is matching with sku
        $propertiesExt = $request->get('properties');
        if(isset($propertiesExt["category"])) {
            $categories = array_map("strtolower", $propertiesExt["category"]);
            $strsku     =  strtolower($sku);
            if(in_array($strsku, $categories)) {
               $index = array_search($strsku, $categories);
               unset($categories[$index]);
            }
            $propertiesExt["category"] = $categories;
        }

        // Get this product from scraped products
        $scrapedProduct = ScrapedProducts::where('sku', $sku)->where('website', $request->get('website'))->first();

        if ($scrapedProduct) {
            // Add scrape statistics
            // $scrapStatistics = new ScrapStatistics();
            // $scrapStatistics->supplier = $request->get('website');
            // $scrapStatistics->type = 'EXISTING_SCRAP_PRODUCT';
            // $scrapStatistics->brand = $brand->name;
            // $scrapStatistics->url = $request->get('url');
            // $scrapStatistics->description = $request->get('sku');
            // $scrapStatistics->save();

            // Set values for existing scraped product
            $scrapedProduct->url = $request->get('url');
            $scrapedProduct->properties = $propertiesExt;
            $scrapedProduct->is_sale = $request->get('is_sale') ?? 0;
            $scrapedProduct->title = ProductHelper::getRedactedText($request->get('title'), 'name');
            $scrapedProduct->description = ProductHelper::getRedactedText($request->get('description'), 'short_description');
            $scrapedProduct->brand_id = $brand->id;
            $scrapedProduct->currency = $request->get('currency');
            $scrapedProduct->price = (float)$request->get('price');
            if ($request->get('currency') == 'EUR') {
                $scrapedProduct->price_eur = (float)$request->get('price');
            }
            $scrapedProduct->discounted_price = $request->get('discounted_price');
            $scrapedProduct->original_sku = trim($request->get('sku'));
            $scrapedProduct->last_inventory_at = Carbon::now()->toDateTimeString();
            $scrapedProduct->validated = empty($errorLog) ? 1 : 0;
            $scrapedProduct->validation_result = $errorLog;
            $scrapedProduct->category = isset($request->properties[ 'category' ]) ? serialize($request->properties[ 'category' ]) : null;
            $scrapedProduct->save();
            $scrapedProduct->touch();
        } else {
            // Add scrape statistics
            // $scrapStatistics = new ScrapStatistics();
            // $scrapStatistics->supplier = $request->get('website');
            // $scrapStatistics->type = 'NEW_SCRAP_PRODUCT';
            // $scrapStatistics->brand = $brand->name;
            // $scrapStatistics->url = $request->get('url');
            // $scrapStatistics->description = $request->get('sku');
            // $scrapStatistics->save();

            // Create new scraped product
            $scrapedProduct = new ScrapedProducts();
            $images = $request->get('images') ?? [];
            $scrapedProduct->images = $images;
            $scrapedProduct->sku = $sku;
            $scrapedProduct->original_sku = trim($request->get('sku'));
            $scrapedProduct->discounted_price = $request->get('discounted_price');
            $scrapedProduct->is_sale = $request->get('is_sale') ?? 0;
            $scrapedProduct->has_sku = 1;
            $scrapedProduct->url = $request->get('url');
            $scrapedProduct->title = ProductHelper::getRedactedText($request->get('title') ?? 'N/A', 'name');
            $scrapedProduct->description = ProductHelper::getRedactedText($request->get('description'), 'short_description');
            $scrapedProduct->properties = $propertiesExt;
            $scrapedProduct->currency = ProductHelper::getCurrency($request->get('currency'));
            $scrapedProduct->price = (float)$request->get('price');
            if ($request->get('currency') == 'EUR') {
                $scrapedProduct->price_eur = (float)$request->get('price');
            }
            $scrapedProduct->last_inventory_at = Carbon::now()->toDateTimeString();
            $scrapedProduct->website = $request->get('website');
            $scrapedProduct->brand_id = $brand->id;
            $scrapedProduct->category = isset($request->properties[ 'category' ]) ? serialize($request->properties[ 'category' ]) : null;
            $scrapedProduct->validated = empty($errorLog) ? 1 : 0;
            $scrapedProduct->validation_result = $errorLog;
            $scrapedProduct->save();
        }

        // Create or update product
        app(ProductsCreator::class)->createProduct($scrapedProduct);

        // Return response
        return response()->json([
            'status' => 'Added items successfuly!'
        ]);
    }

    private function downloadImagesForSites($data, $prefix = 'img'): array
    {

        $images = [];
        foreach ($data as $key => $datum) {
            try {
                $imgData = file_get_contents($datum);
            } catch (\Exception $exception) {
                continue;
            }

            $fileName = $prefix . '_' . md5(time()) . '.png';
            Storage::disk('uploads')->put('social-media/' . $fileName, $imgData);

            $images[] = $fileName;
        }

        return $images;
    }


    public function excel_import()
    {
        $products = ScrapedProducts::where('website', 'EXCEL_IMPORT_TYPE_1')->paginate(25);
        return view('scrap.excel', compact('products'));
    }

    public function excel_store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file'
        ]);

        $file = $request->file('file');

        if ($file->getClientOriginalExtension() == 'xlsx') {
            $reader = new Xlsx();
        } else {
            if ($file->getClientOriginalExtension() == 'xls') {
                $reader = new Xls();
            }
        }

        $spreadsheet = $reader->load($file->getPathname());
        $cells = [];


        $i = 0;
        foreach ($spreadsheet->getActiveSheet()->getDrawingCollection() as $drawing) {
            if ($drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing) {
                ob_start();
                call_user_func(
                    $drawing->getRenderingFunction(),
                    $drawing->getImageResource()
                );
                $imageContents = ob_get_contents();
                ob_end_clean();
                switch ($drawing->getMimeType()) {
                    case \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_PNG :
                        $extension = 'png';
                        break;
                    case \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_GIF:
                        $extension = 'gif';
                        break;
                    case \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_JPEG :
                        $extension = 'jpg';
                        break;
                }
            } else {
                $zipReader = fopen($drawing->getPath(), 'r');
                $imageContents = '';
                while (!feof($zipReader)) {
                    $imageContents .= fread($zipReader, 1024);
                }
                fclose($zipReader);
                $extension = $drawing->getExtension();
            }

            $myFileName = '00_Image_' . ++$i . '.' . $extension;
            file_put_contents('uploads/social-media/' . $myFileName, $imageContents);
            $cells[ substr($drawing->getCoordinates(), 2) ][] = $myFileName;
        }

        $cells_new = [];
        $c = 0;
        foreach ($cells as $cell) {
            $cells_new[ $c ] = $cell;
            $c++;
        }

        $files = Excel::toArray(new ProductsImport(), $file);
        $th = [];

        foreach ($files[ 0 ] as $key => $file) {
            if (
                in_array('MODELLO', $file)
                + in_array('VARIANTE', $file)
                + in_array('COLORE', $file)
                + in_array('GRUPPO', $file)
                + in_array('SETTORE', $file)
                + in_array('DESCRIZIONE', $file)
                + in_array('BRAND', $file)
                + in_array('PR. ACQUISTO', $file)
                + in_array('TESSUTO', $file)
                + in_array('PR. VENDITA', $file)
                + in_array('COD. FOTO', $file)
                >= 4) {
                $th = $file;
                unset($files[ 0 ][ $key ]);
                break;
            }
            unset($files[ 0 ][ $key ]);
        }

        $fields_only_with_keys = [];

        foreach ($th as $key => $file) {
            if ($file) {
                $fields_only_with_keys[ $key ] = $file;
            }
        }

        $dataToSave = [];

        foreach ($files[ 0 ] as $pkey => $row) {
            $null_count = 0;
            foreach ($row as $item) {
                if ($item === null) {
                    $null_count++;
                }
            }
            if ($null_count > 30) {
                unset($files[ 0 ][ $pkey ]);
            }
        }

        $c = 0;
        foreach ($files[ 0 ] as $pkey => $row) {
            foreach ($fields_only_with_keys as $key => $item) {
                $dataToSave[ $pkey ][ $item ] = $row[ $key ];
                if ($item == 'COD. FOTO') {
                    $dataToSave[ $pkey ][ $item ] = $cells_new[ $c ];
                }
            }
            $c++;
        }

        foreach ($dataToSave as $item) {
            $sku = $item[ 'MODELLO VARIANTE COLORE' ] ?? null;
            if (!$sku) {
                continue;
            }

            $brand = Brand::where('name', $item[ 'BRAND' ] ?? 'UNKNOWN_BRAND_FROM_FILE')->first();

            if (!$brand) {
                continue;
            }

            $sp = new ScrapedProducts();
            $sp->website = 'EXCEL_IMPORT_TYPE_1';
            $sp->sku = $sku;
            $sp->has_sku = 1;
            $sp->brand_id = $brand->id;
            $sp->title = $sku;
            $sp->description = $item[ 'description' ] ?? null;
            $sp->images = $item[ 'COD. FOTO' ] ?? [];
            $sp->price = 'N/A';
            $sp->properties = $item;
            $sp->url = 'N/A';
            $sp->is_property_updated = 0;
            $sp->is_price_updated = 0;
            $sp->is_enriched = 0;
            $sp->can_be_deleted = 0;
            $sp->save();
        }

        return redirect()->back()->with('message', 'Excel Imported Successfully!');


    }

    public function saveSupplier(Request $request)
    {
        $this->validate($request, [
            'supplier' => 'required'
        ]);

        $s = Supplier::where('supplier', $request->get('supplier'))->first();

        if ($s) {
            $s->email = $request->get('email');
            $s->save();

            return response()->json([
                'message' => 'Added successfully!'
            ]);
        }

        $params = [
            'supplier' => ucwords($request->get('supplier')),
            'phone' => str_replace('+', '', $request->get('phone')),
            'address' => $request->get('address'),
            'website' => $request->get('website'),
            'email' => $request->get('email'),
            'social_handle' => $request->get('social_handle'),
            'instagram_handle' => $request->get('instagram_handle'),
        ];

        Supplier::create($params);

        return response()->json([
            'message' => 'Added successfully!'
        ]);

    }

    /**
     * Save incoming data from scraper
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveFromNewSupplier(Request $request)
    {
        // Overwrite website
        $request->website = 'internal_scraper';

        // Log before validating
        LogScraper::LogScrapeValidationUsingRequest($request);

        // Find product
        $product = Product::find($request->get('id'));

        // Return false if no product is found
        if ($product == null) {
            return response()->json([
                'status' => 'Error processing your request (#1)'
            ], 400);
        }

        // Set product to unable to scrape - will be updated later if we have info
        $product->status_id = $product->status_id == StatusHelper::$isBeingScraped ? StatusHelper::$unableToScrape : StatusHelper::$unableToScrapeImages;
        $product->save();

        // Validate request
        $validator = Validator::make($request->toArray(), [
            'id' => 'required',
            'website' => 'required',
            'images' => 'required|array',
            'description' => 'required'
        ]);

        // Return an error if the validator fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        // Set proper website name
        $website = str_replace(' ', '', $request->get('website'));

        // If product is found, update it
        if ($product) {
            // Set basic data
            $product->name = $request->get('title');
            $product->short_description = $request->get('description');
            $product->composition = $request->get('material_used');
            $product->color = $request->get('color');
            $product->description_link = $request->get('url');
            $product->made_in = $request->get('country');
            if ((int)$product->price == 0) {
                $product->price = $request->get('price');
            }
            $product->listing_remark = 'Original SKU: ' . $request->get('sku');

            // Set optional data
            if (!$product->lmeasurement) {
                $product->lmeasurement = $request->get('dimension')[ 0 ] ?? '0';
            }
            if (!$product->hmeasurement) {
                $product->hmeasurement = $request->get('dimension')[ 1 ] ?? '0';
            }
            if (!$product->dmeasurement) {
                $product->dmeasurement = $request->get('dimension')[ 2 ] ?? '0';
            }

            // Save
            $product->save();

            // Check if we have images
            $product->attachImagesToProduct($request->get('images'));

            // Update scrape_queues by product ID
            ScrapeQueues::where('done', 0)->where('product_id', $product->id)->update(['done' => 1]);

            // Return response
            return response()->json([
                'status' => 'Product processed'
            ]);
        }

        // Still here? Return error
        return response()->json([
            'status' => 'Error processing your request (#99)'
        ], 400);
    }

    public function processProductLinks(Request $request)
    {
        $pendingUrl = array();
        $links = $request->links;
        $website = $request->website;

        if (empty($website)) {
            $rawJson = json_decode($request->instance()->getContent());
            $website = isset($rawJson->website) ? $rawJson->website : null;
        }
        if (is_string($links)) {
            $links = json_decode($links);
        } else {
            $rawJson = json_decode($request->instance()->getContent());
            $links = isset($rawJson->links) ? $rawJson->links : null;
        }

        if (is_array($links)) {
            foreach ($links as $link) {
                //$logScraper = LogScraper::where('url', $link)->where('website', $website)->first();

                //if ($logScraper != null) {
                    //Log::channel('productUpdates')->debug("[log_scraper] Found existing product with url " . $link);
                    //$logScraper->touch();
                    //$logScraper->save();

                    // Load scraped product and update last_inventory_at
                    $scrapedProduct = ScrapedProducts::where('url', $link)->where('website', $website)->first();

                    if ($scrapedProduct != null) {
                        Log::channel('productUpdates')->debug("[scraped_product] Found existing product with sku " . ProductHelper::getSku($scrapedProduct->sku));
                        $scrapedProduct->url = $link;
                        $scrapedProduct->last_inventory_at = Carbon::now();
                        $scrapedProduct->save();
                    } else {
                        $pendingUrl[] = $link;
                    }
                //} else {
                    //$pendingUrl[] = $link;
                //}
            }

            //Getting Supplier by Scraper name
            try {
                $scraper = Scraper::where('scraper_name', $website)->first();
                $totalLinks = count($links);
                $pendingLinks = count($pendingUrl);
                $existingLinks = ($totalLinks - $pendingLinks);

                if ($scraper != '' && $scraper != null) {
                    $scraper->scraper_total_urls = $totalLinks;
                    $scraper->scraper_existing_urls = $existingLinks;
                    $scraper->scraper_new_urls = $pendingLinks;
                    $scraper->update();
                }

                $scraperResult = new ScraperResult();
                $scraperResult->date = date("Y-m-d");
                $scraperResult->scraper_name = $website;
                $scraperResult->total_urls = $totalLinks;
                $scraperResult->existing_urls = $existingLinks;
                $scraperResult->new_urls = $pendingLinks;
                $scraperResult->save();

            } catch (Exception $e) {

            }

        }

        return $pendingUrl;
    }


    public function scrapedUrls(Request $request)
    {
        $totalSkuRecords       = 0;
        $totalUniqueSkuRecords = 0;

        if ($request->website || $request->url || $request->sku || $request->title || $request->price || $request->created || $request->brand || $request->updated || $request->currency == 0 || $request->orderCreated || $request->orderUpdated || $request->columns) {

            $query = LogScraper::query();

            $dateRange = request("daterange","");
            $startDate = false;
            $endDate   = false;

            if(!empty($dateRange)) {
                $range = explode(" - ", $dateRange);
                if(!empty($range[0]) && !empty($range[1])) {
                    $startDate = $range[0];
                    $endDate   = $range[1];
                }
            }

            //global search website
            if (request('website') != null) {
                $query->whereIn('website', $request->website);
            }

            if (request('url') != null) {
                $query->where('url', 'LIKE', "%{$request->url}%");
            }

            if (request('sku') != null) {
                $query->where('sku', 'LIKE', "%{$request->sku}%");
            }

            if (request('title') != null) {
                $query->where('title', 'LIKE', "%{$request->title}%");
            }

            if (request('currency') != null) {
                $query->where('currency', 'LIKE', "%{$request->currency}%");
            }

            if (request('price') != null) {
                $query->where('price', 'LIKE', "%{$request->price}%");
            }

            if (request('created') != null) {
                $query->whereDate('created_at', request('created'));
            }

            if (request('brand') != null) {
                $suppliers = request('brand');
                $query->whereIn('brand', $suppliers);
            }

            if (request('updated') != null) {
                $query->whereDate('updated_at', request('updated'));
            }

            if(!empty($startDate)) {
                $query->whereDate('created_at'," >= " , $startDate);
            }

            if(!empty($endDate)) {
                $query->whereDate('created_at'," <= " , $endDate);
            }

            if (request('orderCreated') != null) {
                if (request('orderCreated') == 0) {
                    $query->orderby('created_at', 'asc');
                } else {
                    $query->orderby('created_at', 'desc');
                }
            }

            if (request('orderUpdated') != null) {
                if (request('orderUpdated') == 0) {
                    $query->orderby('updated_at', 'asc');
                } else {
                    $query->orderby('updated_at', 'desc');
                }
            }

            if (request('orderCreated') == null && request('orderUpdated') == null) {
                $query->orderby('updated_at', 'desc');
            }

            $paginate = (Setting::get('pagination') * 10);
            $logs = $query->paginate($paginate)->appends(request()->except(['page']));

            $search = [
                \DB::raw("count(*) as total_record"),
                \DB::raw("count(DISTINCT p.sku) as total_u_record")
            ];



            if(!empty($startDate) && !empty($endDate)) {
                $search[] = \DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date");
            }else{
                $search[] = \DB::raw("'All' as date");
            }

            $totalUniqueSkuRecords = \DB::table("log_scraper")->leftJoin("products as p",function($q){
                $q->on("p.sku","log_scraper.sku")->where('stock','>=',1);
            });

            if(!empty($startDate)) {
                $totalUniqueSkuRecords->whereDate('created_at'," >= " , $startDate);
            }

            if(!empty($endDate)) {
                $totalUniqueSkuRecords->whereDate('created_at'," <= " , $endDate);
                $totalUniqueSkuRecords->groupBy(\DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'));
            }

            $totalUniqueSkuRecords->select($search);
            $summeryRecords = $totalUniqueSkuRecords->get();

            $response = request()->except(['page']);
            if(empty($response['columns'])) {
                $response['columns'] = [];
            }

        } else {
            $response = '';
            $paginate = (Setting::get('pagination') * 10);


            $logs = LogScraper::orderby('updated_at', 'desc')->paginate($paginate);

        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('scrap.partials.scraped_url_data', compact('logs', 'response','summeryRecords'))->render(),
                'links' => (string)$logs->render(),
                'count' => $logs->total(),
            ], 200);
        }

        return view('scrap.scraped_url', compact('logs', 'response','summeryRecords'));
    }

    public function getProductsToScrape()
    {
        // Set empty value of productsToPush
        $productsToPush = [];

        // Get all products with status scrape from scrape_queues
        $scrapeQueues = ScrapeQueues::where('done', 0)->orderBy('product_id', 'DESC')->take(50)->get();

        // Check if we have products and loop over them
        if ($scrapeQueues !== null) {
            foreach ($scrapeQueues as $scrapedQueue) {
                // Get product
                $product = Product::find($scrapedQueue->product_id);

                // Add to array
                $productsToPush[] = [
                    'id' => $scrapedQueue->product_id,
                    'sku' => null,
                    'original_sku' => null,
                    'brand' => $product->brands ? $product->brands->name : '',
                    'url' => $scrapedQueue->url,
                    'supplier' => $product->supplier
                ];

                // Update status to is being scraped
                $product->status_id = StatusHelper::$isBeingScrapedWithGoogleImageSearch;
                $product->save();
            }
        }

        // Only run if productsToPush is empty
        if (!is_array($productsToPush) || count($productsToPush) == 0) {
            // Get all products with status scrape
            $products = Product::where('status_id', StatusHelper::$scrape)->where('stock', '>=', 1)->orderBy('products.id', 'DESC')->take(50)->get();

            // Check if we have products and loop over them
            if ($products !== null) {
                foreach ($products as $product) {
                    // Get original SKU
                    $scrapedProduct = ScrapedProducts::where('sku', $product->sku)->first();

                    if ($scrapedProduct != null) {
                        // Add to array
                        $productsToPush[] = [
                            'id' => $product->id,
                            'sku' => $product->sku,
                            'original_sku' => ProductHelper::getOriginalSkuByBrand(!empty($scrapedProduct->original_sku) ? $scrapedProduct->original_sku : $scrapedProduct->sku, $product->brands ? $product->brands->id : 0),
                            'brand' => $product->brands ? $product->brands->name : '',
                            'url' => null,
                            'supplier' => $product->supplier
                        ];

                        // Update status to is being scraped
                        $product->status_id = StatusHelper::$isBeingScraped;
                        $product->save();
                    }
                }
            }
        }

        // Return JSON response
        return response()->json($productsToPush);

    }

    public function genericScraper(Request $request)
    {
        $query = Scraper::query();

        if($request->global != null){
            $query = $query->where('scraper_name', 'LIKE', "%{$request->global}%")
                ->orWhere('product_url_selector', 'LIKE', "%{$request->global}%")
                ->orWhere('designer_url_selector', 'LIKE', "%{$request->global}%")
                ->orWhere('starting_urls', 'LIKE', "%{$request->global}%")
                ->orWhere('run_gap', 'LIKE', "%{$request->global}%")
                ->orWhere('time_out', 'LIKE', "%{$request->global}%")
                ->orWhereHas('mainSupplier', function ($qu) use ($request) {
                    $qu->where('supplier', 'LIKE', "%{$request->global}%");
                });
        }

        if($request->scraper_name != null){
            $query = $query->where('scraper_name', 'LIKE', "%{$request->scraper_name}%");
        }

        if($request->run_gap_search != null){
            $query = $query->where('run_gap', 'LIKE', "%{$request->run_gap_search}%");
        }

        if($request->time_out_search != null){
            $query = $query->where('time_out', 'LIKE', "%{$request->time_out_search}%");
        }

        if($request->starting_url_search != null){
            $query = $query->where('starting_urls', 'LIKE', "%{$request->starting_url_search}%");
        }

        if($request->designer_url_search != null){
            $query = $query->where('designer_url_selector', 'LIKE', "%{$request->designer_url_search}%");
        }

        if($request->product_url_search != null){
            $query = $query->where('product_url_selector', 'LIKE', "%{$request->product_url_search}%");
        }

        if($request->supplier_name != null){
            $query = $query->whereHas('mainSupplier', function ($qu) use ($request) {
                $qu->where('supplier', 'LIKE', "%{$request->supplier_name}%");
            });

        }


        $suppliers = Supplier::where('supplier_status_id',1)->get();
        $scrapers = $query->paginate(25)->appends(request()->except(['page']));;

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('scrap.partials.supplier-scraper-data', compact('scrapers','suppliers'))->render(),
                'links' => (string)$scrapers->render(),
                'count' => $scrapers->total(),
            ], 200);
        }



        return view('scrap.supplier-scraper',compact('scrapers','suppliers'));
    }

    public function genericScraperSave(Request $request){

        if($request->id){
            $scraper = Scraper::find($request->id);
        }else{
            $scraper = new Scraper;
            $scraper->scraper_name = $request->name;
            $scraper->supplier_id = $request->supplier_id;
        }


        $scraper->run_gap = $request->run_gap;
        $scraper->time_out = $request->time_out;
        $scraper->starting_urls = $request->starting_url;
        $scraper->product_url_selector = $request->product_url_selector;
        $scraper->designer_url_selector = $request->designer_url;
        $scraper->save();

        if ($request->ajax()) {
            return response()->json(['success'],200);
        }

        return redirect()->back()->with('message', 'Scraper Saved');
    }

    public function genericMapping($id)
    {
        $scraper = Scraper::find($id);
        $mappings = ScraperMapping::where('scrapers_id',$id)->get();
        return view('scrap.generic-scraper-mapping',compact('scraper','mappings','id'));
    }

    public function genericMappingSave(Request $request)
    {
        $id = $request->id;
        $select = $request->select;
        $count = count($select);
        $functions = $request->functions;
        $parameter = $request->parameter;
        $selector = $request->selector;

        for ($i=0; $i < $count; $i++) {
            if($select[$i] != null){
                $updateMapping = ScraperMapping::where('scrapers_id',$id)->where('field_name',$select[$i])->first();
                if($updateMapping != null){
                    $mapping = $updateMapping;
                }else{
                    $mapping = new ScraperMapping;
                }
                if($selector[$i] == null){
                    $selector[$i] = '';
                }
                if($functions[$i] == null){
                    $functions[$i] = '';
                }
                if($parameter[$i] == null){
                    $parameter[$i] = '';
                }

                $mapping->field_name = $select[$i];
                $mapping->scrapers_id = $id;
                $mapping->selector = $selector[$i];
                $mapping->function = $functions[$i];
                $mapping->parameter = $parameter[$i];
                $mapping->save();
            }
        }

        return response()->json(['success'],200);
    }

    public function sendScrapDetails()
    {

        $scraper = Scraper::whereRaw('(scrapers.start_time IS NULL OR scrapers.start_time < "2000-01-01 00:00:00" OR (scrapers.start_time < scrapers.end_time AND scrapers.end_time < DATE_SUB(NOW(), INTERVAL scrapers.run_gap HOUR)))')->where('time_out','>',0)->first();

        if($scraper == null){
            return response()->json(['message' => 'No Scraper Present'], 400);
        }
        $startingURLs = explode("\n", str_replace("\r", "", $scraper->starting_urls));

        $maps = ScraperMapping::where('scrapers_id',$scraper->id)->get();

        foreach ($maps as $map) {
            $mapArray[]  = array($map->field_name => array('selector' => $map->selector,'function' => $map->function , 'parameters' => $map->parameter));
        }

        if(!isset($mapArray)){
            $mapArray = [];
        }

        $scraper->start_time = now();
        $scraper->save();

        $json = json_encode(array("website" => $scraper->scraper_name , "timeout" => $scraper->time_out , "starting_urls" => $startingURLs , "designer_url_selector" => $scraper->designer_url_selector, "product_url_selector" => $scraper->product_url_selector,"map" => $mapArray));

        return $json;

    }

    public function recieveScrapDetails(Request $request){
        $id = $request->id;
        $scraper = Scraper::find($id);
        if($scraper == null){
            return response()->json(['message' => 'No Scraper Present'], 400);
        }
        $scraper->end_time = now();
        $scraper->save();

        return response()->json(['success'],200);
    }

    public function genericMappingDelete(Request $request){
        $id = $request->id;
        $mapping = ScraperMapping::find($id);
        $mapping->delete();
        return response()->json(['success'],200);
    }
}
