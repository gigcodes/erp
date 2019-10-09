<?php

namespace App\Http\Controllers;

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
use App\Services\Products\AttachSupplier;
use App\Services\Scrap\GoogleImageScraper;
use App\Services\Scrap\PinterestScraper;
use App\Services\Products\GnbProductsCreator;
use App\Supplier;
use App\Loggers\LogScraper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Storage;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\Products\ProductsCreator;
use App\SupplierBrandCount;
use App\SupplierCategoryCount;

class ScrapController extends Controller
{

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
            $scrapedProduct->properties = $request->get('properties');
            $scrapedProduct->is_sale = $request->get('is_sale') ?? 0;
            $scrapedProduct->title = ProductHelper::getRedactedText($request->get('title'));
            $scrapedProduct->description = ProductHelper::getRedactedText($request->get('description'));
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

            // Category Count Save
            if($scrapedProduct->properties){

                $property =  $scrapedProduct->properties;
                if($property['category']){
                $category = $property['category'];
                $supplier = $scrapStatistics->supplier;
                foreach ($category as $categories) {
                    $cat = Category::select('id')->where('title', $categories)->first();
                    if ($cat) {
                        if ($cat->suppliercategorycount) {
                            $count = $cat->suppliercategorycount->count();
                        } else {
                            $count = 0;
                        }

                        if ($count == 0) {
                            $sup = Supplier::select('id')->where('supplier', $supplier)->first();
                            if ($sup) {
                                $data['category_id'] = $cat->id;
                                $data['supplier_id'] = $sup->id;
                                $data['cnt'] = 0;
                                SupplierCategoryCount::create($data);
                            }
                        }
                    }
                 }
              }
            }

            //Brand Count Save
            if($brand->supplierbrandcount){
                $count = $brand->supplierbrandcount->count();
            }else{
                $count = 0;
            }

            if($count == 0){
                $sup = Supplier::select('id')->where('supplier', 'like', '%' . $supplier . '%')->first();
                if($sup){
                    $data['brand_id'] = $brand->id;
                    $data['supplier_id'] = $sup->id;
                    $data['cnt'] = 0;
                    SupplierBrandCount::create($data);
                }
            }


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
            $scrapedProduct->title = ProductHelper::getRedactedText($request->get('title') ?? 'N/A');
            $scrapedProduct->description = ProductHelper::getRedactedText($request->get('description'));
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

            // Category Count Save
            if($scrapedProduct->properties){

                $property =  $scrapedProduct->properties;
                if($property['category']){
                    $category = $property['category'];
                    $supplier = $scrapStatistics->supplier;
                    foreach ($category as $categories) {
                        $cat = Category::select('id')->where('title', $categories)->first();
                        if ($cat) {
                            if ($cat->suppliercategorycount) {
                                $count = $cat->suppliercategorycount->count();
                            } else {
                                $count = 0;
                            }

                            if ($count == 0) {
                                $sup = Supplier::select('id')->where('supplier', $supplier)->first();
                                if ($sup) {
                                    $data['category_id'] = $cat->id;
                                    $data['supplier_id'] = $sup->id;
                                    $data['cnt'] = 0;
                                    SupplierCategoryCount::create($data);
                                }
                            }
                        }
                    }
                }
            }

            //Brand Count Save
            if($brand->supplierbrandcount){
                $count = $brand->supplierbrandcount->count();
            }else{
                $count = 0;
            }

            if($count == 0){
                $sup = Supplier::select('id')->where('supplier', 'like', '%' . $supplier . '%')->first();
                if($sup){
                    $data['brand_id'] = $brand->id;
                    $data['supplier_id'] = $sup->id;
                    $data['cnt'] = 0;
                    SupplierBrandCount::create($data);
                }
            }

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
}