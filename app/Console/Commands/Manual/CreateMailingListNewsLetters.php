<?php

namespace App\Console\Commands\Manual;

use App\Customer;
use App\Language;
use App\Mailinglist;
use App\StoreWebsite;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\LogRequest;

class CreateMailingListNewsLetters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create_mailing_list_news_letters';

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
        $storeWebsites = StoreWebsite::get();
        $languages = Language::get();

        foreach ($storeWebsites as $key => $website) {
            foreach ($languages as $index => $lang) {
                $anyFound = Mailinglist::where(['language' => $lang->id, 'website_id' => $website->id])->first();

                if (! $anyFound) {
                    $res = $this->createSendInBlueMailingList($website, $lang);
                    /*if($res['code']==200){
                        $mess = $this->subscribeToNewsLetter($website->website,$website->title,$lang->code,$res['last_record_id']);

                    }*/
                }
            }
        }
    }

    /*public function subscribeToNewsLetter($website=null,$store_name=null,$lang_code=null,$mailingListId=null){

        $mailingListDetails = Mailinglist::find($mailingListId);

        $email = $mailingListDetails->email;

        if($store_name==''){
            $store_name = null;
        }

        $store_website = StoreWebsite::Where('website'  , $website )->first();

        if (!$store_website) {
            $message = $this->generate_erp_response("newsletter.failed", 0, $default = "Store website not found", $lang_code);
           return response()->json(["code" => 200, "message" => $message]);
        }

        $customer = $this->get_customer($email , $store_website->id );

        if( $customer && $customer->newsletter == 1  && $customer->store_website_id == $store_website->id ) {
            $message = $this->generate_erp_response("newsletter.failed.already_subscribed", $store_website->id, $default = "You have already subscibed newsletter", $lang_code );
            return response()->json(["code" => 500, "message" => $message ]);
        }

        if (!$customer) {
            $customer =  $this->create_customer( $email , $store_website->id, $store_name ,$lang_code );
        }

        // Step4
        $mailinglist = Mailinglist::where('website_id', $store_website->id)->get();

        // Step5
        foreach ($mailinglist as $key => $m) {
            $this->addToList($m->remote_id, $email);
        }

        $customer->newsletter = 1;
        $customer->save();


        $message = $this->generate_erp_response("newsletter.success", $store_website->id, $default = "Successfully added", request('lang_code'));
        return response()->json(["code" => 200, "message" => $message ]);

    }

    public function get_customer($email , $store_website_id){
       $customer = Customer::where('email', $email)->where("store_website_id", $store_website_id )->first();
       return $customer;
    }

    public function create_customer($email ,  $store_website_id, $storeName = null, $language = null) {

        $customer = new Customer;

        if( !empty( $language ) ){
            $language = explode("_", $language);
            $language = end($language);
            if ( !empty( $language ) ) {
                $customer->language = $language;
            }
        }

        $customer->email            = $email;
        $customer->store_website_id = $store_website_id;
        $customer->store_name = $storeName ;
        $customer->save();
        return $customer;
    }

    public function addToList($id, $email){

        $curl = curl_init();
        $data = [
            "email" => $email,
            "listIds" => [intval($id)]
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // "api-key: ".getenv('SEND_IN_BLUE_API'),
                "api-key: ".config('env.SEND_IN_BLUE_API'),
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);

        if (isset($res->message)) {
            if($res->message == 'Contact already exist'){
                $curl3 = curl_init();
                curl_setopt_array($curl3, array(
                    CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/".$email,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "DELETE",
                    CURLOPT_HTTPHEADER => array(
                        // "api-key: ".getenv('SEND_IN_BLUE_API'),
                        "api-key: ".config('env.SEND_IN_BLUE_API'),
                        "Content-Type: application/json"
                    ),
                ));
                $respw = curl_exec($curl3);
                curl_close($curl3);
                $respw = json_decode($respw);

                $curl2 = curl_init();
                curl_setopt_array($curl2, array(
                    CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => array(
                        // "api-key: ".getenv('SEND_IN_BLUE_API'),
                        "api-key: ".config('env.SEND_IN_BLUE_API'),
                        "Content-Type: application/json"
                    ),
                ));
                $resp = curl_exec($curl2);
                curl_close($curl2);
                $ress = json_decode($resp);
                if(isset($ress->message)){
                    return response()->json(['status' => 'error']);
                }
                $customer = Customer::where('email', $email)->first();
                $mailinglist = Mailinglist::find($id);
                \DB::table('list_contacts')->where('customer_id', $customer->id)->delete();
                if(!empty($mailinglist)){
                    $mailinglist->listCustomers()->attach($customer->id);
                }

                return response()->json(['status' => 'success']);
            }
        } else {
            $customer = Customer::where('email', $email)->first();
            $mailinglist = Mailinglist::find($id);
            $mailinglist->listCustomers()->attach($customer->id);

            return response()->json(['status' => 'success']);
        }
    }
*/
    public function createSendInBlueMailingList($website = null, $lan = null)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $return_response = [];
        $curl = curl_init();
        $data = [
            'folderId' => 1,
            'name' => $website->title,
        ];
        $api_key = (isset($website->send_in_blue_api) && $website->send_in_blue_api != '') ? $website->send_in_blue_api : getenv('SEND_IN_BLUE_API');
        $url = "https://api.sendinblue.com/v3/contacts/lists";
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                // "api-key: ".getenv('SEND_IN_BLUE_API'),
                'api-key: ' . $api_key,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($data), json_decode($response), $httpcode, \App\Console\Commands\CreateMailingListNewsLetters::class, 'createSendInBlueMailingList');

        if (curl_errno($curl)) {
            $return_response['code'] = 401;
            $return_response['msg'] = curl_error($curl);

            return $return_response;
        }

        curl_close($curl);
        \Log::info($response);
        $res = json_decode($response);
        if (isset($res->id)) {
            $last_record_id = Mailinglist::create([
                'id' => $res->id,
                'name' => $website->title,
                'language' => $lan->id,
                'website_id' => $website->id,
                'service_id' => 1,
                'remote_id' => $res->id,
                'send_in_blue_api' => $website->send_in_blue_api,
                'send_in_blue_account' => $website->send_in_blue_account,
            ]);

            $return_response['code'] = 200;
            $return_response['msg'] = 'success';
            $return_response['last_record_id'] = $last_record_id->id;
        } else {
            $return_response['code'] = 401;
            $return_response['msg'] = $res->message;
        }

        return $return_response;
    }
}
