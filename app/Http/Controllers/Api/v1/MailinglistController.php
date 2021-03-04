<?php

namespace App\Http\Controllers\Api\v1;

use App\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\StoreWebsite;
use App\Mailinglist;

class MailinglistController extends Controller
{   
    
    /**
    *@create_customer
    *@author Hitesh
    *@param $email
    *@param $store_website_id
    */ 
    public function create_customer($email ,  $store_website_id) {
        $customer = new Customer;
        $customer->email            = $email;
        $customer->store_website_id = $store_website_id;
        $customer->save();
        return $customer;
    }


    public function get_customer($email , $store_website_id){
       $customer = Customer::where('email', $email)->where("store_website_id", $store_website_id )->first();
       return $customer;
    }

   
    
   /**
   *@function add
   *Step1) get store website from request
   *Step2) if store website not present send return message
   *Step3) if it's present get cutomer using store_website_Id and email id
   *        if customer is not present create new customer 
   *Step4) Now using store_website_id get mailing list
   *Step5) loop on all mailing list and call to addToList method
   **/ 
    public function add(Request $request) {
        // Step1
        $store_website = StoreWebsite::Where('website'  , $request->website )->first();

        // Step 2
        if (!$store_website) {
            $message = $this->generate_erp_response("newsletter.success", 0, $default = "Store website not found");
           return response()->json(["code" => 200, "message" => $message]);
        }
        // Step 3
        $customer = $this->get_customer($request->get("email") , $store_website->id );

        if (!$customer) {
            $customer =  $this->create_customer( $request->get("email") , $store_website->id );
        } 

        if($customer->newsletter == 1) {
            $message = $this->generate_erp_response("newsletter.already_subscribed", $store_website->id, $default = "You have already subscibed newsletter");
            return response()->json(["code" => 500, "message" => $message ]);
        }


        // Step4
        $mailinglist = Mailinglist::where('website_id', $store_website->id)->get();

        // Step5
        foreach ($mailinglist as $key => $m) {
            $this->addToList($m->remote_id, $request->get("email"));
        }

        $customer->newsletter = true;
        $customer->save();

        // return response()->json(["code" => 200, "message" => "Done", "data" => $request->all()]);
        $message = $this->generate_erp_response("newsletter.success", $store_website->id, $default = "Successfully added");
        return response()->json(["code" => 200, "message" => $message ]);
    }


        /**
     * @param $id
     * @param $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addToList($id, $email)  {
        

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
                "api-key: ".getenv('SEND_IN_BLUE_API'),
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
                        "api-key: ".getenv('SEND_IN_BLUE_API'),
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
                        "api-key: ".getenv('SEND_IN_BLUE_API'),
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






}
