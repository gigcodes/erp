<?php

namespace App\Console\Commands;

use App\AttributeReplacement;
use App\Product;
use Illuminate\Console\Command;

class ReplaceTextsFromProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:replace-text';

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

        $replacements = AttributeReplacement::all();

        Product::where('is_approved', 0)->chunk(1000, function($products) use ($replacements) {
            foreach ($products as $product)  {

                foreach ($replacements as $replacement) {
                    if ($replacement == 'name') {
                        $product->name = str_replace($replacement->first_term, $replacement->replacement_term ?? '', $product->name);
                    }

                    if ($replacement == 'composition') {
                        $product->composition = str_replace($replacement->first_term, $replacement->replacement_term ?? '', $product->composition);
                    }

                    if ($replacement == 'short_description') {
                        $product->short_description = str_replace($replacement->first_term, $replacement->replacement_term ?? '', $product->short_description);
                    }
                }

                $product->save();
            }
        });
    }
}
