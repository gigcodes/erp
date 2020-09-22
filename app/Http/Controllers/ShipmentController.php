<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Email;
use App\Library\DHL\CreateShipmentRequest;
use App\MailinglistTemplate;
use App\Mails\Manual\ShipmentEmail;
use App\Order;
use App\Waybill;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Mail;
use Illuminate\Http\Request;
use Exception;
use Validator;
use Mtc\Dhl\Entity\GB\BookPURequest;
use Mtc\Dhl\Client\Web;
use Carbon\Carbon;

class ShipmentController extends Controller
{
    protected $wayBill, $emails;
    public function __construct(Waybill $wayBill, Email $emails) {
        $this->wayBill = $wayBill;
        $this->emails = $emails;
    }

    /**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
        $waybills = $this->wayBill;
        if($request->get('awb')){
            $waybills->where('awb','=',$request->get('awb'));
        }
        if($request->get('destination')){
            $waybills->where('destination','like','%'.$request->get('destination').'%');
        }
        if($request->get('consignee')){
           $customer_name = Customer::where('name','like','%'.$request->get('consignee').'%')->select('id')->get()->toArray();
           $ids = [];
           foreach($customer_name as $cus){
               array_push($ids, $cus['id']);
           }
           $waybills->whereIn('customer_id',$ids);
        }
		$waybills = $waybills->orderBy('id', 'desc')->with('order', 'order.customer', 'customer');
        $waybills_array = $waybills->paginate(20);
        $customers = Customer::all();
        $mailinglist_templates = MailinglistTemplate::groupBy('name')->get();
		return view( 'shipment.index', ['waybills_array' => $waybills_array,
            'customers' => $customers, 'template_names' => $mailinglist_templates,
            'countries' => config('countries')
        ]);
    }

    /**
     * Send an email to dhl
     */
    public function sendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|min:3|max:255',
            'message' => 'required',
            'to.*' => 'required|email',
            'cc.*' => 'nullable|email',
            'bcc.*' => 'nullable|email'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $fromEmail = 'buying@amourint.com';
        $fromName  =  "buying";

        $file_paths = [];

        if ($request->hasFile('file')) {
            $path = "shipment/".$request->order_id;
            foreach ($request->file('file') as $file) {
            $filename = $file->getClientOriginalName();

            $file->storeAs($path, $filename, 'files');

            $file_paths[] = "$path/$filename";
            }
        }

        $cc = $bcc = [];
        $emails = $request->to;

        if ($request->has('cc')) {
            $cc = array_values(array_filter($request->cc));
        }
        if ($request->has('bcc')) {
            $bcc = array_values(array_filter($request->bcc));
        }

        $to = array_shift($emails);
        $cc = array_merge($emails, $cc);

        $mail = Mail::to($to);

        if ($cc) {
            $mail->cc($cc);
        }
        if ($bcc) {
            $mail->bcc($bcc);
        }

        // return $mail;
        $mail->send(new ShipmentEmail($request->subject, $request->message, $file_paths, ["from" => $fromEmail]));

        $params = [
            'model_id' => $request->order_id,
            'model_type' => Order::class,
            'from' => $fromEmail,
            'to' => implode(',', $request->to),
            'seen' => 1,
            'subject' => $request->subject,
            'message' => $request->message,
            'template' => $request->template,
            'additional_data' => json_encode(['attachment' => $file_paths]),
            'cc' => ($cc) ? implode(',', $cc) : null,
            'bcc' => ($bcc) ? implode(',', $bcc) : null
        ];

        $this->emails::create($params);

