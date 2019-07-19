<?php

namespace App\Console\Commands;

use App\ColorNamesReference;
use App\Colors;
use App\PictureColors;
use App\Product;
use Illuminate\Console\Command;
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;
use ourcodeworld\NameThatColor\ColorInterpreter as NameThatColor;


class ExtractImageColors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extract:image-colors';

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

        $ourColors = ColorNamesReference::pluck('erp_name', 'color_name')->toArray();
        $availableColors = (new Colors())->all();

        Product::where('is_approved', 1)->chunk(1000, function($products) use ($ourColors, $availableColors) {
            foreach ($products as $product) {

                if (isset($availableColors[$product->color])) {
                    dump('skipped');
                    continue;
                }

                $imageUrl = $product->getMedia('gallery')->first() ? $product->getMedia('gallery')->first()->getAbsolutePath() : '';
                if (!$imageUrl) {
                    continue;
                }

                $palette = Palette::fromFilename($imageUrl);
                $extractor = new ColorExtractor($palette);
                $colors = $extractor->extract();
                $color = $colors[0];

                $hex =  Color::fromIntToHex($color);
                $nameThatColor = new NameThatColor();
                $color = $nameThatColor->name($hex)['name'];

                $erpColor = $ourColors[$color];

                $product->color = $erpColor;
                $product->save();

                dump('Saved');

            }
        });
    }
}
