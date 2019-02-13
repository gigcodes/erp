<?php

namespace App\Http\Controllers;

use App\Image;
use App\ScrapedProducts;
use App\Services\Scrap\GoogleImageScraper;
use App\Services\Scrap\PinterestScraper;
use Illuminate\Http\Request;
use Storage;

class ScrapController extends Controller
{
    private $googleImageScraper;
    private $pinterestScraper;

    public function __construct(GoogleImageScraper $googleImageScraper, PinterestScraper $pinterestScraper)
    {
        $this->googleImageScraper = $googleImageScraper;
        $this->pinterestScraper = $pinterestScraper;
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
            $pinterestData = $this->pinterestScraper->scrap($q, $noi);
        }

        if ($request->get('google') === 'on') {
            $googleData = $this->googleImageScraper->scrapGoogleImages($q, $chip, $noi);
        }

        $data = array_merge($pinterestData, $googleData);



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

        return view('scrap.extracted_images', compact('images'));

    }


    public function showGnbProducts() {
        $products = ScrapedProducts::where('website', 'G&B')->paginate(20);
        $title = 'G&B Products';
        return view('scrap.scraped_images', compact('products', 'title'));
    }
}
