<?php

namespace App\Http\Controllers;

use App\Image;
use App\ScrapedProducts;
use App\Services\Scrap\GoogleImageScraper;
use App\Services\Scrap\PinterestScraper;
use App\Services\Products\GnbProductsCreator;
use Illuminate\Http\Request;
use Storage;

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


    public function showProducts($name) {
        $products = ScrapedProducts::where('website', $name)->paginate(20);
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

        $this->gnbCreator->createGnbProducts($product);

        return response()->json([
            'status' => 'success',
            'message' => 'Created or Updated successfully!'
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
}
