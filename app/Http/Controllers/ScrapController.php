<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Image;
use App\Imports\ProductsImport;
use App\Product;
use App\ScrapCounts;
use App\ScrapedProducts;
use App\ScrapEntries;
use App\ScrapActivity;
use App\Services\Scrap\GoogleImageScraper;
use App\Services\Scrap\PinterestScraper;
use App\Services\Products\GnbProductsCreator;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
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
      $date = Carbon::now()->subDays(7)->format('Y-m-d');

      $links_count = DB::select( '
									SELECT site_name, created_at, COUNT(*) as total FROM
								 		(SELECT scrap_entries.site_name, DATE_FORMAT(scrap_entries.created_at, "%Y-%m-%d") as created_at
								  		 FROM scrap_entries
								  		 WHERE scrap_entries.created_at > ?)
								    AS SUBQUERY
								   	GROUP BY created_at, site_name;
							', [$date]);

      $scraped_count = DB::select( '
									SELECT website, created_at, COUNT(*) as total FROM
								 		(SELECT scraped_products.website, DATE_FORMAT(scraped_products.created_at, "%Y-%m-%d") as created_at
								  		 FROM scraped_products
								  		 WHERE scraped_products.created_at > ?)
								    AS SUBQUERY
								   	GROUP BY created_at, website;
							', [$date]);

              // dd($scraped_count);

      $products_count = DB::select( '
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

              // dd($products_count);

      $activity_data = DB::select( '
									SELECT website, status, created_at, COUNT(*) as total FROM
								 		(SELECT scrap_activities.website, scrap_activities.status, DATE_FORMAT(scrap_activities.created_at, "%Y-%m-%d") as created_at
								  		 FROM scrap_activities
								  		 WHERE scrap_activities.created_at > ?)
								    AS SUBQUERY
								   	GROUP BY created_at, website, status;
							', [$date]);

      $data = [];

      // dd('stap');

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
      // dd($data);
      $data = array_reverse($data);

      $currentPage = LengthAwarePaginator::resolveCurrentPage();
  		$perPage = 24;
  		$currentItems = array_slice($data, $perPage * ($currentPage - 1), $perPage);


  		$data = new LengthAwarePaginator($currentItems, count($data), $perPage, $currentPage, [
  			'path'	=> LengthAwarePaginator::resolveCurrentPath()
  		]);

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

//        foreach ($suppliers as $supplier) {
//            $products = DB::table('scraped_products')
//                ->groupBy(['website', 'brand_id'])
//                ->selectRaw('COUNT(*), brand_id, website')
////                ->where('website', $supplier)
//                ->get()
//            ;
////        }

//        dd($products);

        return view('scrap.scraped_product_data', compact('products', 'request'));


    }


    public function showProducts($name, Request $request) {


        $products = ScrapedProducts::where('website', $name);
        if ($request->get('sku') !== '') {
            $sku = $request->get('sku');
            $products = $products->where(function($query) use ($sku) {
                $query->where('sku', 'LIKE', "%$sku%")
                    ->orWhere('title', 'LIKE', "%$sku%");
            });
        }

        $products = $products->latest()->paginate(20);

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

    public function addProductEntries(Request $request) {
        $this->validate($request, [
            'title' => 'required',
            'url' => 'required',
            'website' => 'required',
            'is_product_page' => 'required'
        ]);

        $scrapEntry = ScrapEntries::where('url', $request->get('url'))->first();
        if (!$scrapEntry) {
            $scrapEntry = new ScrapEntries();
        }

        $scrapEntry->url = $request->get('url');
        $scrapEntry->title = $request->get('title') ?? 'N/A';
        $scrapEntry->site_name = $request->get('website');
        $scrapEntry->is_product_page = $request->get('is_product_page');
        $scrapEntry->save();

        $date = date('Y-m-d');
        $allLinks = ScrapCounts::where('scraped_date', $date)->where('website', $request->get('website'))->first();
        if (!$allLinks) {
            $allLinks = new ScrapCounts();
            $allLinks->scraped_date = $date;
            $allLinks->link_count = 0;
            $allLinks->website = $request->get('website');
            $allLinks->save();
        }

        $allLinks->link_count = $allLinks->link_count + 1;
        $allLinks->save();

        return response()->json([
            'status' => 'Added items successfuly!'
        ]);
    }

    public function getProductsForImages() {
//        $products = Product::where('supplier', 'Monti')->where('is_farfetched', 0)->get();
        $products = Product::whereIn('supplier', ['Valenti'])->whereRaw('DATE(created_at) IN ("'.date('Y-m-d').'", "2019-06-20", "2019-06-19", "2019-06-21")')->get();
//        $products = Product::whereIn('supplier', ['Cuccini', 'Monti'])->where('is_farfetched', 0)->get();

        $productsToPush = [];


        foreach ($products as $product) {
//            if ($product->hasMedia(config('constants.media_tags'))) {
//                continue;
//            }

            $productsToPush[] = [
                'id' => $product->id,
                'sku' => $product->sku,
                'brand' => $product->brands ? $product->brands->name : '',
                'url' => $product->url,
                'supplier' => $product->supplier
            ];
        }

        return  response()->json($productsToPush);
    }

    public function saveImagesToProducts(Request $request) {
        $this->validate($request, [
            'id' => 'required',
            'website' => 'required',
            'images' => 'required|array',
            'description' => 'required'
        ]);

        $website = str_replace(' ', '', $request->get('website'));

        $product = Product::find($request->get('id'));
        $product->short_description = $request->get('description');
        $product->composition = $request->get('material_used');
        $product->color = $request->get('color');
        $product->description_link = $request->get('url');
        $dimension = $request->get('dimension');
        $product->made_in = $request->get('country');
        foreach ($dimension as $dimension) {
            if (stripos(strtoupper($dimension), 'WIDTH') !== false) {
                $width = str_replace(['WIDTH', 'CM', ' '], '', strtoupper($dimension));
                $product->lmeasurement = $width;
                echo "$width \n";
                $product->save();
                continue;
            }
            if (stripos(strtoupper($dimension), 'HEIGHT') !== false) {
                $width = str_replace(['HEIGHT', 'CM', ' '], '', strtoupper($dimension));
                echo "$width \n";
                $product->hmeasurement = $width;
                $product->save();
                continue;
            }
            if (stripos(strtoupper($dimension), 'DEPTH') !== false) {
                $width = str_replace(['DEPTH', 'CM', ' '], '', strtoupper($dimension));
                echo "$width \n";
                $product->dmeasurement = $width;
                $product->save();
                continue;
            }
        }


//        $product->detachMediaTags('gallery');
//
//        // Attach other information like description, etc..
//
//        if ($product->supplier == 'Valenti') {
//            $images = $this->downloadImagesForSites($request->get('images'), $website);
//            foreach ($images as $image_name) {
//                // Storage::disk('uploads')->delete('/social-media/' . $image_name);
//
//                $path = public_path('uploads') . '/social-media/' . $image_name;
//                $media = MediaUploader::fromSource($path)->upload();
//                $product->attachMedia($media,config('constants.media_tags'));
//            }
//
//            $product->is_without_image = 0;
//            $product->save();
//        }

        $product->is_farfetched = 1;
        $product->save();

        return response()->json([
            'status' => 'Added items successfuly!'
        ]);

    }

    public function syncProductsFromNodeApp(Request $request) {
        $this->validate($request, [
            'sku' => 'required|min:5',
            'url' => 'required',
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
        $new = false;
        if (!$product) {
            $product = new ScrapedProducts();
            $new = true;
        }

        if ($new === true) {
            $images = $request->get('images') ?? [];
            $images = $this->downloadImagesForSites($images, strtolower($request->get('website')));
            if ($images !== []) {
                $product->images = $images;
            }
        }

        if (($new === false) && count($product->images)) {
            $images = $request->get('images') ?? [];
            $images = $this->downloadImagesForSites($images, strtolower($request->get('website')));
            if ($images !== []) {
                $product->images = $images;
            }
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

        app('App\Services\Products\ProductsCreator')->createProduct($product);

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
                echo $datum . "\n";
            } catch (\Exception $exception) {
                continue;
            }

            $fileName = $prefix . '_' . md5(time() .'_'. rand(5,9999999)).'.png';
            Storage::disk('uploads')->put('social-media/'.$fileName, $imgData);

            echo "$fileName \n";
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

    public function saveSupplier(Request $request) {
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
            'phone'	=> str_replace('+', '', $request->get('phone')),
            'address' => $request->get('address'),
            'website' => $request->get('website'),
            'email'	=> $request->get('email'),
            'social_handle'	=> $request->get('social_handle'),
            'instagram_handle' => $request->get('instagram_handle'),
        ];

        Supplier::create($params);

        return response()->json([
            'message' => 'Added successfully!'
        ]);

    }
}
