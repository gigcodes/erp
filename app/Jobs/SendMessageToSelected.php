<?php

namespace App\Jobs;

use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMessageToSelected implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $number;
    protected $content;

    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $number, array $content)
    {
      $this->number = $number;
      $this->content = $content;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      if ($this->content['message']) {
        $message = $this->content['message'];

        app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($this->number, NULL, $message, false);
      }

      if (isset($this->content['image'])) {
        foreach ($this->content['image'] as $image) {
          app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($this->number, NULL, str_replace(' ', '%20', $image['url']), false);
        }
      }
    }
}
