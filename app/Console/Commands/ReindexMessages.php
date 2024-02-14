<?php

namespace App\Console\Commands;

use Log;
use Illuminate\Console\Command;
use App\Elasticsearch\Reindex\Reindex;

class ReindexMessages extends Command
{
    const LIMIT = 50000;

    const MESSAGES_INDEX = 'messages';

    const REINDEX_IS_RUNNING = 'reindex-messages-is-running';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reindex:messages {param?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        set_time_limit(0);

        try {
            $reindex = new Reindex();
            $reindex->execute();
        } catch (\Exception $e) {
            Log::error('Reindex error: ' . $e->getMessage() . ' trace: ' . json_encode($e->getTrace()));
        }

        return 0;
    }
}
