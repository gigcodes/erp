<?php

namespace App\Console\Commands;

use App\Email;

use Carbon\Carbon;
use App\Models\EmailBox;
use Illuminate\Console\Command;

class CreateMailBoxes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:create-mail-boxes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is using for create mail boxes from emails table.';

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

        $emails = Email::where('created_at', '<=', Carbon::now()->subHours(72)->format('Y-m-d H:i:s'))
            // ->where('type', 'incoming')
            ->orderBy('created_at', 'asc')
            ->whereNull('email_box_id')
            ->get();

        foreach ($emails as $email) {
            try {
                if (! empty($email->from)) {
                    $emailArr = str_split($email->from, 1);
                    $fromEmails = [];

                    if ($emailArr[0] == '[') {
                        $fromEmails = json_decode($email->from, true);
                    } else {
                        $fromEmails[] = $email->from;
                    }

                    foreach ($fromEmails as $fromEmail) {
                        $emailArr = explode('@', $fromEmail);

                        if (isset($emailArr[1])) {
                            $emailBox = EmailBox::updateOrCreate(
                                ['box_name' => $emailArr[1]],
                            );

                            $email->email_box_id = $emailBox->id;
                            $email->save();
                        }
                    }
                }
            } catch(\Exception $e) {
                //
            }
        }
        
        $emails = Email::where('created_at', '<=', Carbon::now()->subHours(72)->format('Y-m-d H:i:s'))
            // ->where('type', 'incoming')
            ->orderBy('created_at', 'asc')
            ->whereNull('email_box_id')
            ->get();

        foreach ($emails as $email) {
            try {
                if (! empty($email->from)) {
                    $emailArr = str_split($email->from, 1);
                    $fromEmails = [];

                    if ($emailArr[0] == '[') {
                        $fromEmails = json_decode($email->from, true);
                    } else {
                        $fromEmails[] = $email->from;
                    }

                    foreach ($fromEmails as $fromEmail) {
                        $emailArr = explode('@', $fromEmail);

                        if (isset($emailArr[1])) {
                            $emailBox = EmailBox::updateOrCreate(
                                ['box_name' => $emailArr[1]],
                            );

                            $email->email_box_id = $emailBox->id;
                            $email->save();
                        }
                    }
                }
            } catch(\Exception $e) {
                //
            }
        }
    }
}
