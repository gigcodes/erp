<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use seo2websites\ErpExcelImporter\ErpExcelImporter;
use Webklex\IMAP\Facades\Client;

class EmailExcelImporter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excelimporter:run';

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

        //Get all Mailboxes
        /** @var \Webklex\IMAP\Support\FolderCollection $aFolder */
        $message = $folder->query()->unseen()->setFetchBody(true)->get()->all();
        if(count($message) == 0){
            echo 'No New Mail Found';
            die();
        }
        //dd(count($message));
        foreach($message as $messages){
            if(session()->has('email.subject')){
                session()->forget('email.subject');
                session()->push('email.subject', $messages->getSubject());
            }else{
                session()->push('email.subject', $messages->getSubject());
            }

            if($messages->hasAttachments()){
                $aAttachment = $messages->getAttachments();
                $aAttachment->each(function ($oAttachment) {
                    if($oAttachment->getExtension() == 'xlsx'){
                        $name = $oAttachment->getName();
                        $oAttachment->save(storage_path('app/files/email-attachments/'), $name);
                        $info = new \SplFileInfo(storage_path('app/files/email-attachments/').$name);
                        $spreadsheet = ErpExcelImporter::readFile($info);
                        $subject = session()->get('email.subject');
                        $name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
                        $result = ErpExcelImporter::processFile($spreadsheet, $subject[0], $name);
                        if (class_exists('\\App\\Jobs\\ProductImport')) {
                            if (class_exists('\\App\\LogExcelImport')) {
                                if (count($result) != 0) {
                                    $data['number_of_products'] = count($result);
                                } else {
                                    $data['number_of_products'] = 0;
                                }
                                $data['filename'] = $name;
                                $data['supplier'] = $subject[0];
                                \App\LogExcelImport::create($data);
                            }
                            //$scrapedProduct = new \App\ScrapedProducts();
                           // $itemsAdded = $scrapedProduct->bulkScrapeImport($result, 1);
                            \App\Jobs\ProductImport::dispatch($result)->onQueue('product');
                        }
                    }elseif ($oAttachment->getExtension() == 'zip'){
//                        $name = $oAttachment->getName();
//                        if (!file_exists(storage_path('app/files/email-attachments/zip/'))) {
//                            mkdir(storage_path('app/files/email-attachments/zip/'), 0777, true);
//                        }
//                        $oAttachment->save(storage_path('app/files/email-attachments/zip/'), $name);
//                        $zip = new \ZipArchive;
//                        $res = $zip->open(storage_path('app/files/email-attachments/zip/').$name);
//                        if ($res === TRUE) {
//                            if (!file_exists(storage_path('app/files/email-attachments/extract/'))) {
//                                mkdir(storage_path('app/files/email-attachments/extract/'), 0777, true);
//                            }
//                            $zip->extractTo(storage_path('app/files/email-attachments/extract/'));
//                            $zip->close();
//                            $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
//                            $b = scandir(storage_path('app/files/email-attachments/extract/').$withoutExt,1);
//                            foreach ($b as $key => $value){
//                                if($value != '.' || $value != '..'){
//                                    $path = new \SplFileInfo(storage_path('app/files/email-attachments/extract/').$withoutExt.'/'.$value);
//                                    $spreadsheet = ErpExcelImporter::readFile($path);
//                                    //dd($spreadsheet);
//                                    $subject = session()->get('email.subject');
//                                    $namewithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value);
//                                    $result = ErpExcelImporter::processFile($spreadsheet, $subject[0], $namewithoutExt);
//                                    if (class_exists('\\App\\Jobs\\ProductImport')) {
//                                        if(class_exists('\\App\\LogExcelImport')) {
//                                            if(count($result) != 0){
//                                                $data['number_of_products'] = count($result);
//                                            }else{
//                                                $data['number_of_products'] = 0;
//                                            }
//                                            $data['filename'] = $namewithoutExt;
//                                            $data['supplier'] =  $subject[0];
//                                            \App\LogExcelImport::create($data);
//                                        }
//                                        $scrapedProduct = new \App\ScrapedProducts();
//                                        $itemsAdded = $scrapedProduct->bulkScrapeImport($result, 1);
//                                    }
//                                }
//
//                            }
                        }

                });
            }
        }

    }
}


