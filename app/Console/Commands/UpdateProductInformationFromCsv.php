<?php

namespace App\Console\Commands;

use App\WebsiteProductCsv;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\ProductPushInformation;


class UpdateProductInformationFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-product:from-csv';

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
        $row = 0;
        $arr_id = [];
        $is_file_exists = null;
        $prodcutInformation = WebsiteProductCsv::pluck('path', 'store_website_id');

        foreach ($prodcutInformation as $file_url) {

            $client   = new Client();
            if (!$file_url) {
                $this->error('Plese add url');
            } else {

                try {

                    // $response = $client->get($url);
                    $promise = $client->request('GET', $file_url);
                    $is_file_exists = true;
                } catch (ClientException $e) {
                    $this->error('file not exists');
                }
    
    
                if ($is_file_exists &&   ($handle = fopen($file_url, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $row++;
                        if ($row > 1) {
                            // dd($data);
                            $updated =   ProductPushInformation::updateOrCreate(['product_id' => $data[0]], [
                                'product_id' => $data[0],
                                'sku' => $data[1],
                                'status' => $data[2],
                                'quantity' => $data[3],
                                'stock_status' => $data[4],
                            ]);
                            $arr_id[] = $updated->product_id;
                        }
                    }
                    fclose($handle);
                    $this->info('product updaetd successfully');
                }

            }
            ProductPushInformation::whereNotIn('product_id', $arr_id)->delete();
        }
    }
}
