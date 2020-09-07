<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\Email;
use App\EmailAddress;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Console\Command;
use seo2websites\ErpExcelImporter\ErpExcelImporter;
use Webklex\IMAP\Client;

/**
 * @author Sukhwinder <sukhwinder@sifars.com>
 * This command takes care of receiving all the emails from the smtp set in the environment
 *
 * All fetched emails will go inside emails table
 */
class FetchAllEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:all_emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches all emails from the configured SMTP settings';

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
        $report = CronJobReport::create([
            'signature'  => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        $emailAddresses = EmailAddress::orderBy('id', 'asc')->get();
        foreach($emailAddresses as $emailAddress) {
            try {
                $imap = new Client([
                    'host' => $emailAddress->host,
                    'port' => $emailAddress->port,
                    'encryption' => $emailAddress->encryption,
                    'validate_cert' => true,
                    'username' => $emailAddress->username,
                    'password' => $emailAddress->password,
                    'protocol' => 'imap',
                ]);

                $imap->connect();
                $types = [
                    'inbox' => [
                        'inbox_name' => 'INBOX',
                        'direction' => 'from',
                        'type' => 'incoming',
                    ],
                    'sent' => [
                        'inbox_name' => 'INBOX.Sent',
                        'direction' => 'to',
                        'type' => 'outgoing',
                    ],
                ];

                $available_models = ["supplier" => \App\Supplier::class, "vendor" => \App\Vendor::class,
                    "customer" => \App\Customer::class, "users" => \App\User::class];
                $email_list = [];
                foreach ($available_models as $key => $value) {
                    $email_list[$value] = $value::whereNotNull('email')->pluck('id', 'email')->unique()->all();
                }

                foreach ($types as $type) {

                    dump("Getting emails for: " . $type['type']);

                    $inbox = $imap->getFolder($type['inbox_name']);
                    $latest_email = Email::where('type', $type['type'])->latest()->first();

                    $latest_email_date = $latest_email?Carbon::parse($latest_email->created_at):false;

                    dump("Last received at: " . ($latest_email_date?:'never'));
                    // Uncomment below just for testing purpose
//                    $latest_email_date = Carbon::parse('2020-01-01');

                    if ($latest_email_date) $emails = $inbox->messages()->where('SINCE', $latest_email_date->subDays(1)->format('d-M-Y'));
                    else $emails = $inbox->messages();

                    $emails = $emails->get();
//
                    // dump($inbox->messages()->where([
                    //     ['SINCE', $latest_email_date->subDays(1)->format('d-M-Y')],
                    //     ])->get());

                    foreach ($emails as $email) {

                        $reference_id = $email->references;
//                        dump($reference_id);
                        $origin_id = $email->message_id;

                        // Skip if message is already stored
                        if (Email::where('origin_id', $origin_id)->count() > 0) {
                            continue;
                        }

                        // check if email has already been received

                        if ($email->hasHTMLBody()) {
                            $content = $email->getHTMLBody();
                        } else {
                            $content = $email->getTextBody();
                        }

                        if (!$latest_email_date || $email->getDate()->timestamp > $latest_email_date->timestamp) {
                            $attachments_array = [];
                            $attachments = $email->getAttachments();

                            $attachments->each(function ($attachment) use (&$attachments_array) {
                                $attachment->name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $attachment->name);
                                file_put_contents(storage_path('app/files/email-attachments/' . $attachment->name), $attachment->content);
                                $path = "email-attachments/" . $attachment->name;

                                $attachments_array[] = $path;
                            });

                            $from = $email->getFrom()[0]->mail;
                            $to = array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail;

                            // Model is sender if its incoming else its receiver if outgoing
                            if ($type['type'] == 'incoming') {
                                $model_email = $from;
                            } else {
                                $model_email = $to;
                            }

                            // Get model id and model type

                            extract($this->getModel($model_email, $email_list));
                            /**
                             * @var $model_id
                             * @var $model_type
                             */

                            $params = [
                                'model_id' => $model_id,
                                'model_type' => $model_type,
                                'origin_id' => $origin_id,
                                'reference_id' => $reference_id,
                                'type' => $type['type'],
                                'seen' => $email->getFlags()['seen'],
                                'from' => $email->getFrom()[0]->mail,
                                'to' => array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                'subject' => $email->getSubject(),
                                'message' => $content,
                                'template' => 'customer-simple',
                                'additional_data' => json_encode(['attachment' => $attachments_array]),
                                'created_at' => $email->getDate(),
                            ];
//                            dump("Received from: ". $email->getFrom()[0]->mail);
                            Email::create($params);
                        }
                    }
                }

                dump('__________');

                $report->update(['end_time' => Carbon::now()]);
            } catch (\Exception $e) {
                \App\CronJob::insertLastError($this->signature, $e->getMessage());
            }
        }
    }

    /**
     * Check all the emails in the DB and extract the model type from there
     *
     * @param [type] $email
     * @param [type] $email_list
     * @return array(model_id,miodel_type)
     */
    private function getModel($email, $email_list){
        $model_id = null;
        $model_type = null;

        // Traverse all models
        foreach ($email_list as $key => $value) {

            // If email exists in the DB
            if( isset($value[$email])){
                $model_id = $value[$email];
                $model_type = $key;
            break;
            }
        }

        return compact('model_id','model_type');

    }
}
