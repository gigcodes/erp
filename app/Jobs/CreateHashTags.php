<?php

namespace App\Jobs;

use App\Brand;
use App\Category;
use App\HashTag;
use App\KeywordSearchVariants;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateHashTags implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;


    /**
     * Create a new job instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        //
        $this->data = $data;
        //print_r($data); exit;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function handle(): bool
    {

        try {
            self::putLog('Job start generategooglescraperkeywordsstart from erp ABC start time : '.date('Y-m-d H:i:s'));

            switch($this->data['type']) {
                case('brand'):

                    $brand_list = $this->data['data'];
                    $user_id = $this->data['user_id'];
                    $category_postfix_string_list = $this->data['category_postfix_string_list'];
                    if (count($brand_list) > 0) {

                        ini_set('max_execution_time', '-1');
                        ini_set('max_execution_time', '0'); // for infinite time of execution
                        $processed_brand_id_array = [];

                        foreach ($brand_list as $id=>$name) {
                            foreach($category_postfix_string_list as $string) {
                                $generated_string = $name .' '. $string->combined_string;
                                $check_exist = HashTag::where('hashtag', $generated_string)->count();
                                if($check_exist > 0) {
                                    continue;
                                }
                                $hashtag = new HashTag();
                                $hashtag->hashtag = $generated_string;
                                $hashtag->platforms_id = 2;
                                $hashtag->rating = 8;
                                $hashtag->created_at = date('Y-m-d h:i:s');
                                $hashtag->updated_at = date('Y-m-d h:i:s');
                                $hashtag->created_by = $user_id;
                                $insert_data = $hashtag->toArray();
                                if(isset($insert_data['hashtag'])) {
                                    \DB::table('hash_tags')->insert($insert_data);
                                }
                            }

                            $processed_brand_id_array[] = $id;
                        }

                        Brand::updateStatusIsHashtagsGenerated($processed_brand_id_array);
                    }
                    break;

                case('category'):

                    $brandList = $this->data['brand_list'];
                    $keywordVariantsList = $this->data['keyword_variants'];
                    $categoryList = $this->data['data'];
                    $user_id = $this->data['user_id'];

                    if (!empty($brandList)) {
                        ini_set('max_execution_time', '-1');
                        ini_set('max_execution_time', '0'); // for infinite time of execution
                        $processed_category_id_array = [];
                        foreach ($categoryList as  $category) {
                            foreach ($brandList as $brand) {
                                foreach ($keywordVariantsList as $keywordVariant) {
                                    $generated_string = $brand . ' ' . $category->title . ' ' . $keywordVariant;
                                    $check_exist = HashTag::where('hashtag', $generated_string)->count();
                                    if ($check_exist > 0) {
                                        continue;
                                    }
                                    $hashtag = new HashTag();
                                    $hashtag->hashtag = $generated_string;
                                    $hashtag->platforms_id = 2;
                                    $hashtag->rating = 8;
                                    $hashtag->created_at = date('Y-m-d h:i:s');
                                    $hashtag->updated_at = date('Y-m-d h:i:s');
                                    $hashtag->created_by = $user_id;
                                    $insert_data = $hashtag->toArray();
                                    if(isset($insert_data['hashtag'])) {
                                        \DB::table('hash_tags')->insert($insert_data);
                                    }
                                }

                            }
                            $processed_category_id_array[] = $category->id;
                        }
                        Category::updateStatusIsHashtagsGeneratedCategories($processed_category_id_array);
                    }

                    break;

                case ('keyword_variant'):

                    if (!empty($brandList)) {
                        ini_set('max_execution_time', '-1');
                        ini_set('max_execution_time', '0'); // for infinite time of execution
                        $keywordVariants = $this->data['data'];
                        $brands = $this->data['brand_list'];
                        $categories = $this->data['category_list'];
                        $user_id = $this->data['user_id'];
                        $processed_variant_id_array = [];
                        foreach ($keywordVariants as $keywordVariant) {
                            foreach ($brands as $brand) {
                                foreach($categories as $category) {
                                    $generated_string = $brand . ' ' . $category->title . ' ' . $keywordVariant;
                                    $check_exist = HashTag::where('hashtag', $generated_string)->count();
                                    if ($check_exist > 0) {
                                        continue;
                                    }
                                    $hashtag = new HashTag();
                                    $hashtag->hashtag = $generated_string;
                                    $hashtag->platforms_id = 2;
                                    $hashtag->rating = 8;
                                    $hashtag->created_at = date('Y-m-d h:i:s');
                                    $hashtag->updated_at = date('Y-m-d h:i:s');
                                    $hashtag->created_by = $user_id;
                                    $insert_data = $hashtag->toArray();
                                    if(isset($insert_data['hashtag'])) {
                                        \DB::table('hash_tags')->insert($insert_data);
                                    }
                                }

                            }
                            $processed_variant_id_array[] = $keywordVariant->id;

                        }
                        KeywordSearchVariants::updateStatusIsHashtagsGeneratedKeywordVariants($processed_variant_id_array);
                    }
                    break;

                default:
                    $msg = 'Something went wrong.';
            }

            self::putLog('Job start generategooglescraperkeywordsstart from erp end time : '.date('Y-m-d H:i:s'));

            return true;
        } catch (\Exception $e) {
            self::putLog('Job start generategooglescraperkeywords Exception  from erp start time : '.date('Y-m-d H:i:s'));
            throw new \Exception($e->getMessage());
        }
    }

    public static function putLog($message)
    {
        \Log::channel('daily')->info($message);
        return true;
    }
}
