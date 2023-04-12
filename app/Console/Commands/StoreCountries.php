<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;

use Http;

class StoreCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for store countries.';

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
     * @return int
     */
    public function handle()
    {
        // API Reference: https://countrystatecity.in/docs/api/all-countries/
        $response = Http::withHeaders([
            'X-CSCAPI-KEY' => 'WUZWeG9GbFpXMnhEcmRBNUZzN0JIYXpuN1FlMTd3eG1YR2duRnlwRA==',
        ])->get('https://api.countrystatecity.in/v1/countries')->json();

        if(!@$response['error']){
            
            foreach($response as $value){

                $input = array(
                    "name" => $value['name'],
                    "code" => $value['iso2'],
                );

                Country::updateOrCreate($input);

                $this->info("Stored country: ". $value['name']);
            }

        }
    }
}
