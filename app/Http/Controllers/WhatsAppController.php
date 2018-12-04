<?php

namespace App\Http\Controllers;

use Twilio\Jwt\ClientToken;
use Twilio\Twiml;
use Twilio\Rest\Client;
use App\Category;
use App\Notification;
use App\Leads;
use App\Order;
use App\Status;
use App\Setting;
use App\User;
use App\Brand;
use App\Product;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers;
use App\ChatMessage;


class WhatsAppController extends FindByNumberController
{
    /**
     * Incoming message URL for whatsApp
     *
     * @return \Illuminate\Http\Response
     */
    public function incomingMessage(Request $request)
    {
		//$data = json_decode($request->get("data"), TRUE);
		$data = $request->json()->all();
		$from = $data['from'];
		$text = $data['text'];
		$lead = $this->findLeadByNumber( $from );

        //save to leads
        $params = [
            'number' => $from
        ];

        if ( $lead ) {
            $params['lead_id'] = $lead->id;
            $params = $this->modifyParamsWithMessage($params, $data);
            ChatMessage::create($params);
        }

        //save to orders
        $params = [
            'number' => $from
        ];

		$order= $this->findOrderByNumber( $from );
        if ( $order ) {
            $params['order_id'] = $order->id;
            $params = $this->modifyParamsWithMessage($params, $data);
        }

        return response("");
    }
    /**
     * Send message
     *
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request, $context)
    {
	   $data = $request->all();
       try {
            $params = [];
           if ($context == "leads") {
             $lead = Leads::findOrFail( $data['lead_id'] );
             $params = [
                'lead_id' => $lead->id,
                'number' => NULL
               ];
            } elseif ($context == "orders") {
             $order = Order::findOrFail( $data['order_id'] );
             $params = [
                'order_id' => $order->id,
                'number' => NULL
              ];
            }
            if (isset($data['message'])) {
                $params['message']  = $data['message'];
            } else { // media message
                $files = \Input::file("media");
                if ($files) {
                  foreach ($files as $media) {
                    if (!$media->isValid()) {
                      \Log::error(sprintf("sendMessage media invalid"));
                      continue;
                    }
                    $extension = $media->guessExtension();
                    $fileName = uniqid(TRUE).".".$extension;
                    $mms->move(\Config::get("apiwha.media_path"), $fileName);

                    $url = implode("/", array( \Config::get("app.url"), "apiwha", "media", $fileName ));
                    $params['message'] =$url;
                  }
                }
            }

            $message = ChatMessage::create($params);
        } catch (\Exception $ex) {
            return response($ex->getMessage(), 500);
        }
       return response($message);
    }
	/**
     * poll messages
     *
     * @return \Illuminate\Http\Response
     */
    public function pollMessages(Request $request, $context)
    {
        /*
	   $sample = [
			[
				'id' => 1,
				'message' => 'Hello 123',
				'received' => TRUE,
				'lead_id' => $leadId
			],
			[
				'id' => 2,
				'message' => 'Response 123',
				'received' => FALSE,
				'lead_id' => $leadId
			]
		];
		return response()->json($sample);
        */

       $params = [];
       if ($context == "leads") {
            $id = $request->get("leadId");
            $params['lead_id'] = $id;
	        $messages = ChatMessage::where('lead_id', '=', $id);
       } elseif ($context == "orders") {
            $id = $request->get("orderId");
            $params['order_id'] = $id;
	        $messages = ChatMessage::where('order_id', '=', $id);
        }
        if ($request->get("elapse")) {
            $elapse = (int) $request->get("elapse");
           $date = new \DateTime;
           $date->modify(sprintf("-%s seconds", $elapse));
           $messages = $messages->where('created_at', '>=', $date->format('Y-m-d H:i:s'));
        }
	   $result = [];
	   foreach ($messages->get() as $message) {
         $received = false;
         if (!is_null($message['number'])) {
            $received = true;
         }
         $messageParams = [
                'id' => $message['id'],
                'received' =>$received,
                'number' => $message['number'],
                'date' => $this->formatChatDate( $message['created_at'] ),
                'approved' => $message['approved']
         ];
         if ($message['media_url']) {
            $messageParams['media_url'] = $message['media_url'];
            $headers = get_headers($message['media_url'], 1);
            $messageParams['content_type'] = $headers["Content-Type"];
         }
         if ($message['message']) {
            $messageParams['message'] = $message['message'];
         }

	     $result[] = array_merge($params, $messageParams);
	   }
       return response()->json( $result );
    }

    public function approveMessage($context, Request $request)
	{
        $user = \Auth::user();

        $message = ChatMessage::findOrFail($request->messageId);
        $message->update([
            'approved' => 1
        ]);
        if ($context == "leads") {
            $lead = Leads::find($message->lead_id);
            $this->sendWithWhatsApp( $lead->contactno, $message->message );
        } elseif ( $context == "orders") {
            $order = Order::find($message->order_id);
            $this->sendWithWhatsApp( $order->contact_detail, $message->message );
        }

        return response("");
    }

	private function sendWithWhatsApp($number, $text)
	{
        $curl = curl_init();
        $keys = \Config::get("apiwha.api_keys");
        $key = $keys[0]['key'];
        $encodedNumber = urlencode($number);
        $encodedText = urlencode($text);
        //$number = "";
        $url = "https://panel.apiwha.com/send_message.php?apikey=".$key."&number=".$encodedNumber."&text=" . $encodedText;
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          throw new \Exception("cURL Error #:" . $err);
        } else {
          $result = json_decode( $response );
          if (!$result->success) {
            throw new \Exception("whatsapp request error: " . $result->description);
           }
        }
	}
    private function formatChatDate($date)
    {
        return $date->format("Y-m-d h:iA");
    }
    private function modifyParamsWithMessage($params, $data)
    {
        if (filter_var($data['text'], FILTER_VALIDATE_URL)) {
  // you're good
            $paths = explode("/", $data['message']);
            $file = $paths[ count( $paths ) - 1];
            $extension = explode(".", $file)[1];
            $fileName = uniqid(TRUE).".".$extension;
            if ( file_put_contents(implode(DIRECTORY_SEPARATOR, array(\Config::get("apiwha.media_path"), $fileName)) ) ==  FALSE) {
                return FALSE;
            }
            $url = implode("/", array( \Config::get("app.url"), "apiwha", "media", $fileName ));
            $params['media_url'] =$url;
            return $params;
        }
        $params['message']=$data['text'];
        return $params;
    }
}
