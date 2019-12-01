<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Marketing\WhatsappConfig;

class IncrementFrequencyWhatsappConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsppconfig:frequency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Increment Frequency For WHatsapp Config Till 10';

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
        $configs = WhatsappConfig::where('is_customer_support',0)->get();

        foreach ($configs as $config) {

            if($config->frequency != 10 && $config->frequency < 10){

                $config->frequency = ($config->frequency+1);
                $config->update();
                dump('Frequency Updated');

            }
        }
    }
}
