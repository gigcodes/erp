<?php

namespace App\Http\Controllers\Marketing;

use App\GmailData;
use App\Image;
use App\Mailinglist;
use App\MailinglistEmail;
use App\MailinglistTemplate;
use App\MailingTemplateFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MailinglistEmailController extends Controller
{
    public function index () {

        $audience = Mailinglist::all();
        $templates = MailinglistTemplate::all();
        $images = Image::all();
        $images_gmail = GmailData::all();
        $mailings = MailinglistEmail::with('audience','template')->orderBy('created_at','desc')->get();

        return view('marketing.mailinglist.sending-email.index',compact('audience', 'templates','images','images_gmail','mailings'));
    }

    public function ajaxIndex (Request $request) {
        $data = $request->all();
        $content = null;

        $template_html = MailingTemplateFile::where('mailing_id',$request->id)->where("path", "like", "%index.html%")->first();
        if($template_html){
            $content = file_get_contents($template_html->path);
        }

        return response()->json([ 'template_html' => $content]);
    }

    public function store (Request $request) {
        $data = $request->all();


       $validator = Validator::make($request->all(), [
            'template_id' => 'required',
            'scheduled_date' => 'required',
            'mailinglist_id' => 'required',
            'subject' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([ 'errors' => $validator->getMessageBag()->toArray()]);
        }

        $mailing_item = new MailinglistEmail();
        $mailing_item->mailinglist_id = $data['mailinglist_id'];
        $mailing_item->template_id = $data['template_id'] ;
        $mailing_item->html = $data['html'];
        $mailing_item->subject = $data['subject'];
        $mailing_item->scheduled_date =$data['scheduled_date'];
        $mailing_item->html = $data['html'];

        if(!empty($data['html'])){
            $curl = curl_init();
            $data = [
                "sender" => array(
                    'name' => 'Luxury Unlimited',
                    'id' => 1,
                ),
                "htmlContent" => $mailing_item->html,
                "templateName" =>  $mailing_item->subject,
                'subject'=> $mailing_item->subject
            ];
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.sendinblue.com/v3/smtp/templates",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    "api-key: ".getenv('SEND_IN_BLUE_API'),
                    "Content-Type: application/json"
                ),
            ));
            $response = curl_exec($curl);
            $response = json_decode($response);
            if($response->id){
                $mailing_item->api_template_id = $response->id;
            }
            curl_close($curl);
        }

        $mailing_item->save();

        return response()->json([
            'item' => view('partials.mailing-template.template',[
                'item' => $mailing_item
            ])->render(),
        ]);
    }

    public function show (Request $request) {
/*        dd($request->id);*/

        $data = MailinglistEmail::where("id", $request->id)->first();
        return response()->json([
            'html'=>$data
        ]);

    }

    public function duplicate (Request $request) {
        /*        dd($request->id);*/


        $data = MailinglistEmail::where("id", $request->id)->first();


        return response()->json([
            'html'=>$data
        ]);

    }
}
