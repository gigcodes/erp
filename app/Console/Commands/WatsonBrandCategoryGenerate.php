<?php

namespace App\Console\Commands;

use App\ChatbotQuestionExample;
use Illuminate\Console\Command;

class WatsonBrandCategoryGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WatsonBrandCategoryGenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the brand and category combination';

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
            \Log::info($this->signature . 'Starting..');

            \DB::table('products')->where('products.name', '!=', null)->join('brands', 'products.brand', 'brands.id')
                ->join('categories as cat', 'cat.id', 'products.category')
                ->leftjoin('categories as sub_cat', 'sub_cat.id', 'cat.parent_id')
                ->leftjoin('categories as main_cat', 'main_cat.id', 'sub_cat.parent_id')
                ->select('cat.title', 'products.id as id', 'brands.name as brand', 'sub_cat.title as sub_category', 'main_cat.title as main_category')
                ->groupBy(['brand', 'category'])->orderBy('products.id', 'asc')->chunk(100, function ($Query) {
                    $chatQueArr = [];

                    foreach ($Query as $key => $value) {
                        $chatQueArr[] = [
                            'question' => ucwords($value->brand . ' ' . $value->main_category . ' ' . $value->sub_category . ' ' . $value->title),
                            'chatbot_question_id' => 117,
                        ];
                    }

                    ChatbotQuestionExample::insert($chatQueArr);
                    $chatQueArr = [];
                });

            \Log::info($this->signature . 'Run success');
        } catch (Exception $e) {
            \Log::error($this->signature . ':: ' . $e->getMessage());
        }
    }
}
