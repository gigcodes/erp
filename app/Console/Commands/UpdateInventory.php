<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\CronJobReport;
use App\Helpers\LogHelper;
use Illuminate\Console\Command;
use App\Jobs\CallHelperForZeroStockQtyUpdate;

class UpdateInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the inventory in the ERP';

    /**
     * Create a new command instance.
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
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

        \Log::info('Update Inventory');
        try {
            \Log::info('Update Inventory TRY');
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            \Log::info('Products Begin: ');

            // find all product first
            \Log::info('Products Query: ');
            $products = \App\Supplier::join('scrapers as sc', 'sc.supplier_id', 'suppliers.id')
                ->join('scraped_products as sp', 'sp.website', 'sc.scraper_name')
                ->where(function ($q) {
                    $q->whereDate('last_cron_check', '!=', date('Y-m-d'))->orWhereNull('last_cron_check');
                })
                ->where('suppliers.supplier_status_id', 1)
                ->select('sp.last_inventory_at', 'sp.sku', 'sc.inventory_lifetime', 'suppliers.id as supplier_id', 'sp.id as sproduct_id', 'last_cron_check')
                ->groupBy('sku')
                ->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Supplier model query finished.']);

            $skuProductArr = [];
            $skusArr = $products->pluck('sku')->toArray();
            $selectedProducts = \App\Product::select('id', 'isUploaded', 'color', 'sku')
                ->whereIn('sku', $skusArr)
                ->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product model query finished.']);

            foreach ($selectedProducts as $selected_prod_key => $selected_prod_value) {
                $skuProductArr[$selected_prod_value->sku] = [
                    'product_id' => $selected_prod_value->id,
                    'isUploaded' => $selected_prod_value->isUploaded,
                    'color' => $selected_prod_value->color,
                ];
            }
            if (! empty($products)) {
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Products records found']);

                \Log::info('Update Inventory Products Found');
                $sproductIdArr = [];
                $statusHistory = [];
                $needToCheck = [];
                $productIdsArr = [];
                $hasInventory = false;
                $productId = null;
                $today = date('Y-m-d');
                foreach ($products as $sku => $records) {
                    \Log::info('Checking :' . json_encode($records));
                    $sku = $records['sku'];
                    \Log::info('skuRecords :' . json_encode($records));
                    if (isset($skuProductArr[$sku]) && $skuProductArr[$sku]['isUploaded'] == 1) {
                        $records['product_id'] = $skuProductArr[$sku]['product_id'];
                        $records['isUploaded'] = $skuProductArr[$sku]['isUploaded'];
                        $records['color'] = $skuProductArr[$sku]['color'];
                        \Log::info('**Product Found**');
                        array_push($sproductIdArr, $records['sproduct_id']);
                        $inventoryLifeTime = isset($records['inventory_lifetime']) && is_numeric($records['inventory_lifetime'])
                            ? $records['inventory_lifetime']
                            : 0;
                        if (isset($records['product_id']) && isset($records['supplier_id'])) {
                            $history = \App\InventoryStatusHistory::where('date', $today)->where('product_id', $records['product_id'])->where('supplier_id', $records['supplier_id'])->first();
                            $lasthistory = \App\InventoryStatusHistory::where('date', '<', $today)->where('product_id', $records['product_id'])->where('supplier_id', $records['supplier_id'])->orderBy('created_at', 'desc')->first();
                            $prev_in_stock = 0;
                            $new_in_stock = 1;
                            if ($lasthistory) {
                                $prev_in_stock = $lasthistory->in_stock;
                                $new_in_stock = $lasthistory->in_stock + 1;
                            }
                            if ($history) {
                                $history->update(['in_stock' => $new_in_stock, 'prev_in_stock' => $prev_in_stock]);

                                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Inventory status history updated']);
                                \Log::info('StatusHistory updated for product: ' . $records['product_id']);
                            } else {
                                $statusHistory[] = [
                                    'product_id' => $records['product_id'],
                                    'supplier_id' => $records['supplier_id'],
                                    'date' => $today,
                                    'in_stock' => $new_in_stock,
                                    'prev_in_stock' => $prev_in_stock,
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                                \Log::info('StatusHistory push data');
                            }
                            $productId = $records['product_id'];
                        } else {
                            \Log::info('product_id or supplier_id is not found');

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'product_id or supplier_id is not found']);
                        }
                        if (is_null($records['last_inventory_at']) || strtotime($records['last_inventory_at']) < strtotime('-' . $inventoryLifeTime . ' days')) {
                            $needToCheck[] = ['id' => $records['product_id'], 'sku' => $records['sku'] . '-' . $records['color']];
                            \Log::info('Last inventory condition is success');

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Last inventory condition is success']);

                            continue;
                        } else {
                            \Log::info('Last inventory condition is failed');
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Last inventory condition is failed']);
                        }
                        $hasInventory = true;
                        dump('Scraped Product updated : ' . $records['sproduct_id']);
                    } else {
                        \Log::info('Product not found or isUploaded value is 0');

                        continue;
                    }
                }
                if (! $hasInventory && ! empty($productId)) {
                    $productIdsArr[] = $productId;
                }
                if (! empty($sproductIdArr)) {
                    \DB::table('scraped_products')->whereIn('id', $sproductIdArr)->update(['last_cron_check' => date('Y-m-d H:i:s')]);
                    \Log::info('********scraped_products updated last_cron_check field********:' . json_encode($sproductIdArr));
                }
                if (! empty($productIdsArr)) {
                    \DB::table('products')->whereIn('id', $productIdsArr)->update(['stock' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
                    \Log::info('********products updated stock to zero and updated_at field********:' . json_encode($productIdsArr));
                }
                if (! empty($statusHistory)) {
                    \App\InventoryStatusHistory::insert($statusHistory);
                    \Log::info('********InventoryStatusHistory Bulk Insert********:' . json_encode($statusHistory));

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Saved inventory status history']);
                }
                if (! empty($needToCheck)) {
                    \Log::info('********needToCheck********:' . json_encode($needToCheck));
                    try {
                        $time_start = microtime(true);
                        CallHelperForZeroStockQtyUpdate::dispatch($needToCheck)->onQueue('MagentoHelperForZeroStockQtyUpdate');
                        $time_end = microtime(true);
                    } catch (\Exception $e) {
                        \Log::error('inventory:update :: CallHelperForZeroStockQtyUpdate :: ' . $e->getMessage());
                    }
                }
            } else {
                \Log::info('Update Inventory Products Not Found');

                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Update Inventory Products Not Found']);
            }
            \Log::info('TRY END**************');
            // TODO: Update stock in Magento
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \Log::info('Update Inventory CATCH');
            \Log::error($e->getMessage());
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
