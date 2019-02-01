<?php

namespace App\Http\Controllers;

use App\Image;
use App\Services\Scrap\GebnegozionlineScraper;
use App\Services\Scrap\GoogleImageScraper;
use Illuminate\Http\Request;
use Storage;

class ScrapController extends Controller
{
    private $googleImageScraper;
    private $gebnegozionlineScraper;

    public function __construct(GoogleImageScraper $googleImageScraper, GebnegozionlineScraper $gebnegozionlineScraper)
    {
        $this->googleImageScraper = $googleImageScraper;
        $this->gebnegozionlineScraper = $gebnegozionlineScraper;

    }

    public function index() {
        return view('scrap.index');
    }

    public function scrapGoogleImages(Request $request)
    {
        $this->validate($request, [
            'query' => 'required',
            'noi' => 'required'
        ]);

        $q = $request->get('query');
        $noi = $request->get('noi');
        $data = $this->googleImageScraper->scrapGoogleImages($q, $noi);

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

    public function scrapTest() {
        $this->gebnegozionlineScraper->scrap();
    }
}
