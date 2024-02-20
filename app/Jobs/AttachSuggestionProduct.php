<?php

namespace App\Jobs;

use App\SuggestedProduct;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AttachSuggestionProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public SuggestedProduct $suggestion)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Request $request)
    {
        try {
            $suggestion = $this->suggestion;

            if (! empty($suggestion)) {
                // check with customer
                SuggestedProduct::attachMoreProducts($suggestion);
            }
        } catch (\Exception $e) {
            \Log::info('Issue fom customer_message ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['customer_message', $this->suggestion->chat_message_id];
    }
}
