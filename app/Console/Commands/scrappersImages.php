<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Order;
use App\Wetransfer;
use App\Website;
use App\scraperImags;
use seo2websites\ErpExcelImporter\ErpExcelImporter;
use Illuminate\Support\Facades\File;

class scrappersImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrappersImages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all websites images';

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
        $queuesList = Website::get()->toArray();

        if (!file_exists( public_path('scrappersImages') )) {
            mkdir( public_path('scrappersImages'), 0777, true );
        }

        if ( !empty( $queuesList ) ) {
            foreach ($queuesList as $list) {
                $file  = $this->downloadImages( $list['name'] );
            }
        }

        $this->output->write('Cron complated', true);
    }

    /**
     * Download Wefransfer Files 
     * @return mixed
     */
    private function downloadImages( $website = null )
    {
        $WETRANSFER_API_URL = 'https://wetransfer.com/api/v4/transfers/';

        try {

            // create & initialize a curl session
            $curl = curl_init();

            // set our url with curl_setopt()
            curl_setopt($curl, CURLOPT_URL, "http://45.32.148.193:8100/get-images");

            // return the transfer as a string, also with setopt()
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            // curl_exec() executes the started curl session
            // $output contains the output string
            $output = curl_exec($curl);
            
            $output = json_decode( $output );
            // die;
            if (  isset( $output->status ) && $output->status == true ){

                if (!file_exists( public_path('scrappersImages') )) {
                    mkdir( public_path('scrappersImages'), 0777, true );
                }
                if( !empty( $output->response->images ) ){
                    foreach ( $output->response->images as $image ) {
                        if ( !empty( $image ) ) {
                            $file_name = uniqid().trim( basename($image) );
                            if ( $this->saveBase64Image( $file_name,  $image ) ) {

                                $newImage = array(
                                    'website_id' => 756,
                                    'img_name'   => $file_name,
                                    'img_url'    => $file_name,
                                );
                                scraperImags::insert( $newImage );
                            }
                        }
                    }
                }
            }
            // close curl resource to free up system resources
            // (deletes the variable made by curl_init)
            curl_close($curl);

        } catch (\Throwable $th) {

            $this->output->write( $th->getMessage() , true );
            return false;  
        }
        return false;
    }

    public function saveBase64Image( $file_name, $base64Image )
    {   
        try {

            $curl = curl_init();
            // set our url with curl_setopt()
            curl_setopt($curl, CURLOPT_URL, "http://45.32.148.193:8100/get-images-url?".$base64Image);

            // return the transfer as a string, also with setopt()
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            // curl_exec() executes the started curl session
            // $output contains the output string
            $output = curl_exec($curl);
            
            $output = json_decode( $output );

            if( $output->status == true ){

                $base64Image = $output->response;

                $base64Image = trim($base64Image);
                $base64Image = str_replace('data:image/png;base64,', '', $base64Image);
                $base64Image = str_replace('data:image/jpg;base64,', '', $base64Image);
                $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
                $base64Image = str_replace('data:image/gif;base64,', '', $base64Image);
                $base64Image = str_replace(' ', '+', $base64Image);
                $imageData = base64_decode( $base64Image );
        
                // //Set image whole path here 
                $filePath = public_path('scrappersImages').'/' . $file_name;
                file_put_contents($filePath, $imageData);
                return true;
            }
            //set name of the image file
            return true;
        } catch (\Throwable $th) {
            $this->output->write( $th->getMessage() , true );
            return false;
        }
    }
}
