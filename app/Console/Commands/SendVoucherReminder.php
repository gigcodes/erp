<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Voucher;
use App\Mail\VoucherReminder;
use Illuminate\Support\Facades\Mail;

class SendVoucherReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:voucher-reminder';

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
     * @return mixed
     */
    public function handle()
    {
      $before = Carbon::now()->subDays(5)->format('Y-m-d 00:00:00');
      $vouchers = Voucher::where('date', '<=', $before)->get();

      foreach ($vouchers as $voucher) {
        $credit = $voucher->amount - $voucher->paid;

        if ($credit > 0) {
          Mail::to('yogeshmordani@icloud.com')
              ->cc('hr@sololuxury.co.in')
              ->send(new VoucherReminder($voucher));
        }
      }
    }
}