        return redirect()->route('shipment.index')->withSuccess('You have successfully sent an email!');

    }

    /**
     * View communication email sent
     */
    public function viewSentEmail(Request $request)
    {
        $emails = $this->emails->where('model_type', Order::class)
                ->where('model_id', $request->order_id)->orderBy('id', 'desc')->get();

        return view('shipment.partial.load_sent_email_data', ['emails' => $emails])->render();
    }


    public function showCustomerDetails($id)
    {
        try {
            $customer_details = Customer::where('id','=',$id)->firstOrFail();
            return new JsonResponse(['status' => 1, 'message' => 'Customer detail found', 'data' => $customer_details]);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 0, 'message' => 'No data found']);
        }
    }

    public function generateShipment(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'customer_id' => 'required|numeric',
            'customer_city' => 'required|string',
            'customer_country' => 'required|string',
            'customer_phone' => 'required|numeric',
            'customer_address1' => 'required|string|min:1|max:40',
            'actual_weight' => 'required|numeric',
            'box_length' => 'required|numeric',
            'box_width' => 'required|numeric',
            'box_height' => 'required|numeric',
            'amount' => 'required|numeric',
            'currency' => 'required',
            'pickup_time' => 'required',
            'service_type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        try {
            //get customer details
            $customer = Customer::where(['id' => $request->customer_id])->first();
            $rateReq   = new CreateShipmentRequest("soap");
            $rateReq->setShipper([
                "street" 		=> config("dhl.shipper.street"),
                "city" 			=> config("dhl.shipper.city"),
                "postal_code" 	=> config("dhl.shipper.postal_code"),
                "country_code"	=> config("dhl.shipper.country_code"),
                "person_name" 	=> config("dhl.shipper.person_name"),
                "company_name" 	=> "Solo Luxury",
                "phone" 		=> config("dhl.shipper.phone")
            ]);
            $rateReq->setRecipient([
                "street" 		=> $request->customer_address1,
                "city" 			=> $request->customer_city,
                "postal_code" 	=> $request->customer_pincode,
                "country_code" 	=> $request->customer_country,
                "person_name" 	=> $customer->name,
                "company_name" 	=> $customer->name,
                "phone" 		=> $request->customer_phone
            ]);

            $rateReq->setShippingTime(gmdate("Y-m-d\TH:i:s",strtotime($request->pickup_time))." GMT+05:30");
            $rateReq->setDeclaredValue($request->amount);
            $rateReq->setCurrency($request->currency);
            $rateReq->setPackages([
                [
                    "weight" => (float)$request->actual_weight,
                    "length" => $request->box_length,
                    "width"  => $request->box_width,
                    "height" => $request->box_height,
                    "note"   => "N/A",
                ]
            ]);

            $phone = !empty($request->customer_phone) ? $request->customer_phone : '';
            $rateReq->setMobile($phone);
            $rateReq->setServiceType($request->service_type);
            $response = $rateReq->call();
            if(!$response->hasError()) {
                $receipt = $response->getReceipt();
                if(!empty($receipt["label_format"])){
                    if(strtolower($receipt["label_format"]) == "pdf") {
                        Storage::disk('files')->put('waybills/' . $receipt["tracking_number"] . '_package_slip.pdf', $bin = base64_decode($receipt["label_image"], true));
                        $waybill = new Waybill;
                        $waybill->order_id = null;
                        $waybill->customer_id = $request->customer_id;
                        $waybill->awb = $receipt["tracking_number"];
                        $waybill->box_width = $request->box_width;
                        $waybill->box_height = $request->box_height;
                        $waybill->box_length = $request->box_length;
                        $waybill->actual_weight = (float)$request->get("actual_weight");
                        $volume_weight = $request->box_width*$request->box_height*$request->box_length/5000;
                        $waybill->volume_weight = (float)$volume_weight;
                        $waybill->cost_of_shipment = $request->amount.' '.$request->currency;
                        $waybill->duty_cost = null; #TODO after discussing
                        $waybill->package_slip = $receipt["tracking_number"] . '_package_slip.pdf';
                        $waybill->pickup_date = $request->pickup_time;
                        $waybill->save();
                    }
                }
                return response()->json([
                    'success' => true
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'globalErrors' => $response->getErrorMessage(),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'globalErrors' => $e->getMessage(),
            ]);
        }
    }

    public function getShipmentByName($name)
    {
        $all_templates = MailinglistTemplate::where('name','=',$name)->get();
        return new JsonResponse(['status' => 1, 'data' => $all_templates]);
    }

    public function show(){
        $pickup = new BookPURequest();
        $pickup->SiteID = '968267613';
        $pickup->Password = 'S#8sD@2gP$2o';
        $pickup->MessageTime = Carbon::now()->format(Carbon::ATOM);
        $pickup->MessageReference = 'reference_28_to_32_characters';

        $pickup->Requestor->AccountNumber = '968267613';
        $pickup->Requestor->AccountType = 'D';
        $pickup->Requestor->CompanyName = 'Webguruz';
        $pickup->Requestor->RequestorContact->PersonName = 9034666652;
        $pickup->Requestor->RequestorContact->Phone = 9034666652;

        // DHL REGION based on the Toolkit documentation for the pickup country
        $pickup->RegionCode = 'AP';

        $pickup->Place->LocationType = 'B';
        $pickup->Place->CompanyName = 'Webguruz technologies PVT LTD';
        $pickup->Place->Address1 = "4th floor Dibon building";
        $pickup->Place->Address2 = "Sector 67";
        $pickup->Place->PackageLocation = 'Ask receptionist';
        $pickup->Place->City = "Mohali";
        $pickup->Place->CountryCode = "IN";
        $pickup->Place->PostalCode = 160062;

        $pickup->Pickup->PickupDate = '2021-02-05';
        $pickup->Pickup->ReadyByTime = '13:00';
        $pickup->Pickup->CloseTime = '17:00';

        $pickup->PickupContact->PersonName = 9034666652;
        $pickup->PickupContact->Phone = 903466652;

        $pickup->ShipmentDetails->AccountType = 'D';
        $pickup->ShipmentDetails->AccountNumber = '968267613';
        $pickup->ShipmentDetails->BillToAccountNumber = '968267613';

        $pickup->ShipmentDetails->AWBNumber = 1111111;
        $pickup->ShipmentDetails->NumberOfPieces = 1;
        $pickup->ShipmentDetails->GlobalProductCode = "ABC";
        $pickup->ShipmentDetails->Weight = "2.5";
        $pickup->ShipmentDetails->WeightUnit = 'K';
        $pickup->ShipmentDetails->DoorTo = 'DD';
        $pickup->ShipmentDetails->DimensionUnit = 'C';

        $client = new Web();
        $xml_response = $client->call($pickup);
        dd($xml_response);
    }


}