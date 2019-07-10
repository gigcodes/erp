<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;

class BringBrunoCompositionFromScraped extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:bruno-russo-composition';

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
        Product::where('supplier', 'BRUNA ROSSO')->chunk(1000, function($products) {
            foreach ($products as $product) {
//                $pro
            }
        });
    }
}
