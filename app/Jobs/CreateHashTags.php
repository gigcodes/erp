<?php

namespace App\Jobs;

use App\HashTag;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateHashTags implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $hashTag;


    /**
     * Create a new job instance.
     *
     * @param array $hashTag
     */
    public function __construct(array $hashTag)
    {
        //
        $this->hashTag = $hashTag;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function handle(): bool
    {
        try {
            self::putLog('Job start sizes from erp start time : '.date('Y-m-d H:i:s'));

            \DB::table('hash_tags')->insert($this->hashTag);

            self::putLog('Job start sizes from erp end time : '.date('Y-m-d H:i:s'));

            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function putLog($message)
    {
        \Log::channel('hashtagAdd')->info($message);
        return true;
    }
}
