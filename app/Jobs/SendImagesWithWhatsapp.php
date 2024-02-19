<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendImagesWithWhatsapp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 5;

    public $backoff = 5;

    public function __construct(protected $phone, protected $whatsapp_number, protected $image_url, protected $message_id)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            app(\App\Http\Controllers\WhatsAppController::class)->sendWithNewApi($this->phone, $this->whatsapp_number, null, $this->image_url, $this->message_id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['SendImagesWithWhatsapp', $this->whatsapp_number];
    }
}
