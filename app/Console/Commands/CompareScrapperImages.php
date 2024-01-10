<?php

namespace App\Console\Commands;
use App\scraperImags;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CompareScrapperImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compare-scrapper-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare Scrapper Images';

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
        try {
            Log::info('Start Compare Scrapper Images');

            $scraperImagsData = scraperImags::where('compare_flag', 0)->where('url', '!=', '')->orderBy('id', 'DESC')->get();
            if(!empty($scraperImagsData)){
                foreach ($scraperImagsData as $scraperImag) {

                    if(!empty($scraperImag->img_name)){

                        Log::info('Main - '.$scraperImag->url);

                        $scraperImagscData = scraperImags::where('url', $scraperImag->url)->where('id', '!=', $scraperImag->id)->orderBy('id', 'DESC')->first();

                        if(!empty($scraperImagscData)){

                            Log::info($scraperImagscData->url);

                            if(!empty($scraperImagscData->img_name) && !empty($scraperImag->img_name)){

                                // Load the images
                                $image1 = asset( 'scrappersImages/'.$scraperImag->img_name);
                                $image2 = asset( 'scrappersImages/'.$scraperImagscData->img_name);

                                // Calculate hashes for both images
                                $imageHash = new ImageHash(new DifferenceHash());
                                $hash1 = $imageHash->hash($image1);
                                $hash2 = $imageHash->hash($image2);

                                // Compare the hashes
                                $hammingDistance = $hash1->distance($hash2);
                                $similarityThreshold = 5; // Set a threshold for similarity

                                if ($hammingDistance <= $similarityThreshold) {
                                    $scraperImagscData->manually_approve_flag = 0;
                                } else {
                                    $scraperImagscData->manually_approve_flag = 1;
                                }
                            }                            

                            $scraperImagscData->compare_flag = 1;

                            $scraperImagscData->save();

                        }
                    }
                }
            }

            Log::info('End Compare Scrapper Images');
            
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
