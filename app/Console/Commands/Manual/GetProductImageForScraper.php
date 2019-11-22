<?php

namespace App\Console\Commands\Manual;

use Illuminate\Console\Command;
use App\Product;
use App\ScrapedProducts;
use App\Helpers\StatusHelper;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;

class GetProductImageForScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:image-scraper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets the images for product';

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
        //Getting All Products
        $scrapedProducts = ScrapedProducts::all();

        foreach ($scrapedProducts as $scrapedProduct) {

            //get products from scraped products
            $product = $scrapedProduct->product;

            //check if scraped product has product
            if ($product != null && $product != '') {
                //check if product has media
                if ($product->hasMedia(\Config('constants.media_tags'))) {
                    dump('Product has media');
                } else {
                    //check if scrapedProduct has images
                    if ($scrapedProduct->images == null && $scrapedProduct->images == '') {
                        continue;
                    }
                    //if product does not have media loop over images
                    $countImageUpdated = 0;
                    foreach ($scrapedProduct->images as $image) {
                        //check if image has http or https link
                        if (strpos($image, 'http') === false) {
                            continue;
                        }

                        try {
                            //generating image from image
                            $jpg = \Image::make($image)->encode('jpg');
                        } catch (\Exception $e) {
                            // if images are null
                            $jpg = null;
                        }
                        if ($jpg != null) {
                            $filename = substr($image, strrpos($image, '/'));
                            $filename = str_replace("/", "", $filename);
                            try {
                                if (strpos($filename, '.png') !== false) {
                                    $filename = str_replace(".png", "", $filename);
                                }
                                if (strpos($filename, '.jpg') !== false) {
                                    $filename = str_replace(".jpg", "", $filename);
                                }
                                if (strpos($filename, '.JPG') !== false) {
                                    $filename = str_replace(".JPG", "", $filename);
                                }
                            } catch (\Exception $e) {
                                //
                            }
                            //save image to media
                            $media = MediaUploader::fromString($jpg)->toDirectory('/product/' . floor($product->id / 10000) . '/' . $product->id)->useFilename($filename)->upload();
                            $product->attachMedia($media, config('constants.media_tags'));
                            $countImageUpdated++;
                        }
                    }
                    if ($countImageUpdated != 0) {
                        $product->status_id = StatusHelper::$AI;
                        $product->save();
                        // Call status update handler
                        StatusHelper::updateStatus($product, StatusHelper::$AI);
                        dump('images saved for product ID ' . $product->id);
                    }
                }
            }
        }
    }
}
