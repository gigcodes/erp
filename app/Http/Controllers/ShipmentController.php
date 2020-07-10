<?php

namespace App\Http\Controllers;

use App\Email;
use App\Mails\Manual\ShipmentEmail;
use App\Order;
use App\Waybill;
use Mail;
use Illuminate\Http\Request;
use Exception;
use Validator;

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

		$waybills = $this->wayBill->orderBy('id', 'desc')->with('order', 'order.customer');

		$waybills_array = $waybills->paginate(20);

		return view( 'shipment.index', ['waybills_array' => $waybills_array]);
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
            'template' => '',
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
}