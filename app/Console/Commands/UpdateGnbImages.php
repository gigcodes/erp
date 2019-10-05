<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Product;
use App\ScrapedProducts;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class UpdateGnbImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:gnb-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
      $scraped_products = ScrapedProducts::where('website', 'G&B')->get();

      foreach ($scraped_products as $scraped_product) {
        if ($scraped_product->product) {
          if ($scraped_product->product->hasMedia(config('constants.media_tags'))) {
            dump('MEDIA');
          } else {
            $images = $scraped_product->images;

            foreach ($images as $image_name) {
              $path = public_path('uploads') . '/social-media/' . $image_name;
              $media = MediaUploader::fromSource($path)
                                    ->toDirectory('product/'.floor($scraped_product->product->id / config('constants.image_par_folder')))
                                    ->upload();
              $scraped_product->product->attachMedia($media,config('constants.media_tags'));
            }
          }
        }
      }

      dd('stap');
    }
}
