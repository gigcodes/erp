<?php

namespace App\Observers;

use App\CallBusyMessage;


class CallBusyMessageObserver
{
    /**
     * Handle the call busy message "created" event.
     *
     * @param  \App\CallBusyMessage  $callBusyMessage
     * @return void
     */
    public function created(CallBusyMessage $callBusyMessage)
    {   
		if($callBusyMessage->recording_url != '') {
			$ch1 = curl_init();
			curl_setopt ( $ch1, CURLOPT_URL, $callBusyMessage->recording_url );
			curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
			$http_respond = curl_exec($ch1);
			$http_respond = trim( strip_tags( $http_respond ) );
			$http_code = curl_getinfo( $ch1, CURLINFO_HTTP_CODE );
			curl_close( $ch1 );
			if ( ( $http_code == "200" ) || ( $http_code == "302" ) ) {			
				$apiKey = "LhLBdFbWlwujC38ym7PcILaalqTBQN7Jb-50H0ij4nkG";
				$url = "https://api.eu-gb.speech-to-text.watson.cloud.ibm.com/instances/9e2e85a8-4bea-4070-b3d3-cef36b5697f0";
				$ch = curl_init();
				$file = file_get_contents($callBusyMessage->recording_url);
				curl_setopt($ch, CURLOPT_URL, $url.'/v1/recognize');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				 
				curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_USERPWD, 'apikey' . ':' . $apiKey);

				$headers = array();
				$headers[] = "Content-Type: application/octet-stream";
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$result = curl_exec($ch);
				if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
				}
				curl_close($ch); 
				$result = json_decode($result);
				$recordedText = $result->results[0]->alternatives[0]->transcript;
				CallBusyMessage::where('id', $callBusyMessage->id)->update(['audio_text'=>$recordedText]);
			} else {
				// you can return $http_code here if necessary or wanted
				
			}
		   
			
			
		}
    }

    /**
     * Handle the call busy message "updated" event.
     *
     * @param  \App\CallBusyMessage  $callBusyMessage
     * @return void
     */
    public function updated(CallBusyMessage $callBusyMessage)
    {
        //
    }

    /**
     * Handle the call busy message "deleted" event.
     *
     * @param  \App\CallBusyMessage  $callBusyMessage
     * @return void
     */
    public function deleted(CallBusyMessage $callBusyMessage)
    {
        //
    }

    /**
     * Handle the call busy message "restored" event.
     *
     * @param  \App\CallBusyMessage  $callBusyMessage
     * @return void
     */
    public function restored(CallBusyMessage $callBusyMessage)
    {
        //
    }

    /**
     * Handle the call busy message "force deleted" event.
     *
     * @param  \App\CallBusyMessage  $callBusyMessage
     * @return void
     */
    public function forceDeleted(CallBusyMessage $callBusyMessage)
    {
        //
    }
}
