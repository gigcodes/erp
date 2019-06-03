<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Illuminate\Console\Command;

class SaveProductsImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:products-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saves Products images on ERP server after it has been scraped on another';

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
      $report = CronJobReport::create([
        'signature' => $this->signature,
        'start_time'  => Carbon::now()
      ]);

      $products = Product::where('is_without_image', 1)->get();

      foreach ($products as $key => $product) {
        echo "$key - Found Product \n";

        if ($product->scraped_products) {
          echo "$key - Found Scraped Product \n";

          $images = $product->scraped_products->images;

          // $product->detachMediaTags(config('constants.media_tags'));

          foreach ($images as $image_path) {
            // Storage::disk('uploads')->delete('/social-media/' . $image_name);

            // $path = public_path('uploads') . '/social-media/' . $image_name;
            try {
              $media = MediaUploader::fromSource($image_path)->upload();
              $product->attachMedia($media, config('constants.media_tags'));
            } catch (\Exception $e) {
              echo "$key - Couldn't upload image " . $e->getMessage() . " - $product->sku \n";
            }
          }

          $product->is_without_image = 0;
          $product->save();
        } else {
          echo "$key - Didn't find match - " . $product->sku . "\n";
        }
      }

      $report->update(['end_time' => Carbon:: now()]);
    }
}
