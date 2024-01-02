<?php

namespace App\Console\Commands;

use DB;
use App\Product;
use App\Template;
use App\ProductTemplate;
use Plank\Mediable\Media;
use App\Helpers\LogHelper;
use Illuminate\Console\Command;

class SetTemplatesForProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'template:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets Selected Product From Template and Process Them to Template Queue';

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
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $totalCount = 0;
            $templates = Template::where('auto_generate_product', 1)->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'template query was finished.']);
            foreach ($templates as $template) {
                if ($totalCount > 10000) {
                    break;
                    echo 'Completed making 10000 entries';
                }
                //chunk this in 1000

                Product::chunk(1000, function ($products) use ($totalCount, $template) {
                    foreach ($products as $product) {
                        $checkTag = 'template_' . $template->id;
                        $mediable = DB::table('mediables')->where('tag', $checkTag)->where('mediable_id', $product->id)->first();

                        if ($mediable != null) {
                            break;
                        }

                        if ($product->getMedia(config('constants.media_tags'))->count() == 0) {
                            break;
                        }

                        if ($totalCount > 10000) {
                            break;
                            echo 'Completed making 10000 entries';
                        }

                        $productTemplate = new ProductTemplate;
                        $productTemplate->template_no = $template->id;
                        $productTemplate->product_title = $product->name;
                        $productTemplate->brand_id = $product->brand;
                        $productTemplate->currency = 'eur';
                        if (empty($product->price)) {
                            $product->price = 0;
                        }
                        if (empty($product->price_eur_discounted)) {
                            $product->price_eur_discounted = 0;
                        }
                        $productTemplate->price = $product->price;
                        $productTemplate->discounted_price = $product->price_eur_discounted;
                        $productTemplate->product_id = $product->id;
                        $productTemplate->is_processed = 0;
                        $productTemplate->save();
                        $totalCount++;
                        foreach ($product->getMedia(config('constants.media_tags'))->all() as $media) {
                            $media = Media::find($media->id);
                            $tag = 'template-image';
                            $productTemplate->attachMedia($media, $tag);
                        }
                    }
                });
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
