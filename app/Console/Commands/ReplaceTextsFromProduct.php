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

        Product::where('id',101637)->orderBy('id', 'DESC')->chunk(1000, function($products) use ($replacements) {
//        Product::where('is_approved', 0)->orderBy('id', 'DESC')->chunk(1000, function($products) use ($replacements) {

            foreach ($products as $product)  {
                foreach ($replacements as $replacement) {
                    if ($replacement->field_identifier == 'name') {
                        dump('changing names...');
                        $product->name = str_replace([$replacement->first_term, title_case($replacement->first_term), strtolower($replacement->first_term), strtoupper($replacement->first_term)], $replacement->replacement_term ?? '', $product->name);
                    }
//
//                    if ($replacement->field_identifier == 'composition') {
//                        dump('changing composition...');
//                        $product->composition = str_replace([$replacement->first_term, title_case($replacement->first_term), strtolower($replacement->first_term), strtoupper($replacement->first_term)], $replacement->replacement_term ?? '', $product->composition);
//                    }

                    if ($replacement->field_identifier == 'short_description') {
                        dump('changing_short_description..');
                        $product->short_description = str_replace([$replacement->first_term, title_case($replacement->first_term), strtolower($replacement->first_term), strtoupper($replacement->first_term)], $replacement->replacement_term ?? '', $product->short_description);
                    }
                }

                $product->save();
            }
        });
    }
}
