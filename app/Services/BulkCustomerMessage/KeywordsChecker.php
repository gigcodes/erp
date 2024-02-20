<?php

namespace App\Services\BulkCustomerMessage;

use App\ChatMessage;
use Illuminate\Support\Facades\DB;
use App\BulkCustomerRepliesKeyword;

class KeywordsChecker
{
    /**
     * @purpose This method gets the messages, and then checks if keywords is in that string or not...
     */
    public function assignCustomerAndKeyword($keywords, $customers): void
    {
        foreach ($customers as $customer) {
            $message = $this->getCustomerMessages($customer);

            if (! $message) {
                continue;
            }
            $this->makeKeywordEntryForCustomer($customer, $message, $keywords);
        }
    }

    /**
     * @purpose Checks if the message is in string, and creates keywords like that...
     */
    private function makeKeywordEntryForCustomer($customer, $message, $keywords): void
    {
        $dataToInsert = [];

        foreach ($keywords as $keyword) {
            $keywordValue = strtolower($keyword->value);
            if (stripos($message, $keywordValue) !== false) {
                $dataToInsert[] = ['keyword_id' => $keyword->id, 'customer_id' => $customer->id];
            }
        }

        if ($dataToInsert === []) {
            return;
        }

        DB::table('bulk_customer_replies_keyword_customer')->insert($dataToInsert);
        $customer->is_categorized_for_bulk_messages = 1;
        $customer->save();
    }

    /**
     * @purpose create customer and keyword relationship for new incoming messages...
     */
    public function assignCustomerAndKeywordForNewMessage($message, $customer): void
    {
        $keywords = BulkCustomerRepliesKeyword::all();
        $this->makeKeywordEntryForCustomer($customer, $message, $keywords);
    }

    /**
     * @purpose To return the latest 3 non-replied messages, this will ignore the auto-generated message...
     */
    private function getCustomerMessages($customer): string
    {
        $messageText = '';
        $messages = ChatMessage::whereNotIn('status', [7, 8, 9, 10])->where('customer_id', $customer->id)->orderBy('id', 'DESC')->take(3)->get();

        foreach ($messages as $message) {
            if ($message->user_id) {
                break;
            }

            $messageText .= $message->message;
        }

        return $messageText;
    }
}
