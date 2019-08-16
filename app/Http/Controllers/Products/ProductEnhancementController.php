<?php

namespace App\Http\Controllers\Products;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ProductEnhancementController extends Controller
{
    public function index() {
        $product = Product::where('is_enhanced', 0)->where('is_crop_ordered', 1)->orderBy('is_approved', 'DESC')->first();
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

        dd($request->all(), $files);

        if ($files !== []) {
            $product->detachMedia(config('constants.media_tags'));
            foreach ($files as $file) {
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
}
