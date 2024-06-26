<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateFromSizeManager implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param public $params
     *
     * @return void
     */
    public function __construct(public $params)
    {
    }

    public static function putLog($message)
    {
        \Log::channel('productUpdates')->info($message);

        return true;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            self::putLog('Job start sizes from erp start time : ' . date('Y-m-d H:i:s'));

            $sizesProduct = \App\Product::join('product_suppliers as ps', 'ps.supplier_id', 'products.supplier_id')
                ->where('ps.size_system', $this->params['size_system'])
                ->where('products.category', $this->params['category_id'])
                ->where(function ($q) {
                    $q->whereNull('products.size_eu')->orWhere('products.size_eu', '');
                })
                ->select(['products.*', 'ps.size_system'])
                ->get();

            if (! $sizesProduct->isEmpty()) {
                foreach ($sizesProduct as $sizesP) {
                    // get size system
                    $euSize          = \App\Helpers\ProductHelper::getEuSize($sizesP, explode(',', $sizesP->size), $sizesP->size_system);
                    $sizesP->size_eu = implode(',', $euSize);
                    if (empty($euSize)) {
                        $sizesP->status_id = \App\Helpers\StatusHelper::$unknownSize;
                    } else {
                        foreach ($euSize as $es) {
                            \App\ProductSizes::updateOrCreate([
                                'product_id' => $sizesP->id, 'supplier_id' => $sizesP->supplier_id, 'size' => $es,
                            ], [
                                'product_id' => $sizesP->id, 'quantity' => 1, 'supplier_id' => $sizesP->supplier_id, 'size' => $es,
                            ]);
                        }
                    }
                    $sizesP->save();
                }
            }

            self::putLog('Job start sizes from erp end time : ' . date('Y-m-d H:i:s'));

            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['mageone', $this->params['category_id']];
    }
}
