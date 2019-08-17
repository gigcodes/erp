<?php

namespace App\Http\Controllers\Products;

use App\Product;
use File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ProductEnhancementController extends Controller
{
    public function index() {

        $orderByPritority = "CASE WHEN supplier IN ('G & B Negozionline', 'Tory Burch', 'Wise Boutique', 'Biffi Boutique (S.P.A.)', 'MARIA STORE', 'Lino Ricci Lei', 'Al Duca d\'Aosta', 'Tiziana Fausti', 'Leam') THEN 0 ELSE 1 END";

        $product = Product::where('is_enhanced', 0)
            ->where('is_crop_ordered', 1)
            ->where('is_approved', 1)
            ->orderByRaw($orderByPritority)
            ->orderBy('is_approved', 'DESC')
            ->first();
        $productImages = $imgs = $product->media()->get();
        $productUrls = [];

        foreach ($productImages as $image) {
            $productUrls[] = $image->getUrl();
        }

        return response()->json([
            'id' => $product->id,
            'images' => $productUrls
        ]);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'images' => 'required',
            'id' => 'required'
        ]);

        $product = Product::find($request->get('id'));
        $files = $request->allFiles();

        if ($files !== []) {
            $this->deleteCroppedImages($product);
            foreach ($files['images'] as $file) {
                $media = MediaUploader::fromSource($file)->useFilename(uniqid('cropped_', true))->upload();
                $product->attachMedia($media, 'gallery');
            }
        }

        $product->is_enhanced = 1;
        $product->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    private function deleteCroppedImages( $product )
    {
        if ( $product->hasMedia( config( 'constants.media_tags' ) ) ) {
            foreach ( $product->getMedia( config( 'constants.media_tags' ) ) as $key => $image ) {
                $image_path = $image->getAbsolutePath();

                if ( File::exists( $image_path ) ) {
                    try {
                        File::delete( $image_path );
                    } catch ( \Exception $exception ) {

                    }
                }
                try {
                    $image->forceDelete();
                } catch (\Exception $exception) {
//                    echo $exception->getMessage();
                }
            }
        }
    }
}
