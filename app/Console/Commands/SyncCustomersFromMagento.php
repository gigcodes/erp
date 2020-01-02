<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Customer;
use App\CronJobReport;
use Carbon\Carbon;

class SyncCustomersFromMagento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:erp-magento-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync customers in Magento with ERP customers';

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

        $report = CronJobReport::create([
            'signature' => $this->signature,
            'start_time'  => Carbon::now()
        ]);

        $options   = array(
            'trace'              => true,
            'connection_timeout' => 120,
            'wsdl_cache'         => WSDL_CACHE_NONE,
        );

        $proxy = new \SoapClient( config('magentoapi.url'), $options);
        try {
            $sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));

            //Get customer list from magento
            $magento_customers = json_decode( json_encode( $proxy->customerCustomerList($sessionId) ), true );

            //Loop through customers
            if(count($magento_customers) > 0){
                foreach($magento_customers as $k=>$customer){
                    $customerId = $customer['customer_id'];
                    $customer_email = $customer['email'];
                    $magento_customers_address = json_decode( json_encode( $proxy->customerAddressList($sessionId, $customerId) ), true );

                    if(count($magento_customers_address) > 0){
                        foreach($magento_customers_address as $ck=>$customer_address){
                            if(trim($customer_address['telephone']) != ''){
                                $customer_phone = $this->FormatPhonenumber($customer_address['telephone'], $customer_address['country_id']);

                                //Check if customer exists in ERP, with email and phone number
                                if(!$this->CheckERPCustomer($customer_email, $customer_phone)){

                                    $customerInfo = $this->SetCustomer($customer, $customer_address);

                                    //Add new customer to ERP
                                    $this->AddNewCustomerToERP($customerInfo);
                                }
                            }
                        }
                    }
                }
            }
        } catch (\SoapFault $fault) {
            // can't connect magento API server
            dump("Can't connect Magento via SOAP: " . $fault->getMessage());
        }

        $report->update(['end_time' => Carbon:: now()]);
    }

    /**
     * Check if customer exist in ERP.
     *
     * @return boolean
     */
    public function CheckERPCustomer($email, $phonenumber)
    {
        //$phone number might need format.. will have to check database for properly matching the phonenumber
        $customer = Customer::where('email', $email)->where('phone', $phonenumber)->first();
        
        return ($customer) ? true : false;
    }

    /**
     * Form customer data from magento.
     *
     * @return array
     */
    public function SetCustomer($customerInfo, $customerAddress)
    {
        $customer = [];
        $customer['name'] = $customerInfo['firstname'].' '.$customerInfo['lastname'];
        $customer['email'] = $customerInfo['email'];
        $customer['address'] = $customerAddress['street'];
        $customer['city'] = $customerAddress['city'];
        $customer['country'] = $customerAddress['country_id'];
        $customer['pincode'] = $customerAddress['postcode'];
        $customer['phone'] = $this->FormatPhonenumber($customerAddress['telephone'], $customerAddress['country_id']);

        return $customer;
    }

    /**
     * Add new customer into ERP.
     *
     * @return boolean
     */
    public function AddNewCustomerToERP($customerInfo)
    {
        $customer = new Customer;
        $customer->name = $customerInfo['name'];
        $customer->email = $customerInfo['email'];
        $customer->address = $customerInfo['address'];
        $customer->city = $customerInfo['city'];
        $customer->country = $customerInfo['country'];
        $customer->pincode = $customerInfo['pincode'];
        $customer->phone = $customerInfo['phone'];

        $customer->save();
    }

    /**
     * Format customer phone number
     *
     * @return string
     */
    public function FormatPhonenumber($phonenumber, $country_id)
    {
        $customer_phone = str_replace(' ', '', $phonenumber);
        //not sure if we need below functionality, but its been used in other part of application
        /*$customer_phone = (int) str_replace(' ', '', $phonenumber);
        $customer_phone = str_replace(' ', '', $phonenumber);
        if ($country_id == 'IN') {
            if (strlen($customer_phone) <= 10) {
                $customer_phone = '91' . $customer_phone;
            }
        }*/

        return $customer_phone;
    }
}
