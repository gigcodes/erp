<?php

namespace App\Http\Controllers;

use App\User;
use App\Email;
use Carbon\Carbon;
use App\EmailAddress;
use App\StoreWebsite;
use App\VirtualminHelper;
use App\EmailRunHistories;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\EmailFailedReport;
use Webklex\PHPIMAP\ClientManager;
use Maatwebsite\Excel\Facades\Excel;
use EmailReplyParser\Parser\EmailParser;
use Illuminate\Support\Facades\DB;

class EmailAddressesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = EmailAddress::query();
        //$queryNew = new EmailAddress;
        //dd($query);
        $query->select('email_addresses.*')->with(['email_assignes',
            'history_last_message' => function ($q) use ($request) {
                //dd($request->website_id);
                if ($request->status) {
                    $q->where('is_success', $request->status)->orderBy('id', 'DESC')->limit(1);
                }
            }, 'history_last_message_error',
        ]);

        $columns = ['from_name', 'from_address', 'driver', 'host', 'port', 'encryption', 'send_grid_token'];

        if ($request->keyword) {
            $query->orWhere('driver', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('port', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('encryption', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('send_grid_token', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('host', 'LIKE', '%' . $request->keyword . '%');
        }

        if ($request->username != '') {
            $query->where('username', 'LIKE', '%' . $request->username . '%');
        }

        if ($request->website_id != '') {
            $query->where('store_website_id', $request->website_id);
        }

        //$query->where('id', 1);

        // dd($query);
        $emailAddress = $query->paginate(\App\Setting::get('pagination', 10))->appends(request()->query());
        //dd($emailAddress->website);
        $allStores = StoreWebsite::all();
        // Retrieve all email addresses
        $emailAddresses = EmailAddress::all();
        $runHistoriesCount = EmailRunHistories::count();

        $allDriver = $emailAddresses->pluck('driver')->unique()->toArray();
        $allIncomingDriver = $emailAddresses->pluck('incoming_driver')->unique()->toArray();
        $allPort = $emailAddresses->pluck('port')->unique()->toArray();
        $allEncryption = $emailAddresses->pluck('encryption')->unique()->toArray();

        // default values for add form
        $defaultDriver = 'smtp';
        $defaultPort = '587';
        $defaultEncryption = 'tls';
        $defaultHost = 'mail.mio-moda.com';

        $users = User::orderBy('name', 'asc')->get()->toArray();
        // dd($users);
        $userEmails = $emailAddresses->pluck('username')->unique()->toArray();
        $fromAddresses = $emailAddresses->pluck('from_address')->unique()->toArray();

        $ops = '';
        foreach ($users as $key => $user) {
            $ops .= '<option class="form-control" value="' . $user['id'] . '">' . $user['name'] . '</option>';
        }
        //dd($ops);
        if ($request->ajax()) {
            return view('email-addresses.index_ajax', [
                'emailAddress' => $emailAddress,
                'allStores' => $allStores,
                'allDriver' => $allDriver,
                'allIncomingDriver' => $allIncomingDriver,
                'allPort' => $allPort,
                'allEncryption' => $allEncryption,
                'users' => $users,
                'uops' => $ops,
                'userEmails' => $userEmails,
                'defaultDriver' => $defaultDriver,
                'defaultPort' => $defaultPort,
                'defaultEncryption' => $defaultEncryption,
                'defaultHost' => $defaultHost,
                'fromAddresses' => $fromAddresses,
                'runHistoriesCount' => $runHistoriesCount
            ]);
        } else {
            return view('email-addresses.index', [
                'emailAddress' => $emailAddress,
                'allStores' => $allStores,
                'allDriver' => $allDriver,
                'allIncomingDriver' => $allIncomingDriver,
                'allPort' => $allPort,
                'allEncryption' => $allEncryption,
                'users' => $users,
                'uops' => $ops,
                'userEmails' => $userEmails,
                'defaultDriver' => $defaultDriver,
                'defaultPort' => $defaultPort,
                'defaultEncryption' => $defaultEncryption,
                'defaultHost' => $defaultHost,
                'fromAddresses' => $fromAddresses,
                'runHistoriesCount' => $runHistoriesCount
            ]);
        }
    }

    public function runHistoriesTruncate()
    {
        EmailRunHistories::truncate();

        return redirect()->back()->withSuccess('Data Removed Successfully!');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'from_name' => 'required|string|max:255',
            'from_address' => 'required|string|max:255',
            'incoming_driver' => 'required|string|max:255',
            'driver' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            //'send_grid_token' => 'required|string',
            'port' => 'required|string|max:255',
            'encryption' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            //'recovery_phone' => 'required|string|max:255',
            //'recovery_email' => 'required|string|max:255',

        ]);

        $data = $request->except('_token', 'signature_logo', 'signature_image');

        $id = EmailAddress::insertGetId($data);

        $signature_logo = $request->file('signature_logo');
        $signature_image = $request->file('signature_image');
        $destinationPath = public_path('uploads');

        if ($signature_logo != '') {
            $signature_logo->move($destinationPath, $signature_logo->getClientOriginalName());
            EmailAddress::find($id)->update(['signature_logo' => $signature_logo->getClientOriginalName()]);
        }
        if ($signature_image != '') {
            $signature_image->move($destinationPath, $signature_image->getClientOriginalName());
            EmailAddress::find($id)->update(['signature_image' => $signature_image->getClientOriginalName()]);
        }

        $this->createEmail($id, $data['host'], $data['username'], $data['password']);

        return redirect()->route('email-addresses.index')->withSuccess('You have successfully saved a Email Address!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'from_name' => 'required|string|max:255',
            'from_address' => 'required|string|max:255',
            'incoming_driver' => 'required|string|max:255',
            'driver' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|string|max:255',
            //'send_grid_token' => 'required|string',
            'encryption' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            //'recovery_phone' => 'required|string|max:255',
            //'recovery_email' => 'required|string|max:255',

        ]);

        $data = $request->except('_token', 'signature_logo', 'signature_image');

        EmailAddress::find($id)->update($data);

        $signature_logo = $request->file('signature_logo');
        $signature_image = $request->file('signature_image');
        $destinationPath = public_path('uploads');

        if ($signature_logo != '') {
            $signature_logo->move($destinationPath, $signature_logo->getClientOriginalName());
            EmailAddress::find($id)->update(['signature_logo' => $signature_logo->getClientOriginalName()]);
        }
        if ($signature_image != '') {
            $signature_image->move($destinationPath, $signature_image->getClientOriginalName());
            EmailAddress::find($id)->update(['signature_image' => $signature_image->getClientOriginalName()]);
        }

        $this->updateEmailPassword($id, $data['host'], $data['username'], $data['password']);

        return redirect()->back()->withSuccess('You have successfully updated a Email Address!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $emailAddress = EmailAddress::find($id);

        $emailAddress->delete();

        return redirect()->route('email-addresses.index')->withSuccess('You have successfully deleted a Email Address');
    }

    public function getEmailAddressHistory(Request $request)
    {
        $EmailHistory = EmailRunHistories::where('email_run_histories.email_address_id', $request->id)
            ->whereDate('email_run_histories.created_at', Carbon::today())
            ->join('email_addresses', 'email_addresses.id', 'email_run_histories.email_address_id')
            ->select(['email_run_histories.*', 'email_addresses.from_name'])
            ->latest()
            ->get();

        $history = '';
        if (count($EmailHistory) > 0) {
            foreach ($EmailHistory as $runHistory) {
                $status = ($runHistory->is_success == 0) ? 'Failed' : 'Success';
                $message = empty($runHistory->message) ? '-' : $runHistory->message;
                $history .= '<tr>
                <td>' . $runHistory->id . '</td>
                <td>' . $runHistory->from_name . '</td>
                <td>' . $status . '</td>
                <td>' . $message . '</td>
                <td>' . $runHistory->created_at->format('Y-m-d H:i:s') . '</td>
                </tr>';
            }
        } else {
            $history .= '<tr>
                    <td colspan="5">
                        No Result Found
                    </td>
                </tr>';
        }

        return response()->json(['data' => $history]);
    }

    public function getRelatedAccount(Request $request)
    {
        $adsAccounts = \App\GoogleAdsAccount::where('account_name', $request->id)->get();
        $translations = \App\googleTraslationSettings::where('email', $request->id)->get();
        $analytics = \App\StoreWebsiteAnalytic::where('email', $request->id)->get();

        $accounts = [];

        if (! $adsAccounts->isEmpty()) {
            foreach ($adsAccounts as $adsAccount) {
                $accounts[] = [
                    'name' => $adsAccount->account_name,
                    'email' => $adsAccount->account_name,
                    'last_error' => $adsAccount->last_error,
                    'last_error_at' => $adsAccount->last_error_at,
                    'credential' => $adsAccount->config_file_path,
                    'store_website' => $adsAccount->store_websites,
                    'status' => $adsAccount->status,
                    'type' => 'Google Ads Account',
                ];
            }
        }

        if (! $translations->isEmpty()) {
            foreach ($translations as $translation) {
                $accounts[] = [
                    'name' => $translation->email,
                    'email' => $translation->email,
                    'last_error' => $translation->last_note,
                    'last_error_at' => $translation->last_error_at,
                    'credential' => $translation->account_json,
                    'store_website' => 'N/A',
                    'status' => $translation->status,
                    'type' => 'Google Translation',
                ];
            }
        }

        if (! $analytics->isEmpty()) {
            foreach ($analytics as $analytic) {
                $accounts[] = [
                    'name' => $analytic->email,
                    'email' => $analytic->email,
                    'last_error' => $analytic->last_error,
                    'last_error_at' => $analytic->last_error_at,
                    'credential' => $analytic->account_id . ' - ' . $analytic->view_id,
                    'store_website' => $analytic->website,
                    'status' => 'N/A',
                    'type' => 'Google Analytics',
                ];
            }
        }

        return view('email-addresses.partials.task', compact('accounts'));
    }

    public function getErrorEmailHistory(Request $request)
    {
        ini_set('memory_limit', -1);

        $histories = EmailAddress::whereHas('history_last_message', function ($query) {
            $query->where('is_success', 0);
        })
            ->with(['history_last_message' => function ($q) {
                $q->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-10 day')));
            }])
            ->get();

        $history = '';

        if ($histories) {
            foreach ($histories as $row) {
                if ($row->history_last_message) {
                    $status = ($row->history_last_message->is_success == 0) ? 'Failed' : 'Success';
                    $message = $row->history_last_message->message ?? '-';
                    $history .= '<tr>
                    <td>' . $row->history_last_message->id . '</td>
                    <td>' . $row->from_name . '</td>
                    <td>' . $status . '</td>
                    <td>' . $message . '</td>
                    <td>' . $row->history_last_message->created_at->format('Y-m-d H:i:s') . '</td>
                    </tr>';
                }
            }
        } else {
            $history .= '<tr>
                    <td colspan="5">
                        No Result Found
                    </td>
                </tr>';
        }

        return response()->json(['data' => $history]);
    }

    public function downloadFailedHistory(Request $request)
    {
        $histories = EmailAddress::whereHas('history_last_message', function ($query) {
            $query->where('is_success', 0);
        })
            ->with(['history_last_message' => function ($q) {
                $q->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-1 day')));
            }])
            ->get();

        $recordsArr = [];
        foreach ($histories as $row) {
            if ($row->history_last_message) {
                $recordsArr[] = [
                    'id' => $row->history_last_message->id,
                    'from_name' => $row->from_name,
                    'status' => ($row->history_last_message->is_success == 0) ? 'Failed' : 'Success',
                    'message' => $row->history_last_message->message ?? '-',
                    'created_at' => $row->history_last_message->created_at->format('Y-m-d H:i:s'),
                ];
            }
        }
        $filename = 'Report-Email-failed' . '.csv';

        return Excel::download(new EmailFailedReport($recordsArr), $filename);
    }

    public function passwordChange(Request $request)
    {
        if (empty($request->users)) {
            return redirect()->back()->with('error', 'Please select user');
        }

        $users = explode(',', $request->users);
        $data = [];
        foreach ($users as $key) {
            // Generate new password
            $newPassword = Str::random(12);

            $user = EmailAddress::findorfail($key);
            $user->password = $newPassword;
            $user->save();
            $data[$key] = $newPassword;

            //update password in virtualmin
            $this->updateEmailPassword($user->id, $user->host, $user->username, $newPassword);
        }
        \Session::flash('success', 'Password Updated');

        return redirect()->back();
    }

    public function sendToWhatsApp(Request $request)
    {
        $emailDetail = EmailAddress::find($request->id);
        $user_id = $request->user_id;
        $user = User::findorfail($user_id);
        $number = $user->phone;
        $whatsappnumber = '971502609192';

        $message = 'Password For ' . $emailDetail->username . 'is: ' . $emailDetail->password;

        $whatsappmessage = new WhatsAppController();
        $whatsappmessage->sendWithThirdApi($number, $user->whatsapp_number, $message);
        \Session::flash('success', 'Password sent');

        return redirect()->back();
    }

    public function assignUsers(Request $request)
    {
        $emailDetail = EmailAddress::find($request->email_id);
        $data = [];
        $clear_existing_data = \App\EmailAssign::where(['email_address_id' => $request->email_id])->delete();
        if (isset($request->users)) {
            foreach ($request->users as $_user) {
                $data[] = ['user_id' => $_user, 'email_address_id' => $request->email_id, 'created_at' => Carbon::today(), 'updated_at' => Carbon::today()];
            }
        }

        if (count($data) > 0) {
            $data_added = \App\EmailAssign::insert($data);

            return redirect()->back()->withSuccess('You have successfully assigned users to email address!');
        }

        return redirect()->back();
    }

    public function searchEmailAddress(Request $request)
    {
        $search = $request->search;

        if ($search != null) {
            $emailAddress = EmailAddress::where('username', 'Like', '%' . $search . '%')->orWhere('password', 'Like', '%' . $search . '%')->get();
        } else {
            $emailAddress = EmailAddress::get();
        }

        return response()->json(['tbody' => view('email-addresses.partials.email-address', compact('emailAddress'))->render()], 200);
    }

    public function updateEmailAddress(Request $request)
    {
        $usernames = $request->username;

        if ($request->username && $request->password) {
            foreach ($usernames as $key => $username) {
                EmailAddress::where('id', $key)->update(['username' => $username, 'password' => $request->password[$key]]);
            }
            session()->flash('msg', 'Email And Password Updated Successfully.');

            return redirect()->back();
        } else {
            session()->flash('msg', 'Please Try Again.');

            return redirect()->back();
        }
    }

    //create email in virtualmin
    public function createEmail($id, $smtpHost, $user, $password): string
    {
        $mailHelper = new VirtualminHelper();
        $result = parse_url(getenv('VIRTUALMIN_ENDPOINT'));
        $vmHost = isset($result['host']) ? $result['host'] : '';
        $status = 'failure';
        if ($smtpHost == $vmHost) {
            $response = $mailHelper->createMail($smtpHost, $user, $password);
            $status = 'failure';
            if ($response['code'] == 200) {
                $status = $response['data']['status'];
                EmailAddress::find($id)->update(['username' => $user . '@' . $smtpHost]);
            }
        }

        return $status;
    }

    //update password in virtualmin
    public function updateEmailPassword($id, $smtpHost, $user, $password): string
    {
        $mailHelper = new VirtualminHelper();
        $result = parse_url(getenv('VIRTUALMIN_ENDPOINT'));
        $vmHost = isset($result['host']) ? $result['host'] : '';
        $status = 'failure';
        if ($smtpHost == $vmHost) {
            $response = $mailHelper->changeMailPassword($smtpHost, $user, $password);
            $status = 'failure';
            if ($response['code'] == 200) {
                $status = $response['data']['status'];
                $parts = explode('@', $user);
                EmailAddress::find($id)->update(['username' => $parts[0] . '@' . $smtpHost]);
            }
        }

        return $status;
    }

    public function singleEmailRunCron(Request $request)
    {
        $emailAddresses = EmailAddress::where('id', $request->get('id'))->first();

        $emailAddress = $emailAddresses;
        try {
            $cm = new ClientManager();
            $imap = $cm->make([
                'host' => $emailAddress->host,
                'port' => 993,
                'encryption' => 'ssl',
                'validate_cert' => false,
                'username' => $emailAddress->username,
                'password' => $emailAddress->password,
                'protocol' => 'imap',
            ]);

            $imap->connect();

            $types = [
                'inbox' => [
                    'inbox_name' => 'INBOX',
                    'direction' => 'from',
                    'type' => 'incoming',
                ],
                'sent' => [
                    'inbox_name' => 'INBOX.Sent',
                    'direction' => 'to',
                    'type' => 'outgoing',
                ],
            ];

            $available_models = [
                'supplier' => \App\Supplier::class, 'vendor' => \App\Vendor::class,
                'customer' => \App\Customer::class, 'users' => \App\User::class,
            ];
            $email_list = [];
            foreach ($available_models as $key => $value) {
                $email_list[$value] = $value::whereNotNull('email')->pluck('id', 'email')->unique()->all();
            }

            foreach ($types as $type) {
                $inbox = $imap->getFolder($type['inbox_name']);
                if ($type['type'] == 'incoming') {
                    $latest_email = Email::where('to', $emailAddress->from_address)->where('type', $type['type'])->latest()->first();
                } else {
                    $latest_email = Email::where('from', $emailAddress->from_address)->where('type', $type['type'])->latest()->first();
                }

                $latest_email_date = $latest_email ? Carbon::parse($latest_email->created_at) : false;
                if ($latest_email_date) {
                    $emails = ($inbox) ? $inbox->messages()->where('SINCE', $latest_email_date->subDays(1)->format('d-M-Y')) : '';
                } else {
                    $emails = ($inbox) ? $inbox->messages() : '';
                }
                if ($emails) {
                    $emails = $emails->all()->get();
                    foreach ($emails as $email) {
                        try {
                            $reference_id = $email->references;
                            $origin_id = $email->message_id;

                            // Skip if message is already stored
                            if (Email::where('origin_id', $origin_id)->count() > 0) {
                                continue;
                            }

                            // check if email has already been received

                            $textContent = $email->getTextBody();
                            if ($email->hasHTMLBody()) {
                                $content = $email->getHTMLBody();
                            } else {
                                $content = $email->getTextBody();
                            }

                            $email_subject = $email->getSubject();
                            \Log::channel('customer')->info('Subject  => ' . $email_subject);

                            //if (!$latest_email_date || $email->getDate()->timestamp > $latest_email_date->timestamp) {
                            $attachments_array = [];
                            $attachments = $email->getAttachments();
                            $fromThis = $email->getFrom()[0]->mail;
                            $attachments->each(function ($attachment) use (&$attachments_array, $fromThis, $email_subject) {
                                $attachment->name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $attachment->name);
                                file_put_contents(storage_path('app/files/email-attachments/' . $attachment->name), $attachment->content);
                                $path = 'email-attachments/' . $attachment->name;

                                $attachments_array[] = $path;

                                /*start 3215 attachment fetch from DHL mail */
                                \Log::channel('customer')->info('Match Start  => ' . $email_subject);

                                $findFromEmail = explode('@', $fromThis);
                                if (strpos(strtolower($email_subject), 'your copy invoice') !== false && isset($findFromEmail[1]) && (strtolower($findFromEmail[1]) == 'dhl.com')) {
                                    \Log::channel('customer')->info('Match Found  => ' . $email_subject);
                                    $this->getEmailAttachedFileData($attachment->name);
                                }
                                /*end 3215 attachment fetch from DHL mail */
                            });

                            $from = $email->getFrom()[0]->mail;
                            $to = array_key_exists(0, $email->getTo()->toArray()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail;

                            // Model is sender if its incoming else its receiver if outgoing
                            if ($type['type'] == 'incoming') {
                                $model_email = $from;
                            } else {
                                $model_email = $to;
                            }

                            // Get model id and model type

                            extract($this->getModel($model_email, $email_list));

                            $subject = explode('#', $email_subject);
                            if (isset($subject[1]) && ! empty($subject[1])) {
                                $findTicket = \App\Tickets::where('ticket_id', $subject[1])->first();
                                if ($findTicket) {
                                    $model_id = $findTicket->id;
                                    $model_type = \App\Tickets::class;
                                }
                            }

                            $params = [
                                'model_id' => $model_id,
                                'model_type' => $model_type,
                                'origin_id' => $origin_id,
                                'reference_id' => $reference_id,
                                'type' => $type['type'],
                                'seen' => isset($email->getFlags()['seen']) ? $email->getFlags()['seen'] : 0,
                                'from' => $email->getFrom()[0]->mail,
                                'to' => array_key_exists(0, $email->getTo()->toArray()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                'subject' => $email->getSubject(),
                                'message' => $content,
                                'template' => 'customer-simple',
                                'additional_data' => json_encode(['attachment' => $attachments_array]),
                                'created_at' => $email->getDate(),
                            ];

                            $email_id = Email::insertGetId($params);

                            if ($type['type'] == 'incoming') {
                                $message = trim($textContent);

                                $reply = (new EmailParser())->parse($message);

                                $fragment = current($reply->getFragments());

                                $pattern = '(On[^abc,]*, (Jan(uary)?|Feb(ruary)?|Mar(ch)?|Apr(il)?|May|Jun(e)?|Jul(y)?|Aug(ust)?|Sep(tember)?|Oct(ober)?|Nov(ember)?|Dec(ember)?)\s+\d{1,2},\s+\d{4}, (1[0-2]|0?[1-9]):([0-5][0-9]) ([AaPp][Mm]))';

                                $reply = strip_tags($fragment);

                                $reply = preg_replace($pattern, ' ', $reply);

                                $mailFound = false;
                                if ($reply) {
                                    $customer = \App\Customer::where('email', $from)->first();
                                    if (! empty($customer)) {
                                        // store the main message
                                        $params = [
                                            'number' => $customer->phone,
                                            'message' => $reply,
                                            'media_url' => null,
                                            'approved' => 0,
                                            'status' => 0,
                                            'contact_id' => null,
                                            'erp_user' => null,
                                            'supplier_id' => null,
                                            'task_id' => null,
                                            'dubizzle_id' => null,
                                            'vendor_id' => null,
                                            'customer_id' => $customer->id,
                                            'is_email' => 1,
                                            'from_email' => $from,
                                            'to_email' => $to,
                                            'email_id' => $email_id,
                                        ];
                                        $messageModel = \App\ChatMessage::create($params);
                                        \App\Helpers\MessageHelper::whatsAppSend($customer, $reply, null, null, $isEmail = true);
                                        \App\Helpers\MessageHelper::sendwatson($customer, $reply, null, $messageModel, $params, $isEmail = true);
                                        $mailFound = true;
                                    }

                                    if (! $mailFound) {
                                        $vandor = \App\Vendor::where('email', $from)->first();
                                        if ($vandor) {
                                            $params = [
                                                'number' => $vandor->phone,
                                                'message' => $reply,
                                                'media_url' => null,
                                                'approved' => 0,
                                                'status' => 0,
                                                'contact_id' => null,
                                                'erp_user' => null,
                                                'supplier_id' => null,
                                                'task_id' => null,
                                                'dubizzle_id' => null,
                                                'vendor_id' => $vandor->id,
                                                'is_email' => 1,
                                                'from_email' => $from,
                                                'to_email' => $to,
                                                'email_id' => $email_id,
                                            ];
                                            $messageModel = \App\ChatMessage::create($params);
                                            $mailFound = true;
                                        }
                                    }

                                    if (! $mailFound) {
                                        $supplier = \App\Supplier::where('email', $from)->first();
                                        if ($supplier) {
                                            $params = [
                                                'number' => $supplier->phone,
                                                'message' => $reply,
                                                'media_url' => null,
                                                'approved' => 0,
                                                'status' => 0,
                                                'contact_id' => null,
                                                'erp_user' => null,
                                                'supplier_id' => $supplier->id,
                                                'task_id' => null,
                                                'dubizzle_id' => null,
                                                'is_email' => 1,
                                                'from_email' => $from,
                                                'to_email' => $to,
                                                'email_id' => $email_id,
                                            ];
                                            $messageModel = \App\ChatMessage::create($params);
                                            $mailFound = true;
                                        }
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::error('error while fetching some emails for ' . $emailAddress->username . ' Error Message: ' . $e->getMessage());
                            $historyParam = [
                                'email_address_id' => $emailAddress->id,
                                'is_success' => 0,
                                'message' => 'error while fetching some emails for ' . $emailAddress->username . ' Error Message: ' . $e->getMessage(),
                            ];
                            EmailRunHistories::create($historyParam);
                        }
                    }
                }
            }

            $historyParam = [
                'email_address_id' => $emailAddress->id,
                'is_success' => 1,
            ];

            EmailRunHistories::create($historyParam);

            return response()->json(['status' => 'success', 'message' => 'Successfully'], 200);
        } catch (\Exception $e) {
            \Log::channel('customer')->info($e->getMessage());
            $historyParam = [
                'email_address_id' => $emailAddress->id,
                'is_success' => 0,
                'message' => $e->getMessage(),
            ];
            EmailRunHistories::create($historyParam);
            \App\CronJob::insertLastError('fetch:all_emails', $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public function listEmailRunLogs(Request $request)
    {
        $eIds = $request->e_ids;
        $searchMessage = $request->search_message;
        $date = $request->date;
        $fromName = $request->search_name;
        $status =  $request->status ?? " "; 

            $emailJobsQuery = DB::table('email_run_histories')
                ->join('email_addresses', 'email_run_histories.email_address_id', '=', 'email_addresses.id')
                ->select(
                    'email_run_histories.*',
                    'email_addresses.from_name as email_from_name'
                )
                ->when($request->search_message, function ($query, $searchMessage) {
                    return $query->where('email_run_histories.message', 'LIKE', '%' . $searchMessage . '%');
                })
                ->when($request->date, function ($query, $date) {
                    return $query->where('email_run_histories.created_at', 'LIKE', '%' . $date . '%');
                })
                ->when($fromName, function ($query, $fromName) {
                    return $query->where('email_addresses.from_name', 'LIKE', '%' . $fromName . '%');
                })
                ->latest();

                if($status != " "){
                    if ($status !== "failed") {
                        $emailJobsQuery->where('email_run_histories.is_success', 1);
                    }
                
                    if ($status === "failed") {
                        $emailJobsQuery->where('email_run_histories.is_success', 0);
                    }
                }
            
            $emailJobs = $emailJobsQuery->paginate(\App\Setting::get('pagination', 25));



        return view('email-addresses.email-run-log-listing', compact('emailJobs'));
    }
}
