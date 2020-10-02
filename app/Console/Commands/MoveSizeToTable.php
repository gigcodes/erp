<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MoveSizeToTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'size:move-to-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move size to table';

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
        //
        $allsizes = \DB::table("products")->where("stock", ">", 0)->where("size", "!=", "")->groupBy("size")->select("size")->get();
        $sizes    = [];

        if (!empty($allsizes)) {
            foreach ($allsizes as $s) {
                $isJson = self::isJson($s->size);
                $ex     = null;
                if ($isJson) {
                    $ex = json_decode($s->size, true);
                }

                if (empty($ex) && !is_array($ex)) {
                    $ex = explode(",", $s->size);
                }

                $ex = !is_array($ex) ? [$ex] : $ex;

                $ex = array_filter($ex);
                if (!empty($ex)) {
                    foreach ($ex as $e) {
                        $e       = preg_replace("/\s+/", " ", $e);
                        $sizes[] = trim(str_replace(["// Out of stock", "bold'>", "</span>"], "", $e));
                    }
                }

            }
        }

        $sizes = array_unique($sizes);

        if (!empty($sizes)) {
            foreach ($sizes as $sz) {
                $size = \App\Size::updateOrCreate([
                    "name" => $sz,
                ], [
                    "name" => $sz,
                ]);
            }
        }

    }

    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
