<?php

namespace App\Http\Controllers;

use Storage;
use App\User;
use App\Brand;
use App\Image;
use Validator;
use App\Helpers;
use App\Product;
use App\Scraper;
use App\Setting;
use App\Supplier;
use Carbon\Carbon;
use App\LogRequest;
use App\ScrapApiLog;
use App\ScrapCounts;
use App\ScrapeQueues;
use App\StoreWebsite;
use App\ScraperResult;
use App\ScraperMapping;
use App\ScrapedProducts;
use App\Loggers\LogScraper;
use App\ScrapRequestHistory;
use Illuminate\Http\Request;
use App\Helpers\StatusHelper;
use App\Helpers\ProductHelper;
use App\Imports\ProductsImport;
use App\Loggers\ScrapPythonLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ScrapedProductsLinks;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Scrap\PinterestScraper;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use App\Services\Products\ProductsCreator;
use App\Services\Scrap\GoogleImageScraper;
use App\Models\ScrapedProductsLinksHistory;
use App\Services\Products\GnbProductsCreator;
use Illuminate\Pagination\LengthAwarePaginator;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class ScrapController extends Controller
{
    public function __construct(private GoogleImageScraper $googleImageScraper, private PinterestScraper $pinterestScraper, private GnbProductsCreator $gnbCreator)
    {
    }

    public function index()
    {
        return view('scrap.index');
    }

    public function scrapGoogleImages(Request $request)
    {
        $this->validate($request, [
            'query' => 'required',
            'noi'   => 'required',
        ]);

        $q    = $request->get('query');
        $noi  = $request->get('noi');
        $chip = $request->get('chip');

        $pinterestData = [];
        $googleData    = [];

        if ($request->get('pinterest') === 'on') {
            $pinterestData = $this->pinterestScraper->scrapPinterestImages($q, $chip, $noi);
            if (! is_array($pinterestData)) {
                // Pinterest data is also coming from google
                return redirect()->back()->with('error', 'HTML element is changed in Google.');
            }
        }

        if ($request->get('google') === 'on') {
            $googleData = $this->googleImageScraper->scrapGoogleImages($q, $chip, $noi);
            if (! is_array($googleData)) {
                return redirect()->back()->with('error', 'HTML element is changed in Google.');
            }
        }

        return view('scrap.extracted_images', compact('googleData', 'pinterestData'));
    }

    public function activity()
    {
        $date = Carbon::now()->subDays(7)->format('Y-m-d');

        $links_count = DB::select('
									SELECT site_name, created_at, COUNT(*) as total FROM
								 		(SELECT scrap_entries.site_name, DATE_FORMAT(scrap_entries.created_at, "%Y-%m-%d") as created_at
								  		 FROM scrap_entries
								  		 WHERE scrap_entries.created_at > ?)
								    AS SUBQUERY
								   	GROUP BY created_at, site_name;
							', [$date]);

        $scraped_count = DB::select('
									SELECT website, created_at, COUNT(*) as total FROM
								 		(SELECT scraped_products.website, DATE_FORMAT(scraped_products.created_at, "%Y-%m-%d") as created_at
								  		 FROM scraped_products
								  		 WHERE scraped_products.created_at > ?)
								    AS SUBQUERY
								   	GROUP BY created_at, website;
							', [$date]);

        $products_count = DB::select('
									SELECT website, created_at, COUNT(*) as total FROM
								 		(SELECT scraped_products.website, scraped_products.sku, DATE_FORMAT(scraped_products.created_at, "%Y-%m-%d") as created_at
								  		 FROM scraped_products

                       RIGHT JOIN (
                         SELECT products.sku FROM products
                       ) AS products
                       ON scraped_products.sku = products.sku

								  		 WHERE scraped_products.created_at > ?
                       )

								    AS SUBQUERY
								   	GROUP BY created_at, website;
							', [$date]);

        $activity_data = DB::select('
									SELECT website, status, created_at, COUNT(*) as total FROM
								 		(SELECT scrap_activities.website, scrap_activities.status, DATE_FORMAT(scrap_activities.created_at, "%Y-%m-%d") as created_at
								  		 FROM scrap_activities
								  		 WHERE scrap_activities.created_at > ?)
								    AS SUBQUERY
								   	GROUP BY created_at, website, status;
							', [$date]);

        $data = [];

        $link_entries = ScrapCounts::where('created_at', '>', $date)->orderBy('created_at', 'DESC')->get();

        foreach ($links_count as $item) {
            if ($item->site_name == 'GNB') {
                $item->site_name = 'G&B';
            }

            $data[$item->created_at][$item->site_name]['links'] = $item->total;
        }

        foreach ($scraped_count as $item) {
            $data[$item->created_at][$item->website]['scraped'] = $item->total;
        }

        foreach ($products_count as $item) {
            $data[$item->created_at][$item->website]['created'] = $item->total;
        }

        foreach ($activity_data as $item) {
            $data[$item->created_at][$item->website][$item->status] = $item->total;
        }

        ksort($data);
        $data = array_reverse($data);

        $currentPage  = LengthAwarePaginator::resolveCurrentPage();
        $perPage      = 24;
        $currentItems = array_slice($data, $perPage * ($currentPage - 1), $perPage);

        $data = new LengthAwarePaginator($currentItems, count($data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('scrap.activity', [
            'data'         => $data,
            'link_entries' => $link_entries,
        ]);
    }

    public function downloadImages(Request $request)
    {
        $this->validate($request, [
            'data' => 'required|array',
        ]);
        $data       = $request->get('data');
        $product_id = $request->get('product_id');

        $images = [];

        foreach ($data as $key => $datum) {
            try {
                $imgData = file_get_contents($datum);

                $fileName = md5(time() . microtime()) . '.png';
                Storage::disk('s3')->put('social-media/' . $fileName, $imgData);
                $i           = new Image();
                $i->filename = $fileName;
                if (! empty($product_id)) {
                    $i->product_id = $product_id;
                }
                $i->save();

                $images[] = $fileName;

                $StoreWebsite = StoreWebsite::where('id', 18)->first();
                if ($StoreWebsite) {
                    $media = MediaUploader::fromSource($datum)->toDirectory('product-template-images')->upload();
                    $StoreWebsite->attachMedia($media, ['website-image-attach']);
                }
            } catch (\Exception $exception) {
                \Log::error('Image save :: ' . $exception->getMessage());
                dd($exception->getMessage());

                continue;
            }
        }

        $downloaded = true;

        return view('scrap.extracted_images', compact('images', 'downloaded'));
    }

    /**
     * @SWG\Post(
     *   path="/scrap-products/add",
     *   tags={"Scrape​r"},
     *   summary="Add Product from scraper to erp",
     *   operationId="scrape​r-post-product",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="sku",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="url",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="images",
     *          in="formData",
     *          required=true,
     *          type="array",
     *
     *           @SWG\Items(
     *              type="string",
     *           ),
     *      ),
     *
     *      @SWG\Parameter(
     *          name="properties",
     *          in="formData",
     *          required=true,
     *          type="array",
     *
     *          @SWG\Items(
     *             type="string",
     *           ),
     *      ),
     *
     *      @SWG\Parameter(
     *          name="website",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="price",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="brand",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function syncProductsFromNodeApp(Request $request)
    {
        // Update request data with common mistakes
        $request = ProductHelper::fixCommonMistakesInRequest($request);

        // Log before validating
        $errorLog = LogScraper::LogScrapeValidationUsingRequest($request);

        // Return error
        if (! empty($errorLog['error'])) {
            return response()->json([
                'error' => $errorLog['error'],
            ]);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'sku'        => 'required',
            'url'        => 'required',
            'images'     => 'required|array',
            'properties' => 'required',
            'website'    => 'required',
            'price'      => 'required',
            'brand'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => $validator->errors()]);
        }

        // Get SKU
        $sku = ProductHelper::getSku($request->get('sku'));

        // Get brand
        $brand = Brand::where('name', $request->get('brand'))->first();

        // No brand found?
        if (! $brand) {
            // Check for reference
            $brand = Brand::where('references', 'LIKE', '%' . $request->get('brand') . '%')->first();

            if (! $brand) {
                // if brand is not then create a brand
                $brand = Brand::create([
                    'name' => $request->get('brand'),
                ]);
            }
        }

        // fix property array
        $requestedProperties = $request->get('properties');
        if (! empty($requestedProperties)) {
            foreach ($requestedProperties as $key => &$value) {
                if ($key == 'category') {
                    $requestedProperties[$key] = ! is_array($value) ? [$value] : $value;
                }
            }
        }
        $request->request->add(['properties' => $requestedProperties]);

        $categoryForScrapedProducts    = '';
        $colorForScrapedProducts       = '';
        $compositionForScrapedProducts = '';

        // remove categories if it is matching with sku
        $propertiesExt = $request->get('properties');
        if (isset($propertiesExt['category'])) {
            if (is_array($propertiesExt['category'])) {
                $categories = array_map('strtolower', $propertiesExt['category']);
                $strsku     = strtolower($sku);

                if (in_array($strsku, $categories)) {
                    $index = array_search($strsku, $categories);
                    unset($categories[$index]);
                }
                //category for scrapper
                if (is_array($categories)) {
                    $categoryForScrapedProducts = implode(',', $categories);
                } else {
                    $categoryForScrapedProducts = $categories;
                }

                $propertiesExt['category'] = $categories;
            } else {
                $propertiesExt['category'] = '';
            }
        }

        //color for scraperProducts for
        if (isset($propertiesExt['color'])) {
            if (is_array($propertiesExt['color'])) {
                $colorForScrapedProducts = implode(',', $propertiesExt['color']);
            } else {
                $colorForScrapedProducts = $propertiesExt['color'];
            }
        }

        //compostion for scraped Products
        if (isset($propertiesExt['material_used'])) {
            if (is_array($propertiesExt['material_used'])) {
                $compositionForScrapedProducts = implode(',', $propertiesExt['material_used']);
            } else {
                $compositionForScrapedProducts = $propertiesExt['material_used'];
            }
        }

        // Get this product from scraped products
        $scrapedProduct = ScrapedProducts::where('sku', $sku)->where('website', $request->get('website'))->first();
        $images         = $request->get('images') ?? [];
        $scPrice        = (float) $request->get('price');

        try {
            if (strlen($scPrice) > 4 && strlen($scPrice) < 6) {
                $scPrice = substr($scPrice, 0, 3);
                $scPrice = $scPrice . '.00';
            } elseif (strlen($scPrice) > 5 && strlen($scPrice) < 7) {
                $scPrice = substr($scPrice, 0, 4);
                $scPrice = $scPrice . '.00';
            }
        } catch (\Exception $e) {
            \Log::info('Having problem with this price' . $scPrice . ' and get message is ' . $e->getMessage());
        }

        if (is_numeric($scPrice)) {
            $scPrice = ceil($scPrice / 10) * 10;
        }

        if ($scrapedProduct) {
            // Set values for existing scraped product
            $scrapedProduct->images      = $images;
            $scrapedProduct->url         = $request->get('url');
            $scrapedProduct->properties  = $propertiesExt;
            $scrapedProduct->is_sale     = $request->get('is_sale') ?? 0;
            $scrapedProduct->title       = ProductHelper::getRedactedText($request->get('title'), 'name');
            $scrapedProduct->description = ProductHelper::getRedactedText($request->get('description'), 'short_description');
            $scrapedProduct->brand_id    = $brand->id;
            $scrapedProduct->currency    = $request->get('currency');
            $scrapedProduct->price       = (float) $scPrice;
            if ($request->get('currency') == 'EUR') {
                $scrapedProduct->price_eur = (float) $scPrice;
            }
            $scrapedProduct->discounted_price      = $request->get('discounted_price');
            $scrapedProduct->discounted_percentage = (float) $request->get('discounted_percentage', 0.00);
            $scrapedProduct->original_sku          = trim($request->get('sku'));
            $scrapedProduct->last_inventory_at     = Carbon::now()->toDateTimeString();
            $scrapedProduct->validated             = empty($errorLog['error']) ? 1 : 0;
            $scrapedProduct->validation_result     = $errorLog['error'] . $errorLog['warning'];
            $scrapedProduct->category              = isset($request->properties['category']) ? serialize($request->properties['category']) : null;
            $scrapedProduct->categories            = $categoryForScrapedProducts;
            $scrapedProduct->color                 = $colorForScrapedProducts;
            $scrapedProduct->composition           = $compositionForScrapedProducts;
            $scrapedProduct->material_used         = $compositionForScrapedProducts;
            $scrapedProduct->supplier_id           = isset($requestedProperties['supplier']) ? $requestedProperties['supplier'] : null;
            $scrapedProduct->country               = isset($requestedProperties['country']) ? $requestedProperties['country'] : null;
            $scrapedProduct->size                  = (isset($requestedProperties['sizes']) && is_array($requestedProperties['sizes'])) ? implode(',', $requestedProperties['sizes']) : null;
            if ($request->get('size_system') != '') {
                $scrapedProduct->size_system = $request->get('size_system');
            }
            $scrapedProduct->save();
            $scrapedProduct->touch();
        } else {
            // Create new scraped product
            $scrapedProduct = new ScrapedProducts();

            $scrapedProduct->images                = $images;
            $scrapedProduct->sku                   = $sku;
            $scrapedProduct->original_sku          = trim($request->get('sku'));
            $scrapedProduct->discounted_price      = $request->get('discounted_price');
            $scrapedProduct->is_sale               = $request->get('is_sale') ?? 0;
            $scrapedProduct->has_sku               = 1;
            $scrapedProduct->url                   = $request->get('url');
            $scrapedProduct->title                 = ProductHelper::getRedactedText($request->get('title') ?? 'N/A', 'name');
            $scrapedProduct->description           = ProductHelper::getRedactedText($request->get('description'), 'short_description');
            $scrapedProduct->properties            = $propertiesExt;
            $scrapedProduct->currency              = ProductHelper::getCurrency($request->get('currency'));
            $scrapedProduct->price                 = (float) $scPrice;
            $scrapedProduct->discounted_percentage = (float) $request->get('discounted_percentage', 0.00);
            if ($request->get('currency') == 'EUR') {
                $scrapedProduct->price_eur = (float) $scPrice;
            }
            $scrapedProduct->last_inventory_at = Carbon::now()->toDateTimeString();
            $scrapedProduct->website           = $request->get('website');
            $scrapedProduct->brand_id          = $brand->id;
            $scrapedProduct->category          = isset($request->properties['category']) ? serialize($request->properties['category']) : null;
            $scrapedProduct->validated         = empty($errorLog) ? 1 : 0;
            $scrapedProduct->validation_result = $errorLog['error'] . $errorLog['warning'];
            //adding new fields
            $scrapedProduct->categories    = $categoryForScrapedProducts;
            $scrapedProduct->color         = $colorForScrapedProducts;
            $scrapedProduct->composition   = $compositionForScrapedProducts;
            $scrapedProduct->material_used = $compositionForScrapedProducts;
            $scrapedProduct->supplier_id   = isset($requestedProperties['supplier']) ? $requestedProperties['supplier'] : null;
            $scrapedProduct->country       = isset($requestedProperties['country']) ? $requestedProperties['country'] : null;
            $scrapedProduct->size          = (isset($requestedProperties['sizes']) && is_array($requestedProperties['sizes'])) ? implode(',', $requestedProperties['sizes']) : null;
            if ($request->get('size_system') != '') {
                $scrapedProduct->size_system = $request->get('size_system');
            }
            $scrapedProduct->save();
        }

        $scrap_details = Scraper::where(['scraper_name' => $request->get('website')])->first();
        $this->saveScrapperRequest($scrap_details, $errorLog);

        // Create or update product
        $scrapedProductUpdate = ScrapedProducts::where('sku', $sku)
            ->whereNotNull('description') // Filter out rows where description is null
            ->orderBy('sort_order') // Order by sort_order
            ->first();
        app(ProductsCreator::class)->createProduct($scrapedProductUpdate);

        // Return response
        return response()->json([
            'status' => 'Added items successfully!',
        ]);
    }

    public function storeUnknownSizes(Request $request)
    {
        $statusId = \App\Helpers\StatusHelper::$unknownSize;
        $products = Product::where('status_id', $statusId)->select('id', 'size', 'supplier_id')->get();
        foreach ($products as $product) {
            $size_system  = ScrapedProducts::where('product_id', $product->id)->pluck('size_system')->first();
            $systemSizeId = \App\SystemSize::where('name', $size_system)->pluck('id')->first();
            $sizes        = explode(',', $product['size']);
            foreach ($sizes as $size) {
                $erp_sizeFound = \App\SizeAndErpSize::where(['size' => $size])->first();
                if ($erp_sizeFound == null) {
                    \App\SizeAndErpSize::updateOrCreate(['size' => $size, 'system_size_id' => $systemSizeId], ['size' => $size, 'system_size_id' => $systemSizeId]);
                } elseif ($erp_sizeFound['erp_size_id'] != null) {
                    $erp_size = SystemSizeManager::where('id', $erp_sizeFound['erp_size_id'])->pluck('erp_size')->first();

                    \App\ProductSizes::updateOrCreate([
                        'product_id' => $product->id, 'supplier_id' => $product->supplier_id, 'size' => $erp_size,
                    ], [
                        'product_id' => $product->id, 'quantity' => 1, 'supplier_id' => $product->supplier_id, 'size' => $erp_size,
                    ]);
                }
            }
        }

        return redirect(url('/'));
    }

    public function saveScrapperRequest($scrap_details, $errorLog)
    {
        try {
            //check if scraper of same id have records with same day , then only update the end time
            $check_history = ScrapRequestHistory::where(['scraper_id' => $scrap_details->id, 'start_date' => Carbon::now()])->firstOrFail();
            //update the request data
            ScrapRequestHistory::where(['scraper_id' => $scrap_details->id])->update([
                'end_time'       => Carbon::now(),
                'request_sent'   => empty($errorLog) ? intval($check_history->request_sent + 1) : intval($check_history->request_sent),
                'request_failed' => empty($errorLog) ? intval($check_history->request_failed) : intval($check_history->request_failed + 1),
            ]);
        } catch (\Exception $e) {
            if ($scrap_details) {
                ScrapRequestHistory::create([
                    'scraper_id'     => $scrap_details->id,
                    'date'           => Carbon::now(),
                    'start_time'     => Carbon::now(),
                    'end_time'       => Carbon::now(),
                    'request_sent'   => empty($errorLog) ? 1 : 0,
                    'request_failed' => empty($errorLog) ? 0 : 1,
                ]);
            }
        }

        return true;
    }

    public function excel_import()
    {
        $products = ScrapedProducts::where('website', 'EXCEL_IMPORT_TYPE_1')->paginate(25);

        return view('scrap.excel', compact('products'));
    }

    public function excel_store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file',
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
        $cells       = [];

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
                    case \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_PNG:
                        $extension = 'png';
                        break;
                    case \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_GIF:
                        $extension = 'gif';
                        break;
                    case \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_JPEG:
                        $extension = 'jpg';
                        break;
                }
            } else {
                $zipReader     = fopen($drawing->getPath(), 'r');
                $imageContents = '';
                while (! feof($zipReader)) {
                    $imageContents .= fread($zipReader, 1024);
                }
                fclose($zipReader);
                $extension = $drawing->getExtension();
            }

            $myFileName = '00_Image_' . ++$i . '.' . $extension;
            file_put_contents('uploads/social-media/' . $myFileName, $imageContents);
            $cells[substr($drawing->getCoordinates(), 2)][] = $myFileName;
        }

        $cells_new = [];
        $c         = 0;
        foreach ($cells as $cell) {
            $cells_new[$c] = $cell;
            $c++;
        }

        $files = Excel::toArray(new ProductsImport(), $file);
        $th    = [];

        foreach ($files[0] as $key => $file) {
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
                unset($files[0][$key]);
                break;
            }
            unset($files[0][$key]);
        }

        $fields_only_with_keys = [];

        foreach ($th as $key => $file) {
            if ($file) {
                $fields_only_with_keys[$key] = $file;
            }
        }

        $dataToSave = [];

        foreach ($files[0] as $pkey => $row) {
            $null_count = 0;
            foreach ($row as $item) {
                if ($item === null) {
                    $null_count++;
                }
            }
            if ($null_count > 30) {
                unset($files[0][$pkey]);
            }
        }

        $c = 0;
        foreach ($files[0] as $pkey => $row) {
            foreach ($fields_only_with_keys as $key => $item) {
                $dataToSave[$pkey][$item] = $row[$key];
                if ($item == 'COD. FOTO') {
                    $dataToSave[$pkey][$item] = $cells_new[$c];
                }
            }
            $c++;
        }

        foreach ($dataToSave as $item) {
            $sku = $item['MODELLO VARIANTE COLORE'] ?? null;
            if (! $sku) {
                continue;
            }

            $brand = Brand::where('name', $item['BRAND'] ?? 'UNKNOWN_BRAND_FROM_FILE')->first();

            if (! $brand) {
                continue;
            }

            $sp                      = new ScrapedProducts();
            $sp->website             = 'EXCEL_IMPORT_TYPE_1';
            $sp->sku                 = $sku;
            $sp->has_sku             = 1;
            $sp->brand_id            = $brand->id;
            $sp->title               = $sku;
            $sp->description         = $item['description'] ?? null;
            $sp->images              = $item['COD. FOTO'] ?? [];
            $sp->price               = 'N/A';
            $sp->properties          = $item;
            $sp->url                 = 'N/A';
            $sp->is_property_updated = 0;
            $sp->is_price_updated    = 0;
            $sp->is_enriched         = 0;
            $sp->can_be_deleted      = 0;
            $sp->save();
        }

        return redirect()->back()->with('message', 'Excel Imported Successfully!');
    }

    /**
     * @SWG\Post(
     *   path="/save-supplier",
     *   tags={"Scraper"},
     *   summary="Create supplier",
     *   operationId="scraper-product-save-supplier",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="supplier",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="phone",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="address",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="website",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="email",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="social_handle",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="instagram_handle",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      )
     * )
     */
    public function saveSupplier(Request $request)
    {
        $this->validate($request, [
            'supplier' => 'required',
        ]);

        $s = Supplier::where('supplier', $request->get('supplier'))->first();

        if ($s) {
            $s->email = $request->get('email');
            $s->save();

            return response()->json([
                'message' => 'Added successfully!',
            ]);
        }

        $params = [
            'supplier'         => ucwords($request->get('supplier')),
            'phone'            => str_replace('+', '', $request->get('phone')),
            'address'          => $request->get('address'),
            'website'          => $request->get('website'),
            'email'            => $request->get('email'),
            'social_handle'    => $request->get('social_handle'),
            'instagram_handle' => $request->get('instagram_handle'),
        ];

        Supplier::create($params);

        return response()->json([
            'message' => 'Added successfully!',
        ]);
    }

    /**
     * @SWG\Post(
     *   path="/products/new-supplier",
     *   tags={"Scraper"},
     *   summary="Update/Add product from external scraper",
     *   operationId="scraper-add-procuct-supplier",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    /**
     * Save incoming data from scraper
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveFromNewSupplier(Request $request)
    {
        $receivedJson = json_decode($request->getContent());
        if (! isset($receivedJson)) {
            return response()->json([
                'status' => 'Json format not valid',
            ], 400);
        }

        if ($receivedJson->id == '') {
            return response()->json([
                'status' => 'Product id empty',
            ], 400);
        }

        if ($receivedJson->brand == '') {
            return response()->json([
                'status' => 'Brand name is empty',
            ], 400);
        }
        // Find product
        $product = Product::find($receivedJson->id);

        if ($product) {
            // sets initial status pending for Finished external Scraper
            $pending_finished_external_scraper = [
                'product_id'     => $product->id,
                'old_status'     => $product->status_id,
                'new_status'     => StatusHelper::$externalScraperFinished,
                'pending_status' => 1,
                'created_at'     => date('Y-m-d H:i:s'),
            ];
            \App\ProductStatusHistory::addStatusToProduct($pending_finished_external_scraper);
        }
        // Get brand
        $brand = Brand::where('name', $receivedJson->brand)->first();
        // No brand found?
        if (! $brand) {
            // Check for reference
            $brand = Brand::where('references', 'LIKE', '%' . $receivedJson->brand . '%')->first();

            if (! $brand) {
                // if brand is not then create a brand
                $brand = Brand::create([
                    'name' => $receivedJson->brand,
                ]);
            }
        }
        //add log in scraped product
        $website        = isset($receivedJson->website) ? $receivedJson->website : '';
        $scrapedProduct = null;
        if (! empty($website)) {
            $scrapedProduct = ScrapedProducts::where('website', $website)
                ->where('sku', ! empty($receivedJson->sku) ? $receivedJson->sku : $product->sku)
                ->first();

            if ($scrapedProduct == null || $scrapedProduct == '') {
                $scrapedProduct          = new ScrapedProducts();
                $scrapedProduct->sku     = ! empty($receivedJson->sku) ? $receivedJson->sku : $product->sku;
                $scrapedProduct->website = $website;
            }

            $scrapedProduct->has_sku       = 1;
            $scrapedProduct->supplier      = isset($receivedJson->supplier) ? $receivedJson->supplier : '';
            $scrapedProduct->title         = isset($receivedJson->title) ? $receivedJson->title : '';
            $scrapedProduct->composition   = isset($receivedJson->composition) ? $receivedJson->composition : '';
            $scrapedProduct->color         = isset($receivedJson->color) ? $receivedJson->color : '';
            $scrapedProduct->brand_id      = $brand->id;
            $scrapedProduct->description   = $brand->description;
            $scrapedProduct->material_used = isset($receivedJson->composition) ? $receivedJson->composition : '';
            $scrapedProduct->country       = isset($receivedJson->country) ? $receivedJson->country : '';
            $scrapedProduct->size          = isset($receivedJson->sizes) ? implode(',', $receivedJson->sizes) : '';
            $scrapedProduct->url           = isset($receivedJson->url) ? $receivedJson->url : '';
            $scrapedProduct->images        = isset($receivedJson->images) ? serialize($receivedJson->images) : '';
            $scrapedProduct->size_system   = isset($receivedJson->size_system) ? $receivedJson->size_system : '';
            $scrapedProduct->currency      = isset($receivedJson->currency) ? $receivedJson->currency : '';
            $scrapedProduct->price         = isset($receivedJson->price) ? ($receivedJson->price) : '';

            $scrapedProduct->is_property_updated = 0;
            $scrapedProduct->is_external_scraper = 1;
            $scrapedProduct->is_price_updated    = 0;
            $scrapedProduct->is_enriched         = 0;
            $scrapedProduct->can_be_deleted      = 0;
            $scrapedProduct->validated           = 1;
            $scrapedProduct->save();
        }

        // Return false if no product is found
        if ($product == null) {
            // $scrapedProduct->validated = 1;
            if ($scrapedProduct) {
                $scrapedProduct->validated         = 0;
                $scrapedProduct->validation_result = 'Error processing your request (#1)';
                $scrapedProduct->save();
            }

            return response()->json([
                'status' => 'Error processing your request (#1)',
            ], 400);
        }

        if (isset($receivedJson->status)) {
            // Search For ScraperQueue
            ScrapeQueues::where('done', 0)->where('product_id', $product->id)->update(['done' => 2]);
            $product->status_id = StatusHelper::$unableToScrape;
            $product->save();

            if ($scrapedProduct) {
                $scrapedProduct->validated         = 0;
                $scrapedProduct->validation_result = 'Product processed for unable to scrap';
                $scrapedProduct->save();
            }

            return response()->json([
                'status' => 'Product processed for unable to scrap',
            ]);
        }

        $input = get_object_vars($receivedJson);

        // Validate request
        $validator = Validator::make($input, [
            'id'          => 'required',
            'images'      => 'required|array',
            'description' => 'required',
        ]);

        // Return an error if the validator fails
        if ($validator->fails()) {
            if ($scrapedProduct) {
                $scrapedProduct->validation_result = json_encode($validator->messages());
                $scrapedProduct->save();
            }

            return response()->json($validator->messages(), 400);
        }

        // If product is found, update it
        if ($product) {
            // clear the request using for the new scraper
            $propertiesArray = [
                'material_used' => isset($receivedJson->properties->material_used) ? $receivedJson->properties->material_used : $receivedJson->composition,
                'color'         => isset($receivedJson->properties->color) ? $receivedJson->properties->color : $receivedJson->color,
                'sizes'         => isset($receivedJson->properties->sizes) ? $receivedJson->properties->sizes : $receivedJson->sizes,
                'category'      => isset($receivedJson->properties->category) ? $receivedJson->properties->category : $receivedJson->category,
                'dimension'     => isset($receivedJson->properties->dimension) ? $receivedJson->properties->dimension : $receivedJson->dimensions,
                'country'       => isset($receivedJson->properties->country) ? $receivedJson->properties->country : $receivedJson->country,
            ];

            $formatter = (new \App\Services\Products\ProductsCreator)->getGeneralDetails($propertiesArray);

            $color       = \App\ColorNamesReference::getColorRequest($formatter['color'], $receivedJson->url, $receivedJson->title, $receivedJson->description);
            $composition = $formatter['composition'];
            if (! empty($formatter['composition'])) {
                $composition = \App\Compositions::getErpName($formatter['composition']);
            }

            $description = $receivedJson->description;
            if (! empty($receivedJson->description)) {
                $description = \App\DescriptionChange::getErpName($receivedJson->description);
            }

            // Set basic data
            if (empty($product->name)) {
                $product->name = $receivedJson->title;
            }

            if (empty($product->short_description)) {
                $product->short_description = $description;
            }

            if (empty($product->composition)) {
                $product->composition = $composition;
            }

            if (empty($product->color) && ! empty($formatter['color'])) {
                $product->color = $color;
            }

            if (empty($formatter['color'])) {
                $product->suggested_color = $color;
            }

            if (empty($product->description_link)) {
                $product->description_link = $receivedJson->url;
            }

            if (empty($product->made_in)) {
                $product->made_in = $formatter['made_in'];
            }

            if (empty($product->category)) {
                $product->category = $formatter['category'];
            }

            // if size is empty then only update
            if (empty($product->size)) {
                $product->size = $formatter['size'];
            }
            if ((int) $product->price == 0) {
                $product->price = $receivedJson->price;
            }
            $product->listing_remark = 'Original SKU: ' . $receivedJson->sku;

            // Set optional data
            if (! $product->lmeasurement) {
                $product->lmeasurement = $formatter['lmeasurement'];
            }
            if (! $product->hmeasurement) {
                $product->hmeasurement = $formatter['hmeasurement'];
            }
            if (! $product->dmeasurement) {
                $product->dmeasurement = $formatter['dmeasurement'];
            }

            // Save
            $product->status_id = StatusHelper::$externalScraperFinished;
            $product->save();

            // sets initial status pending for Finished external Scraper
            $finished_external_scraper = [
                'product_id'     => $product->id,
                'old_status'     => $product->status_id,
                'new_status'     => StatusHelper::$externalScraperFinished,
                'pending_status' => 0,
                'created_at'     => date('Y-m-d H:i:s'),
            ];
            \App\ProductStatusHistory::addStatusToProduct($finished_external_scraper);

            // Check if we have images
            $product->attachImagesToProduct($receivedJson->images);

            if (isset($receivedJson->website)) {
                $supplierModel = Supplier::leftJoin('scrapers as sc', 'sc.supplier_id', 'suppliers.id')->where(function ($query) use ($receivedJson) {
                    $query->where('supplier', '=', $receivedJson->website)->orWhere('sc.scraper_name', '=', $receivedJson->website);
                })->first();

                if ($supplierModel) {
                    $productSupplier = \App\ProductSupplier::where('supplier_id', $supplierModel->id)->where('product_id', $product->id)->first();
                    if (! $productSupplier) {
                        $productSupplier              = new \App\ProductSupplier;
                        $productSupplier->supplier_id = $supplierModel->id;
                        $productSupplier->product_id  = $product->id;
                    }

                    $productSupplier->title         = $receivedJson->title;
                    $productSupplier->description   = $description;
                    $productSupplier->supplier_link = $receivedJson->url;
                    $productSupplier->stock         = 1;
                    $productSupplier->price         = ($product->price > 0) ? $product->price : 0;
                    $productSupplier->size          = $formatter['size'];
                    $productSupplier->color         = isset($formatter['color']) ? $formatter['color'] : '';
                    $productSupplier->composition   = isset($formatter['composition']) ? $formatter['composition'] : '';
                    $productSupplier->sku           = $receivedJson->sku;
                    $productSupplier->save();
                }
            }

            // Update scrape_queues by product ID
            ScrapeQueues::where('done', 0)->where('product_id', $product->id)->update(['done' => 1]);
            // Return response
            return response()->json([
                'status' => 'Product processed',
            ]);
        }
        //
        if ($scrapedProduct) {
            $scrapedProduct->validated         = 0;
            $scrapedProduct->validation_result = 'Error processing your request (#99)';
            $scrapedProduct->save();
        }

        // Still here? Return error
        return response()->json([
            'status' => 'Error processing your request (#99)',
        ], 400);
    }

    /**
     * @SWG\Post(
     *   path="/scrape/process-product-links",
     *   tags={"Scraper"},
     *   summary="Process product links for scraper to check which links are available to scrap",
     *   operationId="scraper-process-product-links",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="links[]",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="website",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function scrap_links(Request $request)
    {
        $scrap_links = ScrapedProductsLinks::select('*');

        if (! empty($request->status)) {
            $scrap_links = $scrap_links->where('status', $request->status);
        }

        if (! empty($request->selected_date)) {
            $scrap_links = $scrap_links->whereDate('created_at', '=', $request->selected_date);
        }

        if (! empty($request->search)) {
            $scrap_links = $scrap_links->where('links', 'LIKE', '%' . $request->search . '%')->orWhere('website', 'LIKE', '%' . $request->search . '%');
        }

        $scrap_links = $scrap_links->orderBy('id', 'DESC')->paginate(25);

        return view('scrap.scrap-links', ['scrap_links' => $scrap_links])->with('i', ($request->input('page', 1) - 1) * 25);
    }

    public function scrapLinksStatusHistories($id)
    {
        $datas = ScrapedProductsLinksHistory::where('scraped_products_links_id', $id)->latest()->get();

        return response()->json([
            'status'      => true,
            'data'        => $datas,
            'message'     => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function processProductLinks(Request $request)
    {
        $pendingUrl = [];
        $links      = $request->links;
        $website    = $request->website;

        if (empty($website)) {
            $rawJson = json_decode($request->instance()->getContent());
            $website = isset($rawJson->website) ? $rawJson->website : null;
        }
        if (is_string($links)) {
            $links = json_decode($links);
        } else {
            $rawJson = json_decode($request->instance()->getContent());
            $links   = isset($rawJson->links) ? $rawJson->links : null;
        }

        if (is_array($links)) {
            $scraper = Scraper::where('scraper_name', $website)->first();
            if (! empty($scraper)) {
                if ($scraper->full_scrape == 1) {
                    $scraper->full_scrape = 0;
                    $scraper->save();

                    foreach ($links as $key => $value) {
                        $input                     = [];
                        $input['status']           = 'new';
                        $input['website']          = $website;
                        $input['links']            = $value;
                        $input['scrap_product_id'] = 0;

                        $ScrapedProductsLinksNew = ScrapedProductsLinks::updateOrCreate(
                            ['links' => $value, 'website' => $website], $input
                        );

                        if (! empty($ScrapedProductsLinksNew)) {
                            ScrapedProductsLinksHistory::create([
                                'scraped_products_links_id' => $ScrapedProductsLinksNew->id,
                                'status'                    => 'new',
                            ]);
                        }
                    }

                    return $links;
                }
            }

            foreach ($links as $link) {
                // Load scraped product and update last_inventory_at
                $scrapedProduct = ScrapedProducts::where('url', $link)->where('website', $website)->first();

                if ($scrapedProduct != null) {
                    Log::channel('productUpdates')->debug('[scraped_product] Found existing product with sku ' . ProductHelper::getSku($scrapedProduct->sku));
                    $scrapedProduct->url               = $link;
                    $scrapedProduct->last_inventory_at = Carbon::now();
                    $scrapedProduct->save();

                    $input                     = [];
                    $input['status']           = 'in stock';
                    $input['website']          = $website;
                    $input['links']            = $link;
                    $input['scrap_product_id'] = $scrapedProduct->id;

                    $ScrapedProductsLinksInStock = ScrapedProductsLinks::updateOrCreate(
                        ['links' => $link, 'website' => $website], $input
                    );

                    if (! empty($ScrapedProductsLinksInStock)) {
                        ScrapedProductsLinksHistory::create([
                            'scraped_products_links_id' => $ScrapedProductsLinksInStock->id,
                            'status'                    => 'in stock',
                        ]);
                    }
                } else {
                    $pendingUrl[] = $link;

                    $input                     = [];
                    $input['status']           = 'out of stock';
                    $input['website']          = $website;
                    $input['links']            = $link;
                    $input['scrap_product_id'] = 0;

                    $ScrapedProductsLinksOutOfStock = ScrapedProductsLinks::updateOrCreate(
                        ['links' => $link, 'website' => $website], $input
                    );

                    if (! empty($ScrapedProductsLinksOutOfStock)) {
                        ScrapedProductsLinksHistory::create([
                            'scraped_products_links_id' => $ScrapedProductsLinksOutOfStock->id,
                            'status'                    => 'out of stock',
                        ]);
                    }
                }
            }

            //Getting Supplier by Scraper name
            try {
                $scraper       = Scraper::where('scraper_name', $website)->first();
                $totalLinks    = count($links);
                $pendingLinks  = count($pendingUrl);
                $existingLinks = ($totalLinks - $pendingLinks);

                if ($scraper != '' && $scraper != null) {
                    $scraper->scraper_total_urls    = $totalLinks;
                    $scraper->scraper_existing_urls = $existingLinks;
                    $scraper->scraper_new_urls      = $pendingLinks;
                    $scraper->update();
                }

                $scraperResult                = new ScraperResult();
                $scraperResult->date          = date('Y-m-d');
                $scraperResult->scraper_name  = $website;
                $scraperResult->total_urls    = $totalLinks;
                $scraperResult->existing_urls = $existingLinks;
                $scraperResult->new_urls      = $pendingLinks;
                $scraperResult->save();
            } catch (Exception $e) {
            }
        }

        return $pendingUrl;
    }

    /**
     * @SWG\Post(
     *   path="/scrape/process-product-links-by-brand",
     *   tags={"Scraper"},
     *   summary="Process product links for scraper to check which links are available to scrap and will store the entry brand wise",
     *   operationId="scraper-process-product-links-by-brand",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="links[]",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="website",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function processProductLinksByBrand(Request $request)
    {
        set_time_limit(0);

        $pendingUrl = [];
        $links      = $request->links;
        $website    = $request->website;

        if (empty($website)) {
            $rawJson = json_decode($request->instance()->getContent());
            $website = isset($rawJson->website) ? $rawJson->website : null;
        }
        if (is_string($links)) {
            $links = json_decode($links);
        } else {
            $rawJson = json_decode($request->instance()->getContent());
            $links   = isset($rawJson->links) ? $rawJson->links : null;
        }

        if (is_array($links)) {
            $scraper = Scraper::where('scraper_name', $website)->first();
            if (! empty($scraper)) {
                if ($scraper->full_scrape == 1) {
                    $scraper->full_scrape = 0;
                    $scraper->save();

                    return $links;
                }
            }

            $brands = [];

            foreach ($links as $link) {
                // Load scraped product and update last_inventory_at
                $scrapedProduct = ScrapedProducts::where('url', $link->link)->where('website', $website)->first();

                if ($scrapedProduct != null) {
                    Log::channel('productUpdates')->debug('[scraped_product] Found existing product with sku ' . ProductHelper::getSku($scrapedProduct->sku));
                    $scrapedProduct->url               = $link->link;
                    $scrapedProduct->last_inventory_at = Carbon::now();
                    $scrapedProduct->save();

                    $product = \App\Product::where('sku', $scrapedProduct->sku)->first();
                    if ($product) {
                        $product->stock = $product->stock + 1;
                    }
                    $product->save();
                } else {
                    $pendingUrl[] = $link->link;
                }

                if (isset($brands[$link->brand])) {
                    $brands[$link->brand] = $brands[$link->brand] + 1;
                } else {
                    $brands[$link->brand] = 1;
                }
            }

            if (! empty($brands)) {
                foreach ($brands as $bn => $t) {
                    $brandM = \App\Brand::where('name', $bn)->first();
                    if ($brandM) {
                        $bscraperResult               = new \App\BrandScraperResult();
                        $bscraperResult->date         = date('Y-m-d');
                        $bscraperResult->scraper_name = $website;
                        $bscraperResult->total_urls   = $t;
                        $bscraperResult->brand_id     = $brandM->id;
                        $bscraperResult->save();
                    }
                }
            }

            //Getting Supplier by Scraper name
            try {
                $scraper       = Scraper::where('scraper_name', $website)->first();
                $totalLinks    = count($links);
                $pendingLinks  = count($pendingUrl);
                $existingLinks = ($totalLinks - $pendingLinks);

                if ($scraper != '' && $scraper != null) {
                    $scraper->scraper_total_urls    = $totalLinks;
                    $scraper->scraper_existing_urls = $existingLinks;
                    $scraper->scraper_new_urls      = $pendingLinks;
                    $scraper->update();
                }

                $scraperResult                = new ScraperResult();
                $scraperResult->date          = date('Y-m-d');
                $scraperResult->scraper_name  = $website;
                $scraperResult->total_urls    = $totalLinks;
                $scraperResult->existing_urls = $existingLinks;
                $scraperResult->new_urls      = $pendingLinks;
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
        $users                 = Helpers::getUserArray(User::role('Developer')->get());
        if ($request->website || $request->url || $request->sku || $request->title || $request->price || $request->created || $request->brand || $request->updated || $request->currency == 0 || $request->orderCreated || $request->orderUpdated || $request->columns || $request->color || $request->psize || $request->category || $request->product_id || $request->dimension || $request->prod_img_filter || $request->prod_error_filter) {
            $query = \App\ScrapedProducts::query();

            $dateRange = request('daterange', '');
            $startDate = false;
            $endDate   = false;

            if (! empty($dateRange)) {
                $range = explode(' - ', $dateRange);
                if (! empty($range[0]) && ! empty($range[1])) {
                    $startDate = $range[0];
                    $endDate   = $range[1];
                }
            }

            //global search website
            if (request('prod_img_filter') != null && request('prod_img_filter') == '0') {
                $query->whereRaw('( JSON_EXTRACT(images, "$")  like "%.jpg%" or  JSON_EXTRACT(images, "$")  like "%.png%" or JSON_EXTRACT(images, "$") like "%.jpeg%" or JSON_EXTRACT(images, "$") like "%.gif%")');
            } elseif (request('prod_img_filter') != null && request('prod_img_filter') == '1') {
                $query->whereRaw('not( JSON_EXTRACT(images, "$")  like "%.jpg%" or  JSON_EXTRACT(images, "$")  like "%.png%" or JSON_EXTRACT(images, "$") like "%.jpeg%" or JSON_EXTRACT(images, "$") like "%.gif%")');
            }

            if (request('prod_error_filter') != null && request('prod_error_filter') == '0') {
                $query->where('validation_result', '!=', null);
            } elseif (request('prod_error_filter') != null && request('prod_error_filter') == '1') {
                $query->where('validation_result', '=', null);
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
                $query->whereRaw('JSON_EXTRACT(properties, \'$.color\') like "%' . $request->color . '%"');
            }

            if (request('category') != null) {
                $query->whereRaw('JSON_EXTRACT(properties, \'$.category\') like "%' . $request->category . '%"');
            }

            if (request('psize') != null) {
                $query->whereRaw('JSON_EXTRACT(properties, \'$.sizes\') like "%' . $request->psize . '%" OR JSON_EXTRACT(properties, \'$.size\') like "%' . $request->psize . '%"');
            }

            if (request('dimension') != null) {
                $query->whereRaw('JSON_EXTRACT(properties, \'$.dimension\') like "%' . $request->dimension . '%"');
            }

            if (request('product_id') != null) {
                $productIds = explode(',', $request->product_id);
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

            if (! empty($startDate)) {
                $query->whereDate('created_at', ' >= ', $startDate);
            }

            if (! empty($endDate)) {
                $query->whereDate('created_at', ' <= ', $endDate);
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
            $logs     = $query->paginate($paginate)->appends(request()->except(['page']));
            $search   = [
                \DB::raw('count(*) as total_record'),
                \DB::raw('count(DISTINCT p.sku) as total_u_record'),
            ];

            if (! empty($startDate) && ! empty($endDate)) {
                $search[] = \DB::raw("DATE_FORMAT(scraped_products.created_at, '%Y-%m-%d') as date");
            } else {
                $search[] = \DB::raw("'All' as date");
            }

            $totalUniqueSkuRecords = \DB::table('scraped_products')->leftJoin('products as p', function ($q) {
                $q->on('p.id', 'scraped_products.product_id')->where('stock', '>=', 1);
            });

            if (! empty($startDate)) {
                $totalUniqueSkuRecords->whereDate('scraped_products.created_at', ' >= ', $startDate);
            }

            if (! empty($endDate)) {
                $totalUniqueSkuRecords->whereDate('scraped_products.created_at', ' <= ', $endDate);
                $totalUniqueSkuRecords->groupBy(\DB::raw('DATE_FORMAT(scraped_products.created_at, "%Y-%m-%d")'));
            }

            $totalUniqueSkuRecords->select($search);
            $summeryRecords = $totalUniqueSkuRecords->get();

            $response = request()->except(['page']);
            if (empty($response['columns'])) {
                $response['columns'] = ['color', 'category', 'size', 'dimension'];
            }
        } else {
            $response = '';
            $paginate = (Setting::get('pagination') * 10);

            $logs = LogScraper::orderby('updated_at', 'desc')->paginate($paginate);
        }
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('scrap.partials.scraped_url_data', compact('logs', 'response', 'summeryRecords', 'users'))->render(),
                'links' => (string) $logs->render(),
                'count' => $logs->total(),
            ], 200);
        }

        return view('scrap.scraped_url', compact('logs', 'response', 'summeryRecords', 'users'));
    }

    /**
     * @SWG\Get(
     *   path="/products/auto-rejected",
     *   tags={"Scraper"},
     *   summary="List of product which is in queue where done = 0 in scrap_queues",
     *   operationId="scraper-products-auto-rejected",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */

    /**
     * @SWG\Get(
     *   path="products/get-products-to-scrape",
     *   tags={"Scraper"},
     *   summary="List of product which is in queue where done = 0 in scrap_queues",
     *   operationId="scraper-products-get-products-to-scrape",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */

    /**
     * @SWG\Get(
     *   path="products/new-supplier",
     *   tags={"Scraper"},
     *   summary="List of product which is in queue where done = 0 in scrap_queues",
     *   operationId="scraper-products-new-supplier",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
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
                    'id'           => $scrapedQueue->product_id,
                    'sku'          => null,
                    'original_sku' => null,
                    'brand'        => $product->brands ? $product->brands->name : '',
                    'url'          => $scrapedQueue->url,
                    'supplier'     => $product->supplier,
                ];

                // Update status to is being scraped
                $product->status_id = StatusHelper::$isBeingScrapedWithGoogleImageSearch;
                $product->save();
            }
        }

        // Only run if productsToPush is empty
        if (! is_array($productsToPush) || count($productsToPush) == 0) {
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
                            'id'           => $product->id,
                            'sku'          => $product->sku,
                            'original_sku' => ProductHelper::getOriginalSkuByBrand(! empty($scrapedProduct->original_sku) ? $scrapedProduct->original_sku : $scrapedProduct->sku, $product->brands ? $product->brands->id : 0),
                            'brand'        => $product->brands ? $product->brands->name : '',
                            'url'          => null,
                            'supplier'     => $product->supplier,
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

        if ($request->global != null) {
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

        if ($request->scraper_name != null) {
            $query = $query->where('scraper_name', 'LIKE', "%{$request->scraper_name}%");
        }

        if ($request->run_gap_search != null) {
            $query = $query->where('run_gap', 'LIKE', "%{$request->run_gap_search}%");
        }

        if ($request->time_out_search != null) {
            $query = $query->where('time_out', 'LIKE', "%{$request->time_out_search}%");
        }

        if ($request->starting_url_search != null) {
            $query = $query->where('starting_urls', 'LIKE', "%{$request->starting_url_search}%");
        }

        if ($request->designer_url_search != null) {
            $query = $query->where('designer_url_selector', 'LIKE', "%{$request->designer_url_search}%");
        }

        if ($request->product_url_search != null) {
            $query = $query->where('product_url_selector', 'LIKE', "%{$request->product_url_search}%");
        }

        if ($request->supplier_name != null) {
            $query = $query->whereHas('mainSupplier', function ($qu) use ($request) {
                $qu->where('supplier', 'LIKE', "%{$request->supplier_name}%");
            });
        }

        $suppliers = Supplier::where('supplier_status_id', 1)->get();
        $scrapers  = $query->paginate(25)->appends(request()->except(['page']));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('scrap.partials.supplier-scraper-data', compact('scrapers', 'suppliers'))->render(),
                'links' => (string) $scrapers->render(),
                'count' => $scrapers->total(),
            ], 200);
        }

        return view('scrap.supplier-scraper', compact('scrapers', 'suppliers'));
    }

    public function genericScraperSave(Request $request)
    {
        if ($request->id) {
            $scraper = Scraper::find($request->id);
        } else {
            $scraper               = new Scraper;
            $scraper->scraper_name = $request->name;
            $scraper->supplier_id  = $request->supplier_id;
        }

        $scraper->run_gap               = $request->run_gap;
        $scraper->full_scrape           = ! empty($request->full_scrape) ? $request->full_scrape : '';
        $scraper->time_out              = $request->time_out;
        $scraper->starting_urls         = $request->starting_url;
        $scraper->product_url_selector  = $request->product_url_selector;
        $scraper->designer_url_selector = $request->designer_url;
        $scraper->save();

        if ($request->ajax()) {
            return response()->json(['success'], 200);
        }

        return redirect()->back()->with('message', 'Scraper Saved');
    }

    public function genericMapping($id)
    {
        $scraper  = Scraper::find($id);
        $mappings = ScraperMapping::where('scrapers_id', $id)->get();

        return view('scrap.generic-scraper-mapping', compact('scraper', 'mappings', 'id'));
    }

    public function genericMappingSave(Request $request)
    {
        $id        = $request->id;
        $select    = $request->select;
        $count     = count($select);
        $functions = $request->functions;
        $parameter = $request->parameter;
        $selector  = $request->selector;

        for ($i = 0; $i < $count; $i++) {
            if ($select[$i] != null) {
                $updateMapping = ScraperMapping::where('scrapers_id', $id)->where('field_name', $select[$i])->first();
                if ($updateMapping != null) {
                    $mapping = $updateMapping;
                } else {
                    $mapping = new ScraperMapping;
                }
                if ($selector[$i] == null) {
                    $selector[$i] = '';
                }
                if ($functions[$i] == null) {
                    $functions[$i] = '';
                }
                if ($parameter[$i] == null) {
                    $parameter[$i] = '';
                }

                $mapping->field_name  = $select[$i];
                $mapping->scrapers_id = $id;
                $mapping->selector    = $selector[$i];
                $mapping->function    = $functions[$i];
                $mapping->parameter   = $parameter[$i];
                $mapping->save();
            }
        }

        return response()->json(['success'], 200);
    }

    /**
     * @SWG\Get(
     *   path="/scraper/next",
     *   tags={"Scrape​r"},
     *   summary="Send the next scraper",
     *   operationId="scraper-next",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=400, description="No Scraper Present"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function sendScrapDetails()
    {
        $scraper = Scraper::whereRaw('(scrapers.start_time IS NULL OR scrapers.start_time < "2000-01-01 00:00:00" OR (scrapers.start_time < scrapers.end_time AND scrapers.end_time < DATE_SUB(NOW(), INTERVAL scrapers.run_gap HOUR)))')->where('time_out', '>', 0)->first();

        if ($scraper == null) {
            return response()->json(['message' => 'No Scraper Present'], 400);
        }
        $startingURLs = explode("\n", str_replace("\r", '', $scraper->starting_urls));

        $maps = ScraperMapping::where('scrapers_id', $scraper->id)->get();

        foreach ($maps as $map) {
            $mapArray[] = [$map->field_name => ['selector' => $map->selector, 'function' => $map->function, 'parameters' => $map->parameter]];
        }

        if (! isset($mapArray)) {
            $mapArray = [];
        }

        $scraper->start_time = now();
        $scraper->save();

        return response()->json(
            [
                'id'                    => $scraper->id,
                'website'               => $scraper->scraper_name,
                'timeout'               => $scraper->time_out,
                'starting_urls'         => $startingURLs,
                'designer_url_selector' => $scraper->designer_url_selector,
                'product_url_selector'  => $scraper->product_url_selector,
                'map'                   => $mapArray,
            ]
        );
    }

    /**
     * @SWG\Post(
     *   path="/scraper/endtime",
     *   tags={"Scrape​r"},
     *   summary="Update scraper end time",
     *   operationId="scraper-endtime",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=400, description="No Scraper Present"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          type="integer"
     *      ),
     * )
     */
    public function recieveScrapDetails(Request $request)
    {
        $id      = $request->id;
        $scraper = Scraper::find($id);
        if ($scraper == null) {
            return response()->json(['message' => 'No Scraper Present'], 400);
        }
        $scraper->end_time = now();
        $scraper->save();

        return response()->json(['success'], 200);
    }

    public function genericMappingDelete(Request $request)
    {
        $id      = $request->id;
        $mapping = ScraperMapping::find($id);
        $mapping->delete();

        return response()->json(['success'], 200);
    }

    public function scraperFullScrape(Request $request)
    {
        $scraper = Scraper::find($request->id);
        if (! empty($scraper)) {
            $scraper->full_scrape = $request->value;
            $scraper->save();
        }

        return response()->json(['success'], 200);
    }

    /**
     * @SWG\Post(
     *   path="/scraper/ready",
     *   tags={"Scrape​r"},
     *   summary="Update scraper last started at time",
     *   operationId="scrapper-ready",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *
     *      @SWG\Parameter(
     *          name="scraper_name",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    /**
     * Store scraper starting time
     */
    public function scraperReady(Request $request)
    {
        $scraper = Scraper::where('scraper_name', $request->scraper_name)->first();
        if (! empty($scraper)) {
            $scraper->last_started_at = Carbon::now();
            $scraper->save();
        }

        return response()->json(['success'], 200);
    }

    /**
     * @SWG\Post(
     *   path="/scraper/completed",
     *   tags={"Scrape​r"},
     *   summary="Update scraper last completed at",
     *   operationId="scrapper-completed",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *
     *      @SWG\Parameter(
     *          name="scraper_name",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    /**
     * Store scraper completed time
     */
    public function scraperCompleted(Request $request)
    {
        $scraper = Scraper::where('scraper_name', $request->scraper_name)->first();
        if (! empty($scraper)) {
            $scraper->last_completed_at = Carbon::now();
            $scraper->save();
        }

        return response()->json(['success'], 200);
    }

    /**
     * @SWG\Get(
     *   path="/scraper/need-to-start",
     *   tags={"Scrape​r"},
     *   summary="List of scraper which need to start",
     *   operationId="scraper-need-start",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="server_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function needToStart(Request $request)
    {
        if ($request->server_id != null) {
            $totalScraper = [];
            $scrapers     = Scraper::select('parent_id', 'scraper_name')->where('server_id', $request->server_id)->where('scraper_start_time', \DB::raw('HOUR(now())'))->get();
            foreach ($scrapers as $scraper) {
                if (! $scraper->parent_id) {
                    $totalScraper[] = $scraper->scraper_name;
                } else {
                    $totalScraper[] = $scraper->parent->scraper_name . '/' . $scraper->scraper_name;
                }
            }

            return response()->json(['code' => 200, 'data' => $totalScraper, 'message' => '']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Please send server id']);
        }
    }

    /**
     * @SWG\Get(
     *   path="/scraper-needed-products",
     *   tags={"Scrape​r"},
     *   summary="Send product which is on request from external scraper",
     *   operationId="scraper-needed-product",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     * )
     */
    public function scraperNeeded(Request $request)
    {
        $products = Product::where('status_id', StatusHelper::$requestForExternalScraper)
            ->leftJoin('brands', function ($join) {
                $join->on('products.brand', '=', 'brands.id');
            })
            ->leftJoin('suppliers', function ($join) {
                $join->on('products.supplier_id', '=', 'suppliers.id');
            })
            ->select(['products.id', 'products.sku', 'products.supplier', 'products.status_id', 'brands.name'])
            ->orderBy('brands.priority', 'desc')
            ->orderBy('suppliers.priority', 'desc')
            ->latest('products.created_at')
            ->limit(50)
            ->get()
            ->toArray();
        if ($products) {
            foreach ($products as $value) {
                $scrap_status_data = [
                    'product_id'     => $value['id'],
                    'old_status'     => StatusHelper::$requestForExternalScraper,
                    'new_status'     => StatusHelper::$sendtoExternalScraper,
                    'pending_status' => 1,
                    'created_at'     => date('Y-m-d H:i:s'),
                ];
                \App\ProductStatusHistory::addStatusToProduct($scrap_status_data);
            }
        }
        foreach ($products as $value) {
            Product::where('id', $value['id'])->update(['status_id' => StatusHelper::$sendtoExternalScraper]);
            $scrap_status_data = [
                'product_id'     => $value['id'],
                'old_status'     => StatusHelper::$requestForExternalScraper,
                'new_status'     => StatusHelper::$sendtoExternalScraper,
                'pending_status' => 0,
                'created_at'     => date('Y-m-d H:i:s'),
            ];
            \App\ProductStatusHistory::addStatusToProduct($scrap_status_data);
        }

        return response()->json($products);
    }

    /**
     * @SWG\Post(
     *   path="/node/restart-script",
     *   tags={"Node"},
     *   summary="Product restart node",
     *   operationId="procuct-restart-node",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function restartNode(Request $request)
    {
        if ($request->name && $request->server_id) {
            $scraper = Scraper::where('scraper_name', $request->name)->first();
            if (! $scraper->parent_id) {
                $name = $scraper->scraper_name;
            } else {
                $name = $scraper->parent->scraper_name . '/' . $scraper->scraper_name;
            }
            $url       = 'https://' . $request->server_id . '.theluxuryunlimited.com:' . config('env.NODE_SERVER_PORT') . '/restart-script?filename=' . $name . '.js';
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $curl      = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, \App\Http\Controllers\ScrapController::class, 'restartNode');
            curl_close($curl);

            if ($response) {
                return response()->json(['code' => 200, 'message' => 'Script Restarted']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Check if Server is running']);
            }
        }
    }

    /**
     * @SWG\Post(
     *   path="/node/get-status",
     *   tags={"Node"},
     *   summary="procuct get status",
     *   operationId="procuct-get-status",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function getStatus(Request $request)
    {
        if ($request->name && $request->server_id) {
            $scraper = Scraper::where('scraper_name', $request->name)->first();
            if (! $scraper->parent_id) {
                $name = $scraper->scraper_name;
            } else {
                $name = $scraper->parent->scraper_name . '/' . $scraper->scraper_name;
            }
            $url       = 'https://' . $request->server_id . '.theluxuryunlimited.com:' . config('env.NODE_SERVER_PORT') . '/process-list?filename=' . $name . '.js';
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $curl      = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, \App\Http\Controllers\ScrapController::class, 'getStatus');
            curl_close($curl);

            if ($response) {
                $re  = '/\d+/m';
                $str = $response;
                preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

                if (count($matches) == 2 || count($matches) == 1 || count($matches) == 0) {
                    return response()->json(['code' => 200, 'message' => 'Script Is Not Running']);
                } else {
                    return response()->json(['code' => 200, 'message' => "Script Is Running \n" . json_decode($response)->Process[0]->duration]);
                }
            } else {
                return response()->json(['code' => 500, 'message' => 'Check if Server is running']);
            }
        }
    }

    public function updateNode(Request $request)
    {
        if ($request->name && $request->server_id) {
            $scraper = Scraper::where('scraper_name', $request->name)->first();
            if (! $scraper->parent_id) {
                $name = $scraper->scraper_name;
            } else {
                $name = $scraper->parent->scraper_name . '/' . $scraper->scraper_name;
            }

            $url       = 'https://' . $request->server_id . '.theluxuryunlimited.com:' . config('env.NODE_SERVER_PORT') . '/process-list?filename=' . $name . '.js';
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $curl      = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($response), $httpcode, \App\Http\Controllers\ScrapController::class, 'updateNode');
            curl_close($curl);

            $duration = json_decode($response);
            $duration = isset($duration->Process[0]->duration) ? $duration->Process[0]->duration : null;
            if ($response) {
                return response()->json(['code' => 200, 'message' => 'Script Restarted', 'duration' => $duration]);
            } else {
                return response()->json(['code' => 500, 'message' => 'Check if Server is running']);
            }
        }
    }

    public function killNode(Request $request)
    {
        if ($request->name && $request->server_id) {
            $scraper = Scraper::where('scraper_name', $request->name)->first();
            if (! $scraper->parent_id) {
                $name = $scraper->scraper_name;
            } else {
                $name = $scraper->parent->scraper_name . '/' . $scraper->scraper_name;
            }

            $url       = 'https://' . $request->server_id . '.theluxuryunlimited.com:' . config('env.NODE_SERVER_PORT') . '/kill-scraper?filename=' . $name . '.js';
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $curl      = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $response = curl_exec($curl);
            curl_close($curl);
            LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($response), $httpcode, \App\Http\Controllers\ScrapController::class, 'killNode');
            if ($response) {
                return response()->json(['code' => 200, 'message' => 'Script Restarted']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Check if Server is running']);
            }
        }
    }

    public function saveChildScraper(Request $request)
    {
        $scrperEx = explode('#', $request->scraper_name);

        $scraper = Scraper::whereNull('parent_id');

        if (! empty($scrperEx[0])) {
            $scraper = $scraper->where('scraper_name', $scrperEx[0]);
        }

        if (! empty($scrperEx[1])) {
            $scraper = $scraper->where('id', $scrperEx[1]);
        }

        $scraper = $scraper->first();

        if ($scraper) {
            $parentId                      = $scraper->id;
            $scraperChild                  = new Scraper;
            $scraperChild->scraper_name    = $request->name;
            $scraperChild->supplier_id     = $scraper->supplier_id;
            $scraperChild->parent_id       = $parentId;
            $scraperChild->run_gap         = $request->run_gap;
            $scraperChild->start_time      = $request->start_time;
            $scraperChild->scraper_made_by = $request->scraper_made_by;
            $scraperChild->server_id       = $request->server_id;
            $scraperChild->save();

            return redirect()->back()->with('message', 'Child Scraper Saved');
        }

        return redirect()->back()->with('message', 'Scraper Not Found');
    }

    public function assignScrapProductTask(Request $request)
    {
        $requestData = new Request();
        $requestData->setMethod('POST');
        $requestData->request->add([
            'priority'    => 1,
            'issue'       => $request->message, // issue detail
            'status'      => 'Planned',
            'module'      => 'Scraper',
            'subject'     => $request->subject, // enter issue name
            'assigned_to' => 6,
        ]);
        app(\App\Http\Controllers\DevelopmentController::class)->issueStore($requestData, 'issue');

        return redirect()->back();
    }

    /**
     * @SWG\POST(
     *   consumes={"multipart/form-data"},
     *   path="/scrape/send-screenshot",
     *   tags={"Scrape​r"} ,
     *   summary="Store scraper screenshot into database",
     *   operationId="scrape-send-screenshot",
     *
     *   @SWG\Response(response=200, description="Screenshot saved successfully"),
     *   @SWG\Response(response=500, description="Required field is missing"),
     *
     *      @SWG\Parameter(
     *          name="website",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
    @SWG\Parameter(
     *          name="screenshot",
     *          in="formData",
     *          required=true,
     *          type="file"
     *      ),
     * )
     */
    public function sendScreenshot(Request $request)
    {
        return response()->json(['code' => 500, 'data' => [], 'message' => 'Screenshot request has been disabled']);

        if (empty($request->website)) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'website (scraper name) is required field']);
        }

        if (! $request->hasFile('screenshot')) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Screenshot is required']);
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Screenshot saved successfully']);
    }

    /**
     * @SWG\POST(
     *   path="/scrape/send-position",
     *   tags={"Scrape​r"} ,
     *   summary="Store scraper posiotion periodically",
     *   operationId="scrape-send-position",
     *
     *   @SWG\Response(response=200, description="History saved successfully"),
     *   @SWG\Response(response=500, description="Required field is missing"),
     *
     *      @SWG\Parameter(
     *          name="website",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
    @SWG\Parameter(
     *          name="comment",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function sendPosition(Request $request)
    {
        if (empty($request->website)) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'website (scraper name) is required field']);
        }

        if (empty($request->get('comment'))) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Comment is required']);
        }

        $scraper = \App\Scraper::where('scraper_name', $request->website)->first();

        if (! $scraper) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'website (scraper name) is wrong']);
        }

        $history = new \App\ScraperPositionHistory;
        $history->fill([
            'scraper_name' => $scraper->scraper_name,
            'scraper_id'   => $scraper->id,
            'comment'      => $request->get('comment'),
        ]);

        $history->save();

        return response()->json(['code' => 200, 'data' => [], 'message' => 'History saved successfully']);
    }

    public function getLatestLog(Request $request)
    {
        if ($request->name && $request->server_id) {
            $scraper = Scraper::where('scraper_name', $request->name)->first();
            if (! $scraper->parent_id) {
                $name = $scraper->scraper_name;
            } else {
                $name = $scraper->parent->scraper_name . '/' . $scraper->scraper_name;
            }

            $url       = 'https://' . $request->server_id . '.theluxuryunlimited.com:' . config('env.NODE_SERVER_PORT') . '/send-position?website=' . $name;
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $curl      = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($response), $httpcode, \App\Http\Controllers\ScrapController::class, 'getLatestLog');

            curl_close($curl);

            if (! empty($response)) {
                $response = json_decode($response);

                \Log::info(print_r($response, true));

                if ((isset($response->status) && $response->status == "Didn't able to find file of given scrapper") || empty($response->log)) {
                    echo 'Sorry , no log was return from server';
                    exit;
                } else {
                    if (! empty($response->log)) {
                        $file = "$request->server_id-$scraper->scraper_name.txt";
                        header('Content-Description: File Transfer');
                        header('Content-type: application/octet-stream');
                        header('Content-disposition: attachment; filename= ' . $file . '');
                        $log = base64_decode($response->log);

                        if (! empty($log)) {
                            $api_log               = new ScrapApiLog;
                            $api_log->scraper_id   = $scraper->id;
                            $api_log->server_id    = $request->server_id;
                            $api_log->log_messages = $log;
                            $api_log->save();
                        }
                    }
                }
            } else {
                abort(404);
            }
        }
    }

    /**
     * @SWG\GET(
     *   path="/scrape/auto-restart",
     *   tags={"Scrape​r"} ,
     *   summary="Check scraper is auto restart ?",
     *   operationId="scrape-auto-restart",
     *
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=500, description="Required field is missing"),
     *
     *      @SWG\Parameter(
     *          name="website",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      )
     * )
     */
    public function needToAutoRestart(Request $request)
    {
        if (empty($request->website)) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'website (scraper name) is required field']);
        }

        $scraper = \App\Scraper::where('scraper_name', $request->website)->first();

        if (! $scraper) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'website (scraper name) is  wrong']);
        }

        return response()->json(['code' => 200, 'auto_restart' => $scraper->auto_restart]);
    }

    /**
     * @SWG\GET(
     *   path="/scrape/update-restart-time",
     *   tags={"Scrape​r"} ,
     *   summary="Update scraper restart time",
     *   operationId="scrape-update-restart-time",
     *
     *   @SWG\Response(response=200, description="History saved successfully"),
     *   @SWG\Response(response=500, description="Required field is missing"),
     *
     *      @SWG\Parameter(
     *          name="website",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      )
     * )
     */
    public function updateRestartTime(Request $request)
    {
        if (empty($request->website)) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'website (scraper name) is required field']);
        }

        $scraper = \App\Scraper::where('scraper_name', $request->website)->first();

        if (! $scraper) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'website (scraper name) is wrong']);
        }

        $remark_entry = \App\ScrapRemark::create([
            'scraper_name' => $scraper->scraper_name,
            'scrap_field'  => 'update-restart-time',
            'new_value'    => date('Y-m-d H:i:s'),
            'scrap_id'     => $scraper->id,
        ]);

        return response()->json(['code' => 200, 'data' => [], 'message' => 'History saved successfully']);
    }

    public function getServerStatistics(Request $request)
    {
        $servers  = Scraper::whereNotNull('server_id')->groupBy('server_id')->pluck('server_id', 'id')->toArray();
        $scrapers = Scraper::whereNotNull('server_id');

        if ($request->has('q') && ! empty($request->get('q'))) {
            $scrapers->where('scraper_name', 'LIKE', '%' . $request->get('q') . '%');
        }
        $scrapers = $scrapers->select('id', 'server_id', 'scraper_name', 'scraper_start_time')->get();
        $data     = [];
        foreach ($scrapers as $scraper) {
            if ($scraper->scraper_start_time >= 0 && $scraper->scraper_start_time <= 3) {
                $data[$scraper->server_id][3][] = $scraper->scraper_name;
            } elseif ($scraper->scraper_start_time > 3 && $scraper->scraper_start_time <= 6) {
                $data[$scraper->server_id][6][] = $scraper->scraper_name;
            } elseif ($scraper->scraper_start_time > 6 && $scraper->scraper_start_time <= 9) {
                $data[$scraper->server_id][9][] = $scraper->scraper_name;
            } elseif ($scraper->scraper_start_time > 9 && $scraper->scraper_start_time <= 12) {
                $data[$scraper->server_id][12][] = $scraper->scraper_name;
            } elseif ($scraper->scraper_start_time > 12 && $scraper->scraper_start_time <= 15) {
                $data[$scraper->server_id][15][] = $scraper->scraper_name;
            } elseif ($scraper->scraper_start_time > 15 && $scraper->scraper_start_time <= 18) {
                $data[$scraper->server_id][18][] = $scraper->scraper_name;
            } elseif ($scraper->scraper_start_time > 18 && $scraper->scraper_start_time <= 21) {
                $data[$scraper->server_id][21][] = $scraper->scraper_name;
            } elseif ($scraper->scraper_start_time > 21 && $scraper->scraper_start_time <= 24) {
                $data[$scraper->server_id][24][] = $scraper->scraper_name;
            }
        }

        return view()->make('scrap.server-statistics', compact('servers', 'data'));
    }

    public function getPythonLog(Request $request)
    {
        $storeWebsites = ['sololuxury', 'avoir-chic', 'brands-labels', 'o-labels', 'suvandnat', 'veralusso'];
        $devices       = ['mobile', 'desktop', 'tablet'];

        if ($request->website || $request->created_at) {
            $query = ScrapPythonLog::orderby('updated_at', 'desc');

            if (request('created_at') != null) {
                $query->whereDate('created_at', request('created_at'));
            }
            if (request('website') != null) {
                $query->where('website', 'LIKE', "%{$request->website}%");
            }

            $paginate = (Setting::get('pagination') * 10);
            $logs     = $query->paginate($paginate)->appends(request()->except(['page']));
        } else {
            $paginate = (Setting::get('pagination') * 10);
            $logs     = ScrapPythonLog::orderby('created_at', 'desc')->paginate($paginate);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('scrap.partials.python_logdata', compact('logs'))->render(),
                'links' => (string) $logs->render(),
                'count' => $logs->total(),
            ], 200);
        }

        return view('scrap.python_log', compact('logs', 'storeWebsites', 'devices'));
    }

    public function loginstance(Request $request)
    {
        $url  = '167.86.88.58:5000/get-logs ';
        $date = ($request->date != '') ? \Carbon\Carbon::parse($request->date)->format('m-d-Y') : '';

        $data = [];
        if (! empty($date)) {
            $data = ['website' => $request->website, 'date' => $date, 'device' => $request->device];
        } else {
            return response()->json([
                'type'     => 'error',
                'response' => 'Please select Date',
            ], 200);
        }
        $insertData = $data;
        $data       = json_encode($data);
        $ch         = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'accept: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result1 = curl_exec($ch);

        if ($result1 == 'Log File Not Found') {
            $insertData['log_text'] = $result1;
        } else {
            $file_name = 'python_logs/python_site_log_' . $insertData['website'] . '_' . $insertData['device'] . '.log';
            Storage::put($file_name, $result1);
            $insertData['log_text'] = url('/storage/app/' . $file_name);
        }

        ScrapPythonLog::create($insertData);
        $result = explode("\n", $result1);

        if (count($result) > 1) {
            return response()->json([
                'type'     => 'success',
                'response' => view('instagram.hashtags.partials.get_status', compact('result'))->render(),
            ], 200);
        } else {
            return response()->json([
                'type'     => 'error',
                'response' => 'Log File Not Found',
            ], 200);
        }
    }

    public function showProductStat(Request $request)
    {
        $products    = [];
        $brands_list = Brand::whereNull('deleted_at')->pluck('name', 'id');

        if (! empty($request->has('brands'))) {
            $brands    = Brand::whereNull('deleted_at')->whereIn('id', $request->get('brands'))->get();
            $suppliers = DB::table('scraped_products')->selectRaw('DISTINCT(`website`)')->pluck('website');

            foreach ($suppliers as $supplier) {
                foreach ($brands as $brand) {
                    $products[$supplier][$brand->name] = ScrapedProducts::where('website', $supplier)
                        ->where('brand_id', $brand->id);
                    if (! empty($request->get('start_date')) && ! empty($request->get('end_date'))) {
                        if ($request->get('start_date') != null && $request->get('end_date') != null) {
                            $products[$supplier][$brand->name] = $products[$supplier][$brand->name]->whereBetween('created_at', [$request->get('start_date'), $request->get('end_date')]);
                        }
                    }

                    $products[$supplier][$brand->name] = $products[$supplier][$brand->name]->count();
                }
            }
        }

        return view('scrap.scraped_product_data', compact('products', 'request', 'brands_list'));
    }
}
