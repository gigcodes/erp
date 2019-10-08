<?php

namespace App\Console\Commands;

use App\Document;
use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;

class DocumentReciever extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document:email';

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
        $oClient = Client::account('default');

        //Connect to the IMAP Server
        $oClient->connect();

        $folder = $oClient->getFolder('INBOX');

        $message = $folder->query()->unseen()->setFetchBody(true)->get()->all();
        if(count($message) == 0){
            echo 'No New Mail Found';
            echo '<br>';
            die();
        }

        foreach($message as $messages){
            $subject = $messages->getSubject();
            $subject = strtolower($subject);
            if (session()->has('email.subject')) {
                session()->forget('email.subject');
                session()->push('email.subject', $subject);
            } else {
                session()->push('email.subject', $subject);
            }
            if (strpos($subject, 'legal') !== false) {
                if ($messages->hasAttachments()) {
                    $aAttachment = $messages->getAttachments();
                    $aAttachment->each(function ($oAttachment) {
                        $name = $oAttachment->getName();
                        $oAttachment->save(storage_path('app/files/documents/'), $name);
                        $document = new Document();
                        $subject = session()->get('email.subject');
                        $document->name = $subject[0];
                        $document->filename = $name;
                        $document->version = 1;
                        $document->from_email = 1;
                        $document->save();

                    });

                }
            }
        }
    }
}
