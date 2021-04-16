<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Brand;
use App\Category;
use App\ChatbotQuestion;
use App\ChatbotQuestionExample;


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
            
            $chatQuestion =  ChatbotQuestion::where(['keyword_or_question' => 'intent','value' => 'Product_Availability'])->first();

            $category = Category::where('categories.title', '!=', null)->leftjoin("categories as sub_cat", "sub_cat.id", "categories.parent_id")
                        ->leftjoin("categories as main_cat", "main_cat.id", "sub_cat.parent_id")
                        ->select("categories.title", "sub_cat.title as sub_category", "main_cat.title as main_category")
                        ->get()->toArray();
            
            \DB::table('brands')->whereNotNull('name')->select('name')->orderBy('created_at','desc')->chunk(1, function( $brandQuery) use ( $category, $chatQuestion){
                $chatQueArr = [];

                foreach ($brandQuery as $bvalue) {
                    foreach ($category as $key => $value) {
                        $chatQueArr[] = array( 
                            'question' => $bvalue->name.' '.$value['main_category'].' '.$value['sub_category'].' '.$value['title'],
                            'chatbot_question_id' => $chatQuestion->id,
                        );
                    }
                }
                ChatbotQuestionExample::insert( $chatQueArr );
                $chatQueArr = [];
            });

            \Log::info( $this->signature .'Run success' );
        } catch (Exception $e) {
            \Log::error( $this->signature .':: '.$e->getMessage() );
        }
                
    }
}
