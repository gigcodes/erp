<?php

namespace App\Http\Controllers;

use App\Image;
use App\Services\Scrap\Scraper;
use Illuminate\Http\Request;
use Storage;

class ScrapController extends Controller
{
    private $scraper;

    public function __construct(Scraper $scraper)
    {
        $this->scraper = $scraper;

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
        $data = $this->scraper->scrapGoogleImages($q, $noi);

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
}
