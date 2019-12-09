<?php

namespace App\Http\Controllers;

use App\ScrapeQueues;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Brand;
use App\Category;
use App\Helpers\ProductHelper;
use App\Image;
use App\Imports\ProductsImport;
use App\Product;
use App\ScrapCounts;
use App\ScrapedProducts;
use App\ScrapEntries;
use App\ScrapActivity;
use App\ScrapStatistics;
use App\ScraperResult;
use App\Services\Products\AttachSupplier;
use App\Services\Scrap\GoogleImageScraper;
use App\Services\Scrap\PinterestScraper;
use App\Services\Products\GnbProductsCreator;
use App\Supplier;
use App\Loggers\LogScraper;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Storage;
use Carbon\Carbon;
use App\Services\Products\ProductsCreator;
use App\Setting;
use App\Helpers\StatusHelper;
use Validator;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;


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

        // Get this product from scraped products
        $scrapedProduct = ScrapedProducts::where('sku', $sku)->where('website', $request->get('website'))->first();

        if ($scrapedProduct) {
            // Add scrape statistics
            $scrapStatistics = new ScrapStatistics();
            $scrapStatistics->supplier = $request->get('website');
            $scrapStatistics->type = 'EXISTING_SCRAP_PRODUCT';
            $scrapStatistics->brand = $brand->name;
            $scrapStatistics->url = $request->get('url');
            $scrapStatistics->description = $request->get('sku');
            $scrapStatistics->save();

            // Set values for existing scraped product
            $scrapedProduct->url = $request->get('url');
            $scrapedProduct->properties = $request->get('properties');
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
            $scrapedProduct->save();
            $scrapedProduct->touch();
        } else {
            // Add scrape statistics
            $scrapStatistics = new ScrapStatistics();
            $scrapStatistics->supplier = $request->get('website');
            $scrapStatistics->type = 'NEW_SCRAP_PRODUCT';
            $scrapStatistics->brand = $brand->name;
            $scrapStatistics->url = $request->get('url');
            $scrapStatistics->description = $request->get('sku');
            $scrapStatistics->save();

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
            $scrapedProduct->properties = $request->get('properties');
            $scrapedProduct->currency = ProductHelper::getCurrency($request->get('currency'));
            $scrapedProduct->price = (float)$request->get('price');
            if ($request->get('currency') == 'EUR') {
                $scrapedProduct->price_eur = (float)$request->get('price');
            }
            $scrapedProduct->last_inventory_at = Carbon::now()->toDateTimeString();
            $scrapedProduct->website = $request->get('website');
            $scrapedProduct->brand_id = $brand->id;
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

        if (is_string($links)) {
            $links = json_decode($links);
        }

        if (is_array($links)) {
            foreach ($links as $link) {
                $logScraper = LogScraper::where('url', $link)->where('website', $request->website)->first();

                if ($logScraper != null) {
                    Log::channel('productUpdates')->debug("[log_scraper] Found existing product with url " . $link);
                    $logScraper->touch();
                    $logScraper->save();

                    // Load scraped product and update last_inventory_at
                    $scrapedProduct = ScrapedProducts::where('sku', ProductHelper::getSku($logScraper->sku))->where('website', $request->website)->first();

                    if ($scrapedProduct != null) {
                        Log::channel('productUpdates')->debug("[scraped_product] Found existing product with sku " . ProductHelper::getSku($logScraper->sku));
                        $scrapedProduct->url = $link;
                        $scrapedProduct->last_inventory_at = Carbon::now();
                        $scrapedProduct->save();
                    } else {
                        $pendingUrl[] = $link;
                    }
                } else {
                    $pendingUrl[] = $link;
                }
            }

            //Getting Supplier by Scraper name
            try {
                $scraper = Supplier::where('scraper_name', $request->website)->first();
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
                $scraperResult->scraper_name = $request->website;
                $scraperResult->total_urls = $totalLinks;
                $scraperResult->existing_urls = $existingLinks;
                $scraperResult->new_urls = $pendingLinks;
                $scraperResult->save();

            } catch (Exception $e) {

            }

        }

        return $pendingUrl;
    }


    public function scrapedUrls(Request $request){

         if ($request->website || $request->url || $request->sku || $request->title || $request->price || $request->created || $request->brand || $request->updated ||$request->currency == 0) {

            $query = LogScraper::query();

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

            $paginate = (Setting::get('pagination') * 10);
            $logs = $query->orderby('updated_at','desc')->paginate($paginate);
        }
        else {

             $paginate = (Setting::get('pagination') * 10);
            $logs = LogScraper::orderby('updated_at','desc')->paginate($paginate);

        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('scrap.partials.scraped_url_data', compact('logs'))->render(),
                'links' => (string)$logs->render()
            ], 200);
            }

        return view('scrap.scraped_url',compact('logs'));
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
}
