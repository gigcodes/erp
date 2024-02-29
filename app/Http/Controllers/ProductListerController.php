<?php

namespace App\Http\Controllers;

use App\Stage;
use App\Product;
use App\Setting;
use App\Category;
use Carbon\Carbon;
use App\StoreWebsite;
use App\ListingHistory;
use App\Jobs\PushToMagento;
use App\Helpers\ProductHelper;
use App\Loggers\LogListMagento;
use Illuminate\Support\Facades\Auth;

class ProductListerController extends Controller
{
    public function index(Stage $stage)
    {
        $products = Product::latest()
            ->where('stock', '>=', 1)
            ->where('stage', '>=', $stage->get('ImageCropper'))
            ->whereNull('dnf')
            ->select(['id', 'sku', 'size', 'price_inr_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])
            ->paginate(Setting::get('pagination'));

        $roletype = 'Lister';

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple'])
            ->selected(1)
            ->renderAsDropdown();

        return view('partials.grid', compact('products', 'roletype', 'category_selection'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function edit(Product $productlister)
    {
        return redirect(route('products.show', $productlister->id));
    }

    public function isUploaded(Product $product, Stage $stage)
    {
        if ($product->isUploaded == 1) {
            return back()->with('error', 'Product already upload.');
        }

        $result = $this->magentoSoapApiUpload($product);

        if ($result) {
            $product->isUploaded       = 1;
            $product->stage            = $stage->get('Lister');
            $product->is_uploaded_date = Carbon::now();
            $product->save();

            NotificaitonContoller::store('has Uploaded', ['Approvers'], $product->id);
            ActivityConroller::create($product->id, 'lister', 'create');

            return back()->with('success', 'Product has been Uploaded');
        }

        return back()->with('error', 'Error Occured while uploading');
    }

    public function magentoSoapApiUpload($product, $status = 2)
    {
        return 'this request is tempoary disable';

        // Log activity
        ListingHistory::createNewListing(Auth::user()->id, $product->id, ['action' => 'MAGENTO_LISTED', 'page' => 'Approved Listing Page'], 'MAGENTO_LISTED');

        // Queue the task
        PushToMagento::dispatch($product);

        $queueName = [
            '1' => 'mageone',
            '2' => 'magetwo',
            '3' => 'magethree',
        ];

        $i             = 1;
        $websiteArrays = ProductHelper::getStoreWebsiteName($product->id);
        if (! empty($websiteArrays)) {
            foreach ($websiteArrays as $websiteArray) {
                $website = StoreWebsite::find($websiteArray);
                if ($website) {
                    \Log::channel('productUpdates')->info('Product started website found For website' . $website->website);
                    LogListMagento::log($product->id, 'Start push to magento for product id ' . $product->id, 'info', $website->id);
                    //currently we have 3 queues assigned for this task.
                    if ($i > 3) {
                        $i = 1;
                    }
                    PushToMagento::dispatch($product, $website)->onQueue($queueName[$i]);
                    $i++;
                }
            }
        }

        return 'ok';
    }
}
