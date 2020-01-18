<?php

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ImageBarcodeGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'barcode-generator-product:run {product_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Barcode into product';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $productId = $this->argument('product_id');

        $file_types = array(
            'gif',
            'jpg',
            'jpeg',
            'png',
            'pdf',
        );

        /*$products = \App\Product::join("mediables as m",function($q){
        $q->on("m.mediable_id","products.id")
        ->where("m.mediable_type",\App\Product::class)
        ->whereIn("tag",config('constants.media_tags'));
        })->join("media as me","me.id","m.media_id")
        ->select("products.*")
        ->limit(1)->get();*/

        $whereString = "where is_barcode_check is null";
        if (!empty($productId) && $productId > 0) {
            $whereString = " where p.id = " . $productId . " ";
        }

        $productQuery = \DB::select('select p.id, count(*) as total_image,(select count(*) from mediables as m2 where m2.mediable_id = p.id and m2.tag  = "barcode" group by m2.mediable_id ) as total_barcode from products as p
        left join mediables as md on md.mediable_id  = p.id and md.tag  = "gallery"
        left join media as m on m.id  = md.media_id
        ' . $whereString . '
        group by p.id having (total_image != total_barcode or total_barcode is null) limit 100');

        if (!empty($productQuery)) {
            foreach ($productQuery as $res) {
                $product = \App\Product::where("id", $res->id)->first();

                echo $product->id . " Started \n";

                if ($product && $product->hasMedia(config('constants.media_tags'))) {
                    $medias = $product->getMedia(config('constants.media_tags'));
                    foreach ($medias as $media) {
                        // set path
                        try {
                            $img           = \IImage::make($media->getAbsolutePath());
                            $filename      = pathinfo($media->filename, PATHINFO_FILENAME);
                            $barcodeString = \DNS1D::getBarcodePNGPath($product->id, "EAN13", 3, 77, array(1, 1, 1), true);
                            $img->insert(public_path($barcodeString), 'bottom-right', 10, 10);
                            $fontSize = 50;

                            $brand_name    = $product->brands->name ?? '';
                            $special_price = (int) $product->price_special_offer > 0 ? (int) $product->price_special_offer : $product->price_special;
                            $special_price = ($special_price > 0) ? $special_price : "";
                            $auto_message  = $brand_name . "\n" . $product->name . "\n" . $special_price;

                            $img->text($auto_message, 10, 10, function ($font) use ($fontSize) {
                                $font->file(public_path('/fonts/Arial.ttf'));
                                $font->size(20);
                                $font->valign('top'); //top, bottom or middle.
                            });

                            $filenameNew = $media->id . "." . $media->extension;

                            $path = public_path() . "/uploads/product-barcode/" . get_folder_number($media->id) . "/";
                            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

                            $img->save($path . $filenameNew);

                            $media = MediaUploader::fromSource(public_path("/uploads/product-barcode/" . get_folder_number($media->id) . "/") . $filenameNew)
                                ->toDirectory('uploads/product-barcode/' . get_folder_number($media->id) . '/')
                                ->setOnDuplicateBehavior("replace")
                                ->upload();
                            $product->attachMedia($media, config('constants.media_barcode_tag'));
                            File::delete(public_path($barcodeString));
                        } catch (\Exception $e) {
                           \Log::info($e->getMessage()." || Product ".$product->id." having issue in image barcode and image stored on : ".$media->getAbsolutePath());
                        }
                    }
                }

                $product->is_barcode_check = 1;
                $product->save();
                echo $product->id . " Ended \n";
            }
        }
    }
}
