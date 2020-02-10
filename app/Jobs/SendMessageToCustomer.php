<?php

namespace App\Jobs;

use App\ChatMessage;
use App\Customer;
use App\Helpers\ProductHelper;
use App\Product;
use Dompdf\Dompdf;
use File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class SendMessageToCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $params;

    const SENDING_MEDIA_SIZE = 10;
    const MEDIA_PDF_CHUNKS   = 50;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {

        // Set product
        $this->type   = isset($data['type']) ? $data['type'] : "simple";
        $this->params = isset($data) ? $data : [];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Request $request)
    {
        // Set time limit
        set_time_limit(0);
        $medias      = [];
        $mediaImages = [];
        $params      = $this->params;
        $this->type  = "by_product";

        // query section

        if ($this->type == "by_product") {
            $mediaImages = ProductHelper::getImagesByProduct($params);
            if (!empty($mediaImages)) {
                $medias = Media::whereIn("id", $mediaImages)->get();
            }
        }

        // if we need to send by images id  direct then use this one
        //if ($this->type == "by_images") {
        if(!empty($params["images"])) {
            $ids = is_array($params["images"]) ? $params["images"] : json_decode($params["images"]);
            $medias = Media::whereIn("id", $ids)->get();
        }
        //}

        // attach to the customer
        $customerIds = !empty($params["customer_ids"]) ? $params["customer_ids"] : explode(",", $params["customers_id"]);

        // @todo since this message all are auto so no need to update cutomer last message to read
        $customers = Customer::whereIn("id", $customerIds)->get();

        $insertParams = [
            "message"  => isset($params["message"]) ? $params["message"] : null,
            "status"   => isset($params["status"]) ? $params["status"] : \App\ChatMessage::CHAT_AUTO_BROADCAST,
            "is_queue" => isset($params["is_queue"]) ? $params["is_queue"] : 0,
            "group_id" => isset($params["group_id"]) ? $params["group_id"] : null,
            "user_id"  => isset($params["user_id"]) ? $params["user_id"] : null,
            "number"   => null,
        ];

        $allMediaIds = $medias->pluck("id")->toArray();
        $mediable    = \DB::table('mediables')->whereIn('media_id', $allMediaIds)->where('mediable_type', 'App\Product')->get();

        $availableMedia = [];
        $productIds     = [];
        if (!$mediable->isEmpty()) {
            foreach ($mediable as $media) {
                $availableMedia[$media->media_id] = $media;
                $productIds[]                     = $media->mediable_id;
            }
        }

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // check first if the media needs to be handled by pdf then first create the images of it
        $allpdf   = [];
        $allMedia = [];
        if ($medias->count() > self::SENDING_MEDIA_SIZE || (isset($params["send_pdf"]) && $params["send_pdf"] == 1)) {
            $chunkedMedia = $medias->chunk(self::MEDIA_PDF_CHUNKS);
            foreach ($chunkedMedia as $key => $medias) {

                $pdfView = (string) view('pdf_views.images_customer', compact('medias', 'availableMedia', 'products'));

                // based on view create a pdf
                $pdf = new Dompdf();
                $pdf->setPaper([0, 0, 1000, 1000], 'portrait');
                $pdf->loadHtml($pdfView);

                if (!empty($params["pdf_file_name"])) {
                    $random = str_replace(" ", "-", $params["pdf_file_name"] . "-" . ($key + 1) . "-" . date("Y-m-d-H-i-s-") . rand());
                } else {
                    $random = uniqid('sololuxury_', true);
                }

                $fileName = public_path() . '/' . $random . '.pdf';
                $pdf->render();

                File::put($fileName, $pdf->output());

                $allpdf[]            = $fileName;
                $media               = MediaUploader::fromSource($fileName)->toDirectory('chatmessage/0')->upload();
                $allMedia[$fileName] = $media;

            }
        }

        if (!$customers->isEmpty()) {
            foreach ($customers as $customer) {
                $insertParams["customer_id"] = $customer->id;
                $chatMessage                 = ChatMessage::create($insertParams);
                if (!$medias->isEmpty()) {

                    if ($medias->count() > self::SENDING_MEDIA_SIZE || (isset($params["send_pdf"]) && $params["send_pdf"] == 1)) {
                        // send pdf
                        if (!empty($allpdf)) {
                            foreach ($allpdf as $no => $file) {
                                // if first file then send direct into queue and if then send after it
                                if ($no == 0) {
                                    $chatMessage->attachMedia($allMedia[$file], config('constants.media_tags'));
                                } else {
                                    // attach to customer so we can send later after approval
                                    $extradata             = $insertParams;
                                    $extradata['is_queue'] = 0;
                                    $extraChatMessage      = ChatMessage::create($extradata);
                                    $extraChatMessage->attachMedia($allMedia[$file], config('constants.media_tags'));

                                }
                            }
                        }

                    } else {
                        foreach ($medias as $media) {
                            try {
                                $chatMessage->attachMedia($media, config('constants.media_tags'));
                            } catch (\Exception $e) {
                                \Log::error($e);
                            }
                        }
                    }

                }
            }
        }

        self::deletePdfFiles($allpdf);

    }

    /**
     * delete all pdf files after we send to the customer
     *
     */

    public static function deletePdfFiles($files = [])
    {
        if (!empty($files)) {
            foreach ($files as $key => $file) {
                File::delete($file);
            }
        }
    }

}
