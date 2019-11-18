<?php

namespace App\Helpers;
use App\ImQueue;
use App\ChatMessage;
use Illuminate\Http\Request;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use DB;
use Dompdf\Dompdf;



class InstantMessagingHelper {

    /**
     * Save Messages For Send Whats App
     *
     * @param $numberTo , $text , $image , $priority, $numberFrom , $client , sendAfter
     * @return void 
     * @static 
     */ 
	public static function sendInstantMessage($numberTo , $text = null , $image = null, $priority = null, $numberFrom = null, $client = null, $sendAfter = null)
	{
        //check if image and text are not null
		if($image != null || $text != null){
			if($numberTo == '' || $numberTo == null){
				return redirect()->back()->withErrors('Please Provide To Send');
			}
            //default number for send message
			if($numberFrom == null){
				$numberFrom = env('DEFAULT_SEND_NUMBER');
			}
            //setting default client name
			if($client == null){
				$client = 'whatsapp';
			}
            //saving queue
			$queue = new ImQueue();
			$queue->im_client = $client;
			$queue->number_to = $numberTo;
			$queue->number_from = $numberFrom;
            //getting image or text
			if($image != null && $text != null){
				$queue->image = self::encodeImage($text,$image);
			}elseif($image != null){
				$queue->image = self::encodeImage('',$image);
			}else{
				$queue->text = $text;
			}
            //setting priority
			if($priority == null){
				$queue->priority = 10;	
			}else{
				$queue->priority = $priority;
			}
            //setting send after
			$queue->send_after = $sendAfter;
			$queue->save();
            //returning response
			return redirect()->back()->withSuccess('Mesage Saved');
		}else{
            //returning error in response
			return redirect()->back()->withErrors('Please Provide with image link or message');
		}
		
	}


    /**
     * Return Json Encode URL
     *
     * @param $text , $image
     * @return jsonencoded image 
    */ 
	public function encodeImage($text = null , $image)
	{
        //getting file name from image url
		$filename = basename($image);

        //getting caption from text
		if($text == null){
			$image = array('body' => $image , 'filename' => $filename , 'caption' => ''); 
		}else{
			$image = array('body' => $image , 'filename' => $filename , 'caption' => $text); 	
		}
        //returning result
		return json_encode($image);
      
	}

	public function sendWhatsAppMessage($request, $customer)
	{
        
		//Save Chat Message
		$data['customer_id'] = $request->customer_id;
		$data['approved'] = 1;
		$chat_message = ChatMessage::create($data);
		

		if ($request->images) {

            $imagesDecoded = json_decode($request->images);

            if (!empty($request->send_pdf) && $request->send_pdf == 1) {

                $temp_chat_message = ChatMessage::create($data);
                foreach ($imagesDecoded as $image) {
                    $media = Media::find($image);
                    $isExists = DB::table('mediables')->where('media_id', $media->id)->where('mediable_id', $temp_chat_message->id)->where('mediable_type', 'App\ChatMessage')->count();
                    if (!$isExists) {
                        $temp_chat_message->attachMedia($media, config('constants.media_tags'));
                    }

                }


                $fn = '';
                if ($context == 'customer') {
                    $fn = '_product';
                }

                $folder = "temppdf_view_" . time();

                $medias = Media::whereIn('id', $imagesDecoded)->get();
                $pdfView = view('pdf_views.images' . $fn, compact('medias', 'folder'));
                $pdf = new Dompdf();
                $pdf->setPaper([0, 0, 1000, 1000], 'portrait');
                $pdf->loadHtml($pdfView);
                $fileName = public_path() . '/' . uniqid('sololuxury_', true) . '.pdf';
                $pdf->render();

                File::put($fileName, $pdf->output());
                $media = MediaUploader::fromSource($fileName)
                    ->toDirectory('chatmessage/' . floor($chat_message->id / config('constants.image_per_folder')))
                    ->upload();
                        $chat_message->attachMedia($media, 'gallery');
            } else {
                foreach (array_unique($imagesDecoded) as $image) {
                    $media = Media::find($image);
                    $isExists = DB::table('mediables')->where('media_id', $media->id)->where('mediable_id', $chat_message->id)->where('mediable_type', 'App\ChatMessage')->count();
                    if (!$isExists) {
                        $chat_message->attachMedia($media, 'constants.media_tags');                    
                    }
                }
            }
        }
	}

}