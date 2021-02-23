<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use seo2websites\ErpExcelImporter\ErpExcelImporter;
use Illuminate\Support\Facades\File;
use App\Product;
use App\CroppedImageReference;

class RejectDuplicateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RejectDuplicateImages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Product duplicate image auto reject';

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
        
        $product_list = CroppedImageReference::where('product_id','296559')->first();
        
        
        
        foreach ($product_list->differentWebsiteImages as $key ) {
            // dd(  public_path('product/' . floor($product_list->product_id / config('constants.image_per_folder')) . '/' . $product_list->product_id)  );
            dd(   public_path( $key->newMedia->directory.'/'.$key->newMedia->filename.'.'.$key->newMedia->extension ) );
        }

        // $dir = "/full/path/to/images";
        $dir = public_path('scrappersImages');

        $checksums = array();

        if ($h = opendir($dir)) {
            while (($file = readdir($h)) !== false) {

                // skip directories
                if(is_dir($_="{$dir}/{$file}")) continue;
                
                $hash = hash_file('md5', $_);

                // delete duplicate
                if (in_array($hash, $checksums)) {
                    unlink($_);
                }
                // add hash to list
                else {
                    $checksums[] = $hash;
                }
            }
            closedir($h);
        }

        $this->output->write('Cron complated', true);
    }
}