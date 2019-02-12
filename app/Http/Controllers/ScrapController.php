<?php

namespace App\Http\Controllers;

use App\Image;
use App\ScrapedProducts;
use App\Services\Scrap\GoogleImageScraper;
use Illuminate\Http\Request;
use Storage;

class ScrapController extends Controller
{
    private $googleImageScraper;

    public function __construct(GoogleImageScraper $googleImageScraper)
    {
        $this->googleImageScraper = $googleImageScraper;
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
        $data = $this->googleImageScraper->scrapGoogleImages($q, $chip, $noi);

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
