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
use App\ScrapRequestHistory;
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
use App\User;
use App\Helpers;

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

        \Log::channel('scraper')->debug("##!!##".json_encode($request->all())."##!!##");

        // Update request data with common mistakes
        $request = ProductHelper::fixCommonMistakesInRequest($request);

        // Log before validating
        $errorLog = LogScraper::LogScrapeValidationUsingRequest($request);

        // Return error
        if (!empty($errorLog["error"])) {
            return response()->json([
                'error' => $errorLog["error"]
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
                // if brand is not then create a brand
                $brand = Brand::create([
                    "name" => $request->get('brand')
                ]);
            }
        }


        // fix property array
        $requestedProperties = $request->get('properties');
        if(!empty($requestedProperties)) {
            foreach ($requestedProperties as $key => &$value) {
                if($key == "category") {
                   $requestedProperties[$key] = !is_array($value) ? [$value] : $value;
                }
            }
        }
        $request->request->add(["properties" => $requestedProperties]);

        $categoryForScrapedProducts = '';
        $colorForScrapedProducts = '';
        $compositionForScrapedProducts = '';
        
        // remove categories if it is matching with sku
        $propertiesExt = $request->get('properties');
        if(isset($propertiesExt["category"])) {
            if(is_array($propertiesExt["category"])){
                
                $categories = array_map("strtolower", $propertiesExt["category"]);
                $strsku     =  strtolower($sku);
                
                if(in_array($strsku, $categories)) {
                   $index = array_search($strsku, $categories);
                   unset($categories[$index]);
                }
                //category for scrapper
                if(is_array($categories)){
                    $categoryForScrapedProducts = implode(',',$categories);
                }else{
                    $categoryForScrapedProducts = $categories;
                }
                
                $propertiesExt["category"] = $categories;
            }else{
                $propertiesExt["category"] = '';
            }
        }

        //color for scraperProducts for
        if(isset($propertiesExt['color'])){
            if(is_array($propertiesExt['color'])){
                $colorForScrapedProducts = implode(',',$propertiesExt['color']);
            }else{
                $colorForScrapedProducts = $propertiesExt['color'];
            }
        }

        //compostion for scraped Products
        if(isset($propertiesExt['material_used'])){
            if(is_array($propertiesExt['material_used'])){
                $compositionForScrapedProducts = implode(',',$propertiesExt['material_used']);
            }else{
                $compositionForScrapedProducts = $propertiesExt['material_used'];
            }
        }

        
        // Get this product from scraped products
        $scrapedProduct = ScrapedProducts::where('sku', $sku)->where('website', $request->get('website'))->first();
        $images = $request->get('images') ?? [];

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
            $scrapedProduct->images = $images;
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
            $scrapedProduct->validated = empty($errorLog["error"]) ? 1 : 0;
            $scrapedProduct->validation_result = $errorLog["error"].$errorLog["warning"];
            $scrapedProduct->category = isset($request->properties[ 'category' ]) ? serialize($request->properties[ 'category' ]) : null;
            $scrapedProduct->categories = $categoryForScrapedProducts;
            $scrapedProduct->color = $colorForScrapedProducts;
            $scrapedProduct->composition = $compositionForScrapedProducts;
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
            $scrapedProduct->validation_result = $errorLog["error"].$errorLog["warning"];
            //adding new fields
            $scrapedProduct->categories = $categoryForScrapedProducts;
            $scrapedProduct->color = $colorForScrapedProducts;
            $scrapedProduct->composition = $compositionForScrapedProducts;
            $scrapedProduct->save();
        }
        
        //Saving to Log Scrapper
        /*$objLogScraper = new LogScraper();      
        
        $objLogScraper->website  = $request->get('website');
        $objLogScraper->url = $request->get('url');
        $objLogScraper->sku = $sku;
        $objLogScraper->original_sku = trim($request->get('sku'));
        
        $objLogScraper->brand = $brand->id;
        $objLogScraper->category = isset($request->properties[ 'category' ]) ? serialize($request->properties[ 'category' ]) : null;;
        $objLogScraper->title = ProductHelper::getRedactedText($request->get('title') ?? 'N/A', 'name');
        $objLogScraper->description = ProductHelper::getRedactedText($request->get('description'), 'short_description');
        $objLogScraper->properties = isset($request->properties[ 'category' ]) ? serialize($request->properties[ 'category' ]) : null;
        $objLogScraper->images = isset($images) ? serialize($images) : null;
        $objLogScraper->currency = ProductHelper::getCurrency($request->get('currency'));
        $objLogScraper->price = (float)$request->get('price');
        $objLogScraper->discounted_price = $request->get('discounted_price');
        $objLogScraper->is_sale = $request->get('is_sale') ?? 0;
        $objLogScraper->validated = empty($errorLog) ? 1 : 0;
        $objLogScraper->validation_result = $errorLog["error"].$errorLog["warning"];
        
        $objLogScraper->save();*/


        $scrap_details = Scraper::where(['scraper_name' => $request->get('website')])->first();
        $this->saveScrapperRequest($scrap_details, $errorLog);

        // Create or update product
        app(ProductsCreator::class)->createProduct($scrapedProduct);

        // Return response
        return response()->json([
            'status' => 'Added items successfully!'
        ]);
    }

    public function saveScrapperRequest($scrap_details, $errorLog)
    {
        try {
            //check if scraper of same id have records with same day , then only update the end time
            $check_history = ScrapRequestHistory::where(['scraper_id' => $scrap_details->id , 'start_date' => Carbon::now()])->firstOrFail();
            //update the request data
            ScrapRequestHistory::where(['scraper_id' => $scrap_details->id])->update([
                'end_time' => Carbon::now(),
                'request_sent' => empty($errorLog) ? intval($check_history->request_sent + 1) : intval($check_history->request_sent),
                'request_failed' => empty($errorLog) ? intval($check_history->request_failed) : intval($check_history->request_failed + 1)
            ]);
        }
        catch (\Exception $e) {
            if($scrap_details) {
                ScrapRequestHistory::create([
                    'scraper_id' => $scrap_details->id,
                    'date' => Carbon::now(),
                    'start_time' => Carbon::now(),
                    'end_time' => Carbon::now(),
                    'request_sent' => empty($errorLog) ? 1 : 0,
                    'request_failed' => empty($errorLog) ? 0 : 1
                ]);
            }
        }
        return true;
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
        //$request->website = 'internal_scraper';

        // Log before validating
        //LogScraper::LogScrapeValidationUsingRequest($request);

        // Find product
        $product = Product::find($request->get('id'));

        // Return false if no product is found
        if ($product == null) {
            return response()->json([
                'status' => 'Error processing your request (#1)'
            ], 400);
        }

        if(isset($request->status)){

            // Search For ScraperQueue
            ScrapeQueues::where('done', 0)->where('product_id', $product->id)->update(['done' => 2]);
            $product->status_id = StatusHelper::$unableToScrape;
            $product->save();
            return response()->json([
                'status' => 'Product processed for unable to scrap'
            ]);
        }
        
        // Set product to unable to scrape - will be updated later if we have info
        $product->status_id = in_array($product->status_id,[StatusHelper::$isBeingScraped, StatusHelper::$requestForExternalScraper]) ? StatusHelper::$unableToScrape : StatusHelper::$unableToScrapeImages;
        $product->save();

        // Validate request
        $validator = Validator::make($request->toArray(), [
            'id' => 'required',
            //'website' => 'required',
            'images' => 'required|array',
            'description' => 'required'
        ]);

        // Return an error if the validator fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        // Set proper website name
        //$website = str_replace(' ', '', $request->get('website'));

        // If product is found, update it
        if ($product) {
            
            // clear the request using for the new scraper
            $propertiesArray = [
                "material_used" => $request->get('composition'),
                "color" => $request->get('color'),
                "sizes" => $request->get('sizes'),
                "category" => $request->get('category'),
                "dimension" => $request->get('dimension'),
                "country" => $request->get('country')
            ];

            $formatter = (new \App\Services\Products\ProductsCreator)->getGeneralDetails($propertiesArray);

            $color = \App\ColorNamesReference::getColorRequest($formatter['color'],$request->get('url'),$request->get('title'),$request->get('description'));
            $composition = $formatter['composition'];
            if(!empty($formatter['composition'])) {
                $composition = \App\Compositions::getErpName($formatter['composition']);
            }

            $description = $request->get('description');
            if(!empty($request->get('description'))) {
                $description = \App\DescriptionChange::getErpName($request->get('description'));
            }
            
            // Set basic data
            if(empty($product->name)) {
                $product->name = $request->get('title');
            }

            if(empty($product->short_description)) {
                $product->short_description = $description;
            }
            
            if(empty($product->composition)) {
                $product->composition = $composition;
            }

            if(empty($product->color)) {
                $product->color = $color;
            }

            if(empty($product->description_link)) {
                $product->description_link = $request->get('url');
            }

            if(empty($product->made_in)) {
                $product->made_in = $formatter['made_in'];
            }

            if(empty($product->category)) {
                $product->category = $formatter['category'];
            }
            
            // if size is empty then only update
            if(empty($product->size)) {
                $product->size = $formatter['size'];
                $product->size_eu = $formatter['size'];
            }
            if ((int)$product->price == 0) {
                $product->price = $request->get('price');
            }
            $product->listing_remark = 'Original SKU: ' . $request->get('sku');

            // Set optional data
            if (!$product->lmeasurement) {
                $product->lmeasurement = $formatter['lmeasurement'];
            }
            if (!$product->hmeasurement) {
                $product->hmeasurement = $formatter['hmeasurement'];
            }
            if (!$product->dmeasurement) {
                $product->dmeasurement = $formatter['dmeasurement'];;
            }

            $product->status_id = StatusHelper::$autoCrop;
            // Save
            $product->save();

            // Check if we have images
            $product->attachImagesToProduct($request->get('images'));


            if($request->website) {
                $supplierModel = Supplier::leftJoin("scrapers as sc", "sc.supplier_id", "suppliers.id")->where(function ($query) use ($request) {
                    $query->where('supplier', '=', $request->website)->orWhere('sc.scraper_name', '=', $request->website);
                })->first();

                if($supplierModel) {
                    $productSupplier = \App\ProductSupplier::where("supplier_id",$supplierModel->id)->where("product_id",$product->id)->first();
                    if(!$productSupplier)  {
                        $productSupplier = new \App\ProductSupplier;
                        $productSupplier->supplier_id = $supplierModel->id;
                        $productSupplier->product_id = $product->id;
                    }

                    $productSupplier->title = $request->title;
                    $productSupplier->description = $description;
                    $productSupplier->supplier_link = $request->url;
                    $productSupplier->stock = 1;
                    $productSupplier->price = ($product->price > 0) ? $product->price : 0;
                    $productSupplier->size = $formatter[ 'size' ];
                    $productSupplier->color = isset($formatter[ 'color' ]) ? $formatter[ 'color' ]  : "";
                    $productSupplier->composition = isset($formatter[ 'composition' ]) ? $formatter[ 'composition' ] : "";
                    $productSupplier->sku = $request->sku;
                    $productSupplier->save();
                }
            }

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
            $scraper = Scraper::where('scraper_name', $website)->first();
            if(!empty($scraper)){
               if($scraper->full_scrape == 1){
                    $scraper->full_scrape = 0;
                    $scraper->save();
                    return $links;
                } 
            }
            

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
        $users = Helpers::getUserArray(User::role('Developer')->get());
        if ($request->website || $request->url || $request->sku || $request->title || $request->price || $request->created || $request->brand || $request->updated || $request->currency == 0 || $request->orderCreated || $request->orderUpdated || $request->columns || $request->color || $request->psize || $request->category || $request->product_id || $request->dimension || $request->prod_img_filter || $request->prod_error_filter) {
            //DB::enableQueryLog();
            $query = \App\ScrapedProducts::query();

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
            if(request('prod_img_filter') != null && request('prod_img_filter') == '0' ){
                $query->whereRaw('( JSON_EXTRACT(images, "$")  like "%.jpg%" or  JSON_EXTRACT(images, "$")  like "%.png%" or JSON_EXTRACT(images, "$") like "%.jpeg%" or JSON_EXTRACT(images, "$") like "%.gif%")');
            }elseif( request('prod_img_filter') != null && request('prod_img_filter') == '1'){
                $query->whereRaw('not( JSON_EXTRACT(images, "$")  like "%.jpg%" or  JSON_EXTRACT(images, "$")  like "%.png%" or JSON_EXTRACT(images, "$") like "%.jpeg%" or JSON_EXTRACT(images, "$") like "%.gif%")');
            }

            if(request('prod_error_filter') != null && request('prod_error_filter') == '0' ){
                $query->where('validation_result','!=',null);
            }elseif( request('prod_error_filter') != null && request('prod_error_filter') == '1'){
                $query->where('validation_result','=',null);
            }
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
            
            if (request('color') != null) {
                $query->whereRaw('JSON_EXTRACT(properties, \'$.color\') like "%'.$request->color.'%"');
            }

            if (request('category') != null) {
                $query->whereRaw('JSON_EXTRACT(properties, \'$.category\') like "%'.$request->category.'%"');
            }

            if (request('psize') != null) {
                $query->whereRaw('JSON_EXTRACT(properties, \'$.sizes\') like "%'.$request->psize.'%" OR JSON_EXTRACT(properties, \'$.size\') like "%'.$request->psize.'%"');
            }

            if (request('dimension') != null) {
                $query->whereRaw('JSON_EXTRACT(properties, \'$.dimension\') like "%'.$request->dimension.'%"');
            }

            if (request('product_id') != null) {
                $productIds = explode(",", $request->product_id);
                $query->whereIn('product_id', $productIds);
            }
            
            if (request('created') != null) {
                $query->whereDate('created_at', Carbon::parse($request->created)->format('Y-m-d'));
            }

            if (request('brand') != null) {
                $suppliers = request('brand');
                $query->whereIn('brand_id', $suppliers);
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
                $search[] = \DB::raw("DATE_FORMAT(scraped_products.created_at, '%Y-%m-%d') as date");
            }else{
                $search[] = \DB::raw("'All' as date");
            }

            $totalUniqueSkuRecords = \DB::table("scraped_products")->leftJoin("products as p",function($q){
                $q->on("p.id","scraped_products.product_id")->where('stock','>=',1);
            });

            if(!empty($startDate)) {
                $totalUniqueSkuRecords->whereDate('scraped_products.created_at'," >= " , $startDate);
            }

            if(!empty($endDate)) {
                $totalUniqueSkuRecords->whereDate('scraped_products.created_at'," <= " , $endDate);
                $totalUniqueSkuRecords->groupBy(\DB::raw('DATE_FORMAT(scraped_products.created_at, "%Y-%m-%d")'));
            }

            $totalUniqueSkuRecords->select($search);
            $summeryRecords = $totalUniqueSkuRecords->get();

            $response = request()->except(['page']);
            if(empty($response['columns'])) {
                $response['columns'] = ['color','category','size','dimension'];
            }            
        } else {
            $response = '';
            $paginate = (Setting::get('pagination') * 10);


            $logs = LogScraper::orderby('updated_at', 'desc')->paginate($paginate);

        }
        //dd(DB::getQueryLog());
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('scrap.partials.scraped_url_data', compact('logs', 'response','summeryRecords','users'))->render(),
                'links' => (string)$logs->render(),
                'count' => $logs->total(),
            ], 200);
        }

        return view('scrap.scraped_url', compact('logs', 'response','summeryRecords','users'));
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
        $scraper->full_scrape = !empty($request->full_scrape) ? $request->full_scrape : "";
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

        //$scraper = Scraper::whereRaw('(scrapers.start_time IS NULL OR scrapers.start_time < "2000-01-01 00:00:00" OR (scrapers.start_time < scrapers.end_time AND scrapers.end_time < DATE_SUB(NOW(), INTERVAL scrapers.run_gap HOUR)))')->where('time_out','>',0)->first();

        $scraper = Scraper::where("id",23)->first();

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

        return response()->json(
            array(
                'id' => $scraper->id,
                "website" => $scraper->scraper_name,
                "timeout" => $scraper->time_out,
                "starting_urls" => $startingURLs,
                "designer_url_selector" => $scraper->designer_url_selector,
                "product_url_selector" => $scraper->product_url_selector,
                "map" => $mapArray
            )
        );

        

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

    public function scraperFullScrape(Request $request)
    {
       $scraper = Scraper::find($request->id);
       if(!empty($scraper)){
            $scraper->full_scrape = $request->value;
            $scraper->save();
       }
       return response()->json(['success'],200);
    }

    /**
     * Store scraper starting time
     */
    public function scraperReady(Request $request)
    {
        $scraper = Scraper::where('scraper_name', $request->scraper_name)->first();
        if(!empty($scraper)) {
                $scraper->last_started_at = Carbon::now();
                $scraper->save();
        }
       return response()->json(['success'],200);
    }

    /**
     * Store scraper completed time
     */
    public function scraperCompleted(Request $request)
    {
        $scraper = Scraper::where('scraper_name', $request->scraper_name)->first();
        if(!empty($scraper)) {
                $scraper->last_completed_at = Carbon::now();
                $scraper->save();
        }
       return response()->json(['success'],200);
    }

    public function needToStart(Request $request)
    {
        if($request->server_id != null) {

            $totalScraper = [];
            $scrapers = Scraper::select('parent_id','scraper_name')->where('server_id', $request->server_id)->where("scraper_start_time",\DB::raw("HOUR(now())"))->get();
            foreach($scrapers as $scraper){
                if(!$scraper->parent_id){
                    $totalScraper[] = $scraper->scraper_name;
                }else{
                    $totalScraper[] = $scraper->parent->scraper_name.'/'.$scraper->scraper_name;
                }
                
            }
            //dd($scraper);
            return response()->json(["code" => 200, "data" => $totalScraper, "message" => ""]);
        }else{
            return response()->json(["code" => 500, "message" => "Please send server id"]);
        }
    }

    public function scraperNeeded(Request $request)
    {
       $products = Product::where("status_id", StatusHelper::$requestForExternalScraper)
        ->latest("created_at")
        ->select(["id","sku","supplier"])
        ->limit(50)
        ->get()
        ->toArray(); 
        
        return response()->json($products);
    }

    public function restartNode(Request $request)
    {
        if($request->name && $request->server_id){
            $scraper = Scraper::where('scraper_name',$request->name)->first();
            if(!$scraper->parent_id){
                $name = $scraper->scraper_name;
            }else{
                $name = $scraper->parent->scraper_name.'/'.$scraper->scraper_name;
            }

            $url = 'http://'.$request->server_id.'.theluxuryunlimited.com:'.env('NODE_SERVER_PORT').'/restart-script?filename='.$name.'.js';
            //dd($url);
            //sample url
            //localhost:8085/restart-script?filename=biffi.js
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            curl_close($curl);
            if($response){
                return response()->json(["code" => 200,"message" => "Script Restarted"]);
            }else{
                return response()->json(["code" => 500,"message" => "Check if Server is running"]);
            }
            
        }
    }

    public function getStatus(Request $request)
    {
        if($request->name && $request->server_id){
            $scraper = Scraper::where('scraper_name',$request->name)->first();
            if(!$scraper->parent_id){
                $name = $scraper->scraper_name;
            }else{
                $name = $scraper->parent->scraper_name.'/'.$scraper->scraper_name;
            }

            $url = 'http://'.$request->server_id.'.theluxuryunlimited.com:'.env('NODE_SERVER_PORT').'/process-list?filename='.$name.'.js';


            //sample url
            //localhost:8085/restart-script?filename=biffi.js
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            curl_close($curl);
            if($response){
                $re = '/\d+/m';
                $str = $response;
                preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
                
                if(count($matches) == 2 || count($matches) == 1 || count($matches) == 0){
                    return response()->json(["code" => 200,"message" => "Script Is Not Running"]);
                }else{
                    return response()->json(["code" => 200,"message" => "Script Is Running"]);
                }
               
            }else{
                return response()->json(["code" => 500,"message" => "Check if Server is running"]);
            }
            
        }
    }


    public function saveChildScraper(Request $request)
    {
        $scraper = Scraper::where('scraper_name',$request->scraper_name)->whereNull('parent_id')->first();
        //dd($scraper);
        if($scraper){
            $parentId = $scraper->id;
            $checkIfChildScraperExist = Scraper::where('parent_id',$parentId)->where('scraper_name',$request->name)->first();
            if(!$checkIfChildScraperExist){
                $scraperChild = new Scraper;
                $scraperChild->scraper_name = $request->name;
                $scraperChild->supplier_id =  $scraper->supplier_id;
                $scraperChild->parent_id = $parentId;
                $scraperChild->run_gap = $request->run_gap;
                $scraperChild->start_time = $request->start_time;
                $scraperChild->scraper_made_by = $request->scraper_made_by;
                $scraperChild->server_id = $request->server_id;
                $scraperChild->save();
            }else{
                 return redirect()->back()->with('message', 'Scraper Already Exist');
            }
                return redirect()->back()->with('message', 'Child Scraper Saved');
        }
                return redirect()->back()->with('message', 'Scraper Not Found');
          
            
        
    }

    public function assignScrapProductTask(Request $request){
        $requestData = new Request();
        $requestData->setMethod('POST');
        $requestData->request->add([
            'priority' => 1,
            'issue' => $request->message,// issue detail  
            'status' => "Planned",
            'module' => "Scraper", 
            'subject' => $request->subject,// enter issue name  
            'assigned_to' => 6
        ]);
        app('App\Http\Controllers\DevelopmentController')->issueStore($requestData, 'issue');
        return redirect()->back();
    }
}

