<?php

namespace App\Listeners;


use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\EmailAddress;

class AddSignaturreToMail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $message = $event->message;
        $fromemail->getHeaders()->get('From') ;  
        $body=$message->getBody(); 
        $signature='';
        $email= EmailAddress::where('email',$fromemail)->first(); 
        if ($email) 
        {
            if($email->signature_name!='')
                 $signature.="<br>".$email->signature_name;
         
            if($email->signature_title!='')
                 $signature.="<br>".$email->signature_title ;
         
         if($email->signature_email!='')
                 $signature.="<br>".$email->signature_email ;
          
         if($email->signature_phone!='')
                 $signature.="<br>".$email->signature_phone ;
          
         if($email->signature_website!='')
                $signature.="<br>".$email->signature_website ;
          
         if($email->signature_address!='')
                 $signature.="<br>".$email->signature_address ;
          
         if($email->signature_logo!='')
                 $signature.="<br><img src='".url('/')."/uploads/".$email->signature_logo."'>" ;
          
         if($email->signature_image!='')
                 $signature.="<br><img src='".url('/')."/uploads/".$email->signature_image."'>" ;
          
         if($email->social!='')
                    $signature.="<br>".$email->social ;
          
        }

        $event->message->setBody($body.$signature);

    }
}
