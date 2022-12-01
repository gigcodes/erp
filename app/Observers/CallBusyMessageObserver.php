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
        if ($callBusyMessage->recording_url != '') {
            $recordedText = (new CallBusyMessage)->convertSpeechToText($callBusyMessage->recording_url);
            if ($recordedText != '') {
                CallBusyMessage::where('id', $callBusyMessage->id)->update(['audio_text' => $recordedText]);
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
        if ($callBusyMessage->recording_url != '' and $callBusyMessage->isDirty('recording_url')) {
            $recordedText = (new CallBusyMessage)->convertSpeechToText($callBusyMessage->recording_url);
            if ($recordedText != '') {
                CallBusyMessage::where('id', $callBusyMessage->id)->update(['audio_text' => $recordedText]);
            }
        }
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
