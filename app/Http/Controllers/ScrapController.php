<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Image;
use App\Imports\ProductsImport;
use App\ScrapCounts;
use App\ScrapedProducts;
use App\ScrapEntries;
use App\ScrapActivity;
use App\Services\Scrap\GoogleImageScraper;
use App\Services\Scrap\PinterestScraper;
use App\Services\Products\GnbProductsCreator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Storage;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function index() {
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

        return view('scrap.extracted_images', compact( 'googleData', 'pinterestData'));

    }

    public function downloadImages(Request $request) {
        $this->validate($request, [
            'data' => 'required|array'
        ]);
        $data = $request->get('data');

        $images = [];

        foreach ($data as $key=>$datum) {
            try {
                $imgData = file_get_contents($datum);
            } catch (\Exception $exception) {
                continue;
            }

            $fileName = md5(time()).'.png';
            Storage::disk('uploads')->put('social-media/'.$fileName, $imgData);

            $i = new Image();
            $i->filename = $fileName;
            $i->save();

            $images[] = $fileName;
        }

        $downloaded = true;


        return view('scrap.extracted_images', compact( 'images', 'downloaded'));

    }

    public function activity()
    {
      $links_count = ScrapEntries::select(['site_name', 'created_at'])->get()->groupBy(['site_name', function ($query) {
        return Carbon::parse($query->created_at)->format('Y-m-d');
      }]);


      $scraped_count = ScrapedProducts::select(['website', 'created_at'])->get()->groupBy(['website', function ($query) {
        return Carbon::parse($query->created_at)->format('Y-m-d');
      }]);


      $products_count = ScrapedProducts::select(['website', 'created_at'])->whereHas('Product')->get()->groupBy(['website', function ($query) {
        return Carbon::parse($query->created_at)->format('Y-m-d');
      }]);


      $activity_data_removed = ScrapActivity::select(['website', 'status', 'created_at'])->where('status', 0)->get()->groupBy(['website', function ($query) {
        return Carbon::parse($query->created_at)->format('Y-m-d');
      }]);

      $activity_data_inventory = ScrapActivity::select(['website', 'status', 'created_at'])->where('status', 1)->get()->groupBy(['website', function ($query) {
        return Carbon::parse($query->created_at)->format('Y-m-d');
      }]);

      $data = [];

      $link_entries = ScrapCounts::orderBy('created_at', 'DESC')->get();

      foreach ($links_count as $website => $dates) {
        if ($website == 'GNB') {
          $website = 'G&B';
        }

        foreach ($dates as $date => $item) {
          $data[$date][$website]['links'] = count($item);
        }
      }

      foreach ($scraped_count as $website => $dates) {
        foreach ($dates as $date => $item) {
          $data[$date][$website]['scraped'] = count($item);
        }
      }

      foreach ($products_count as $website => $dates) {
        foreach ($dates as $date => $item) {
          $data[$date][$website]['created'] = count($item);
        }
      }

      foreach ($activity_data_removed as $website => $dates) {
        foreach ($dates as $date => $item) {
          $data[$date][$website]['removed'] = count($item);
        }
      }

      foreach ($activity_data_inventory as $website => $dates) {
        foreach ($dates as $date => $item) {
          $data[$date][$website]['inventory'] = count($item);
        }
      }

      ksort($data);

      $data = array_reverse($data);

      $currentPage = LengthAwarePaginator::resolveCurrentPage();
  		$perPage = 24;
  		$currentItems = array_slice($data, $perPage * ($currentPage - 1), $perPage);

  		$data = new LengthAwarePaginator($currentItems, count($data), $perPage, $currentPage, [
  			'path'	=> LengthAwarePaginator::resolveCurrentPath()
  		]);

  		// $data['scraped_gnb_product_count'] = Product::where('supplier', 'G & B Negozionline')->where('is_scraped', 1)->whereBetween('created_at', [$start, $end])->get()->count();
  		// $data['scraped_wise_product_count'] = Product::where('supplier', 'Wise Boutique')->where('is_scraped', 1)->whereBetween('created_at', [$start, $end])->get()->count();
  		// $data['scraped_double_product_count'] = Product::where('supplier', 'Double F')->where('is_scraped', 1)->whereBetween('created_at', [$start, $end])->get()->count();

      return view('scrap.activity', [
        'data'  => $data,
        'link_entries'  => $link_entries
      ]);
    }

    public function showProductStat(Request $request) {
        $brands = Brand::whereNull('deleted_at')->get();
        $products = [];
        $suppliers = DB::table('scraped_products')->selectRaw('DISTINCT(`website`)')->pluck('website');

        foreach ($suppliers as $supplier) {
            foreach ($brands as $brand) {
                $products[$supplier][$brand->name] = ScrapedProducts::where('website', $supplier)
                    ->where('brand_id', $brand->id);
                if ($request->has('start_date') && $request->has('end_date')) {
                    $products[$supplier][$brand->name] = $products[$supplier][$brand->name]->whereBetween('created_at', [$request->get('start_date'), $request->get('end_date')]);
                }

                $products[$supplier][$brand->name] = $products[$supplier][$brand->name]->count();
            }
        }

        return view('scrap.scraped_product_data', compact('products', 'request'));


    }


    public function showProducts($name) {
        $products = ScrapedProducts::where('website', $name)->latest()->paginate(20);
        $title = $name;
        return view('scrap.scraped_images', compact('products', 'title'));
    }

    public function syncGnbProducts(Request $request) {
        $this->validate($request, [
            'sku' => 'required'
        ]);

        $product = ScrapedProducts::where('sku', $request->get('sku'))->first();

        if (!$product) {
            $product = new ScrapedProducts();
        }

        $product->fill($request->except(['sku', 'images']));

//        return $request->all();
//        $product->images = $this->downloadImagesForSites($request->get('images'), 'gnb');
        $product->save();

//        $this->gnbCreator->createGnbProducts($product);

        return response()->json([
            'status' => 'success',
            'message' => 'Created or Updated successfully!'
        ]);

    }

    public function syncProductsFromNodeApp(Request $request) {
        $this->validate($request, [
            'sku' => 'required|min:5',
            'url' => 'required',
            'images' => 'required|array',
            'properties' => 'required',
            'website' => 'required',
            'price' => 'required',
            'brand' => 'required'
        ]);

        $brand = Brand::where('name', $request->get('brand'))->first();

        if  (!$brand) {
            return response()->json([
                'status' => 'invalid_brand'
            ]);
        }

        $scrapEntry = ScrapEntries::where('url', $request->get('url'))->first();
        if (!$scrapEntry) {
            $scrapEntry = new ScrapEntries();
        }

        $scrapEntry->url = $request->get('url');
        $scrapEntry->title = $request->get('title') ?? 'N/A';
        $scrapEntry->site_name = $request->get('website');
        $scrapEntry->is_product_page = 1;
        $scrapEntry->save();

        $product = ScrapedProducts::where('sku', $request->get('sku'))->first();
        if (!$product) {
            $product = new ScrapedProducts();
            $images = $request->get('images') ?? [];
            $images = $this->downloadImagesForSites($images, strtolower($request->get('website')));
            $product->images = $images;
        }

        $product->sku = $request->get('sku');
        $product->has_sku = 1;
        $product->url = $request->get('url');
        $product->title = $request->get('title') ?? 'N/A';
        $product->description = $request->get('description');
        $product->properties = $request->get('properties');
        $product->price = $request->get('price');
        $product->website = $request->get('website');
        $product->brand_id = $brand->id;
        $product->save();

        app('App\Services\Products\LidiaProductsCreator')->createProduct($product);

        return response()->json([
            'status' => 'Added items successfuly!'
        ]);

    }

    private function downloadImagesForSites($data, $prefix = 'img'): array
    {

        $images = [];
        foreach ($data as $key=>$datum) {
            try {
                $imgData = file_get_contents($datum);
            } catch (\Exception $exception) {
                continue;
            }

            $fileName = $prefix . '_' . md5(time()).'.png';
            Storage::disk('uploads')->put('social-media/'.$fileName, $imgData);

            $images[] = $fileName;
        }

        return $images;
    }



    public function excel_import() {
        $products = ScrapedProducts::where('website', 'EXCEL_IMPORT_TYPE_1')->paginate(25);
        return view('scrap.excel', compact('products'));
    }

    public function excel_store(Request $request) {
        $this->validate($request, [
            'file' => 'required|file'
        ]);

        $file = $request->file('file');

        if ($file->getClientOriginalExtension() == 'xlsx') {
            $reader = new Xlsx();
        } else if ($file->getClientOriginalExtension() == 'xls') {
            $reader = new Xls();
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
                $zipReader = fopen($drawing->getPath(),'r');
                $imageContents = '';
                while (!feof($zipReader)) {
                    $imageContents .= fread($zipReader,1024);
                }
                fclose($zipReader);
                $extension = $drawing->getExtension();
            }

            $myFileName = '00_Image_'.++$i.'.'.$extension;
            file_put_contents('uploads/social-media/'.$myFileName,$imageContents);
            $cells[substr($drawing->getCoordinates(), 2)][] = $myFileName;
        }

        $cells_new = [];
        $c = 0;
        foreach ($cells as $cell) {
            $cells_new[$c] = $cell;
            $c++;
        }

        $files = Excel::toArray(new ProductsImport(), $file);
        $th = [];

        foreach ($files[0] as $key=>$file) {
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

        foreach ($th as $key=>$file) {
            if ($file) {
                $fields_only_with_keys[$key] = $file;
            }
        }

        $dataToSave = [];

        foreach ($files[0] as $pkey=>$row) {
            $null_count = 0;
            foreach ($row as $item) {
                if ($item===null) $null_count++;
            }
            if ($null_count > 30) unset($files[0][$pkey]);
        }

        $c = 0;
        foreach ($files[0] as $pkey=>$row) {
            foreach ($fields_only_with_keys as $key=>$item) {
                $dataToSave[$pkey][$item] = $row[$key];
                if ($item == 'COD. FOTO') {
                    $dataToSave[$pkey][$item] = $cells_new[$c];
                }
            }
            $c++;
        }

        foreach ($dataToSave as $item) {
            $sku = $item['MODELLO VARIANTE COLORE'] ?? null;
            if (!$sku) {
                continue;
            }

            $brand = Brand::where('name', $item['BRAND'] ?? 'UNKNOWN_BRAND_FROM_FILE')->first();

            if (!$brand) {
                continue;
            }

            $sp = new ScrapedProducts();
            $sp->website = 'EXCEL_IMPORT_TYPE_1';
            $sp->sku = $sku;
            $sp->has_sku = 1;
            $sp->brand_id = $brand->id;
            $sp->title = $sku;
            $sp->description = $item['description'] ?? null;
            $sp->images = $item['COD. FOTO'] ?? [];
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
