<?php

namespace App\Jobs;

use App\Product;
use App\Suggestion;
use App\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AttachSuggestionProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $suggestion;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Suggestion $suggestion)
    {

        $this->suggestion = $suggestion;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Request $request)
    {

        if (!$this->suggestion->isEmpty()) {
            foreach ($this->suggestion as $suggestion) {

                // check with customer
                $customer = $suggestion->customer;
                if ($customer) {

                    $brands     = json_decode($suggestion->brand);
                    $categories = json_decode($suggestion->category);
                    $sizes      = json_decode($suggestion->size);
                    $suppliers  = json_decode($suggestion->supplier);

                    $needToBeRun = false;
                    $products    = new Product;

                    // check with brands
                    if (!empty($brands) && is_array($brands)) {
                        $needToBeRun = true;
                        $products    = $products->whereIn('brand', $brands);
                    }

                    // check with categories
                    if (!empty($categories) && is_array($categories)) {
                        $needToBeRun = true;
                        $products    = $products->whereIn('category', $categories);
                    }

                    // check with sizes
                    if (!empty($sizes) && is_array($sizes)) {
                        $needToBeRun = true;
                        $products    = $products->where(function ($query) use ($sizes) {
                            foreach ($sizes as $size) {
                                $query->orWhere('size', 'LIKE', "%$size%");
                            }
                            return $query;
                        });
                    }

                    // check with suppliers
                    if (!empty($suppliers) && is_array($suppliers)) {
                        $needToBeRun = true;
                        $products    = $products->whereHas('suppliers', function ($query) use ($suppliers) {
                            return $query->where(function ($q) use ($suppliers) {
                                foreach ($suppliers as $supplier) {
                                    $q->orWhere('suppliers.id', $supplier);
                                }
                            });
                        });
                    }

                    // now check the params and start getting result
                    if ($needToBeRun) {
                        $products = $products->where('is_scraped', 1)->where('category', '!=', 1)->latest()->take($suggestion->number)->get();
                        if (!$products->isEmpty()) {
                            $params = [
                                'number'      => null,
                                'user_id'     => 6,
                                'approved'    => 0,
                                'status'      => ChatMessage::CHAT_SUGGESTED_IMAGES,
                                'message'     => 'Suggested images',
                                'customer_id' => $customer->id,
                            ];

                            $count = 0;

                            foreach ($products as $product) {
                                if (!$product->suggestions->contains($suggestion->id)) {
                                    if ($image = $product->getMedia(config('constants.media_tags'))->first()) {
                                        if ($count == 0) {
                                            $chat_message = ChatMessage::create($params);
                                        }

                                        $chat_message->attachMedia($image->getKey(), config('constants.media_tags'));
                                        $count++;
                                    }

                                    $product->suggestions()->attach($suggestion->id);
                                }
                            }
                        }
                    } else {
                        $suggestion->products()->detach();
                        $suggestion->delete();
                    }

                } else {
                    $suggestion->products()->detach();
                    $suggestion->delete();
                    continue;
                }
            }
        }
    }

}
