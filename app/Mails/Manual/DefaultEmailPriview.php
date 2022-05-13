<?php

namespace App\Mails\Manual;

use App\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class DefaultEmailPriview extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $attchments;
    public $template;
    public $returnExchangeProducts;
    public $fromMailer;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $attchments = [], $template = "", $dataArr = '', $rxProducts = '', $fromMailer)
    {
        $this->email      = $email;
        $this->attchments = $attchments;
        $this->template   = $template;
        $this->dataArr = $dataArr;
        $this->returnExchangeProducts = $rxProducts;
        $this->fromMailer = $fromMailer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    // public function build()
    // {
    //     $email   = $this->email;
    //     $content = $email->message;

    //     return $this->to($email->to)
    //     ->from($email->from)
    //     ->subject($email->subject)
    //     ->view('emails.blank_content', compact('content'));
    // }

    public function getDataFromHTML($order,$htmlData){
        preg_match_all('/{{(.*?)}}/i', $htmlData, $matches);
        if (count($matches) != 0) {
            $matches = $matches[0];
            foreach ($matches as $match) {
                $matchString  = str_replace(["{{", "}}"], '', $match);
                $value = Arr::get($order, trim($matchString));
                $htmlData  = str_replace($match, $value, $htmlData);
            }
        }
        return $htmlData;
    }

    public function build()
    {
        try {
            $email   = $this->email;
            $content = $this->template;//$email->message;
            
            if(!empty($this->template)){
                $htmlData = $this->template;
                $re = '/<loop-(.*?)>((.|\n)*?)<\/loop-(.*?)>/m';
                preg_match_all($re, $htmlData, $matches, PREG_SET_ORDER, 0);
                if (count($matches) != 0) {
                    foreach ($matches as $index => $match) {
                        $data = null;
                        foreach($this->dataArr as $orderProduct){
                            $data .= $this->getDataFromHTML($orderProduct,$match[1]);
                        }
                        if($data){
                            $htmlData = str_replace($match[1], $data, $htmlData);
                        }
                    }
                }
                $content =  $this->getDataFromHTML($this->dataArr,$htmlData);
                return $this->from($this->fromMailer)
                    ->subject($this->subject)
                    ->view('email-templates.content', compact(
                        'content'
                    ));
            
            }
        } catch (\Throwable $th) {
            return $th;
        }
        
		

    }
	private function asJSON($data)
    {
        $json = json_encode($data);
        $json = preg_replace('/(["\]}])([,:])(["\[{])/', '$1$2 $3', $json);

        return $json;
    }


    private function asString($data)
    {
        $json = $this->asJSON($data);

        return wordwrap($json, 76, "\n   ");
    }
}
