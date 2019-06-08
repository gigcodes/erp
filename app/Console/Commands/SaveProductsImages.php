<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Image;
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

      // $products = Product::where('is_without_image', 1)->whereNotIn('supplier', ['Tiziana Fausti', 'Giglio Lamezia Terme', "Al Duca d'Aosta", 'Carofiglio Junior'])->get();
      // $products = Product::where('is_without_image', 1)->where('supplier', 'Tiziana Fausti')->get();
      $products = Product::where('is_without_image', 1)->where('supplier', 'Tiziana Fausti')->get();
      // $products = Product::where('is_without_image', 1)->where('sku', 'XXM45A00D80RE0')->get();

      // IGNOR GIGLIO !!!

      // dd(count($products));
      // $products = Product::where('is_without_image', 1)->where('sku', 'BE2007E00C001')->get();

      foreach ($products as $key => $product) {
        echo "$key - Found Product \n";

        if ($product->scraped_products) {
          dump("$key - Found Scraped Product - " . $product->scraped_products->sku);

          $images = $product->scraped_products->images;

          $product->detachMediaTags(config('constants.media_tags'));

          foreach ($images as $key2 => $image_path) {
            // Storage::disk('uploads')->delete('/social-media/' . $image_name);

            // $path = public_path('uploads') . '/social-media/' . $image_name;
            try {
              // $to_lower = $image_path;
              dump("$key2 - Trying save image");

              $to_lower = strtolower('https://www.tizianafausti.com/foto/i40/chloe/chlo%C3%89-sneakers-tizianafausti-chc18a0511843c_5_d.jpg');
              if (strpos($to_lower, '.jpg') !== false) {
                $formatted_final = substr($to_lower, 0, strpos($to_lower, '.jpg')) . '.jpg';
              } else if (strpos($to_lower, '.png') !== false) {
                $formatted_final = substr($to_lower, 0, strpos($to_lower, '.png')) . '.png';
              } else {
                $formatted_final = $to_lower;
              }

              // https://img.giglio.com/images/prodZoom/287288.066_4.jpg
              //
              // https://img.giglio.com/images/prodzoom/287288.066_4.jpg

              $formatted_final = str_replace(' ', '%20', $formatted_final);

              // $image_data = $data['data']['media']['preview']['image'];
              // $image_path2 = public_path() . '/uploads/' . uniqid(TRUE) . ".jpeg";
              // $img = Image::make($formatted_final)->encode('jpeg')->save($image_path2);

              $media = MediaUploader::fromSource($formatted_final)->upload();
              // dd($formatted_final);
              // $media = MediaUploader::fromSource('https://img.giglio.com/images/prodZoom/287288.066_4.jpg')->upload();

              // https://www.tizianafausti.com//FOTO/I39/GIVENCHY/GIVENCHY-MULES-TIZIANAFAUSTI-BE2007E00C001_3_D.JPG?2458483756
              // https://www.tizianafausti.com/foto/i39/givenchy/givenchy-mules-tizianafausti-be2007e00c001_3_d.jpg
              //
              // $path = 'https://i.stack.imgur.com/koFpQ.png';
              // $filename = basename('https://www.carofigliojunior.com/public/foto/SS19---KENZO---KN10258350.jpg');

              // $media = Image::make('https://www.carofigliojunior.com/public/foto/SS19---KENZO---KN10258350.jpg')->save(public_path('uploads/' . $filename));
              $product->attachMedia($media, config('constants.media_tags'));
              // dd($media);
              $product->is_without_image = 0;
              $product->save();
            } catch (\Exception $e) {
              echo "$key - Couldn't upload image " . $e->getMessage() . " - $product->sku \n";
              echo "$image_path \n";
            }

          }
          dd('stap');


        } else {
          echo "$key - Didn't find match - " . $product->sku . "\n";
        }
      }

      $report->update(['end_time' => Carbon:: now()]);
    }
}
