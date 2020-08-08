<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\Email;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Console\Command;
use seo2websites\ErpExcelImporter\ErpExcelImporter;
use Webklex\IMAP\Client;

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
        dump('ok');
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $imap = new Client([
                'host'          => env('IMAP_HOST_PURCHASE'),
                'port'          => env('IMAP_PORT_PURCHASE'),
                'encryption'    => env('IMAP_ENCRYPTION_PURCHASE'),
                'validate_cert' => env('IMAP_VALIDATE_CERT_PURCHASE'),
                'username'      => env('IMAP_USERNAME_PURCHASE'),
                'password'      => env('IMAP_PASSWORD_PURCHASE'),
                'protocol'      => env('IMAP_PROTOCOL_PURCHASE'),
            ]);

            $imap->connect();

            // $supplier = Supplier::find($request->supplier_id);
            // $suppliers = Supplier::whereHas('Agents')->orWhereNotNull('email')->get();

            // dump(count($suppliers));

            $types = [
                'inbox' => [
                    'inbox_name' => 'INBOX',
                    'direction'  => 'from',
                    'type'       => 'incoming',
                ],
                'sent'  => [
                    'inbox_name' => 'INBOX.Sent',
                    'direction'  => 'to',
                    'type'       => 'outgoing',
                ],
            ];

            // foreach ($suppliers as $supplier) {
                foreach ($types as $type) {
                    dump($type['type']);
                    $inbox        = $imap->getFolder($type['inbox_name']);
                    $latest_email = Email::where('type', $type['type'])->latest()->first();

                    if ($latest_email) {
                        $latest_email_date = Carbon::parse($latest_email->created_at);
                    } else {
                        $latest_email_date = Carbon::parse('1990-01-01');
                    }

                    $latest_email_date = Carbon::parse('1990-01-01');

                    dump($latest_email_date);

                    $emails = $inbox->messages()->where([
                                ['SINCE', $latest_email_date->format('d M y H:i')],
                                ]);
                                    // $emails = $emails->setFetchFlags(false)
                                    //                 ->setFetchBody(false)
                                    //                 ->setFetchAttachment(false)->leaveUnread()->get();

                                    $emails = $emails->get();

                                    foreach ($emails as $email) {
                                        dump($email);
                                        $reference_id = $email->references;
                                        // dump('=========');
                                        $origin_id = $email->message_id;

                                        // check if email has already been received

                                        if ($email->hasHTMLBody()) {
                                            $content = $email->getHTMLBody();
                                        } else {
                                            $content = $email->getTextBody();
                                        }

                                        if ($email->getDate()->format('Y-m-d H:i:s') > $latest_email_date->format('Y-m-d H:i:s')) {
                                            dump('NEW EMAIL First');
                                            $attachments_array = [];
                                            $attachments       = $email->getAttachments();

                                            $attachments->each(function ($attachment) use (&$attachments_array) {
                                                $attachment->name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $attachment->name);
                                                file_put_contents(storage_path('app/files/email-attachments/' . $attachment->name), $attachment->content);
                                                $path = "email-attachments/" . $attachment->name;

                                                // if ($attachment->getExtension() == 'xlsx' || $attachment->getExtension() == 'xls') {
                                                //     if (class_exists('\\seo2websites\\ErpExcelImporter\\ErpExcelImporter')) {
                                                //         $excel = $supplier->getSupplierExcelFromSupplierEmail();
                                                //         ErpExcelImporter::excelFileProcess($attachment->name, $excel, $supplier->email);
                                                //     }
                                                // } elseif ($attachment->getExtension() == 'zip') {
                                                //     if (class_exists('\\seo2websites\\ErpExcelImporter\\ErpExcelImporter')) {
                                                //         $excel             = $supplier->getSupplierExcelFromSupplierEmail();
                                                //         $attachments       = ErpExcelImporter::excelZipProcess($attachment, $attachment->name, $excel, $supplier->email, $attachments_array);
                                                //         $attachments_array = $attachments;
                                                //     }
                                                // }

                                                $attachments_array[] = $path;
                                            });

                                            $params = [
                                                'model_id'        => null,
                                                'model_type'      => null,
                                                'origin_id'       => $origin_id,
                                                'reference_id'    => $reference_id,
                                                'type'            => $type['type'],
                                                'seen'            => $email->getFlags()['seen'],
                                                'from'            => $email->getFrom()[0]->mail,
                                                'to'              => array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                                'subject'         => $email->getSubject(),
                                                'message'         => $content,
                                                'template'        => 'customer-simple',
                                                'additional_data' => json_encode(['attachment' => $attachments_array]),
                                                'created_at'      => $email->getDate(),
                                            ];

                                            Email::create($params);
                                        };
                                    }
                    }

                dump('__________');
            // }

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            dump($e);
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
