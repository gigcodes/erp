<?php

namespace App\Console\Commands;

use App\ColorNamesReference;
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

        Product::where('is_approved', 1)->chunk(1000, function($products) use ($ourColors) {
            foreach ($products as $product) {
                $imageUrl = $product->getMedia('gallery')->first() ? $product->getMedia('gallery')->first()->getAbsolutePath() : '';
                if (!$imageUrl) {
                    continue;
                }



                $palette = Palette::fromFilename($imageUrl);
                $extractor = new ColorExtractor($palette);
                $colors = $extractor->extract(1);
                $color = $colors[0];

                $hex =  Color::fromIntToHex($color);
                $nameThatColor = new NameThatColor();
                $color = $nameThatColor->name($hex)['name'];

                $erpColor = $ourColors[$color];

                $pictureColor = new PictureColors();
                $pictureColor->image_url = $product->getMedia('gallery')->first()->getUrl();
                $pictureColor->color = $erpColor;
                $pictureColor->picked_color = $hex;
                $pictureColor->save();
                dump($erpColor);

            }
        });
    }
}
