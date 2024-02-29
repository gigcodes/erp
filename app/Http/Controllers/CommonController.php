<?php

namespace App\Http\Controllers;

use App\User;
use App\Email;
use App\Vendor;
use App\Charity;
use App\Customer;
use App\Supplier;
use App\MailinglistTemplate;
use Illuminate\Http\Request;
use App\Mails\Manual\PurchaseEmail;

class CommonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function sendCommonEmail(request $request)
    {
        $multi_email = explode(',', $request->sendto);

        try {
            $this->validate($request, [
                'subject' => 'required|min:3|max:255',
                'message' => 'required',
                'cc.*'    => 'nullable|email',
                'bcc.*'   => 'nullable|email',
                'sendto'  => 'required',
            ]);

            if (! empty($request->datatype == 'multi_user')) {
                foreach ($multi_email as $data) {
                    if ($request->from_mail) {
                        $mail = \App\EmailAddress::where('from_address', $request->from_mail)->first();
                        if ($mail) {
                            $fromEmail = $mail->from_address;
                            $fromName  = $mail->from_name;
                            $config    = config('mail');
                            unset($config['sendmail']);
                            $configExtra = [
                                'driver' => $mail->driver,
                                'host'   => $mail->host,
                                'port'   => $mail->port,
                                'from'   => [
                                    'address' => $mail->from_address,
                                    'name'    => $mail->from_name,
                                ],
                                'encryption' => $mail->encryption,
                                'username'   => $mail->username,
                                'password'   => $mail->password,
                            ];
                            \Config::set('mail', array_merge($config, $configExtra));
                            (new \Illuminate\Mail\MailServiceProvider(app()))->register();
                        }
                    }

                    $file_paths = [];

                    if ($request->hasFile('file')) {
                        foreach ($request->file('file') as $file) {
                            $filename = $file->getClientOriginalName();

                            $file->storeAs('documents', $filename, 'files');

                            $file_paths[] = "documents/$filename";
                        }
                    }

                    $cc = $bcc = [];
                    if ($request->has('cc')) {
                        $cc = array_values(array_filter($request->cc));
                    }
                    if ($request->has('bcc')) {
                        $bcc = array_values(array_filter($request->bcc));
                    }

                    $emailClass = (new PurchaseEmail($request->subject, $request->message, $file_paths, ['from' => $fromEmail]))->build();

                    $params = [
                        'model_id'        => $request->id,
                        'from'            => $fromEmail,
                        'seen'            => 1,
                        'to'              => $data,
                        'subject'         => $request->subject,
                        'message'         => $emailClass->render(),
                        'template'        => 'simple',
                        'additional_data' => json_encode(['attachment' => $file_paths]),
                        'cc'              => $cc ?: null,
                        'bcc'             => $bcc ?: null,
                    ];
                    if ($request->object) {
                        if ($request->object == 'vendor') {
                            $params['model_type'] = 'Vendor::class';
                        } elseif ($request->object == 'user') {
                            $params['model_type'] = 'User::class';
                        } elseif ($request->object == 'supplier') {
                            $params['model_type'] = 'Supplier::class';
                        } elseif ($request->object == 'customer') {
                            $params['model_type'] = 'Customer::class';
                        } elseif ($request->object == 'order') {
                            $params['model_type'] = 'Order::class';
                        } elseif ($request->object == 'charity') {
                            $params['model_type'] = 'Charity::class';
                        }
                    }

                    $email = Email::create($params);
                    \App\EmailLog::create(
                        [
                            'email_id'  => $email->id,
                            'email_log' => 'Email initiated',
                            'message'   => $email->to,
                        ]
                    );

                    \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
                    if (isset($request->from) && $request->from == 'sop') {
                        return response()->json(['success' => 'You have send email successfully !']);
                    } else {
                        return redirect()->back()->withSuccess('You have successfully sent email!');
                    }
                }
            } else {
                if ($request->from_mail) {
                    $mail = \App\EmailAddress::where('from_address', $request->from_mail)->first();
                    if ($mail) {
                        $fromEmail = $mail->from_address;
                        $fromName  = $mail->from_name;
                        $config    = config('mail');
                        unset($config['sendmail']);
                        $configExtra = [
                            'driver' => $mail->driver,
                            'host'   => $mail->host,
                            'port'   => $mail->port,
                            'from'   => [
                                'address' => $mail->from_address,
                                'name'    => $mail->from_name,
                            ],
                            'encryption' => $mail->encryption,
                            'username'   => $mail->username,
                            'password'   => $mail->password,
                        ];
                        \Config::set('mail', array_merge($config, $configExtra));
                        (new \Illuminate\Mail\MailServiceProvider(app()))->register();
                    }
                }

                $file_paths = [];

                if ($request->hasFile('file')) {
                    foreach ($request->file('file') as $file) {
                        $filename = $file->getClientOriginalName();

                        $file->storeAs('documents', $filename, 'files');

                        $file_paths[] = "documents/$filename";
                    }
                }

                $cc = $bcc = [];
                if ($request->has('cc')) {
                    $cc = array_values(array_filter($request->cc));
                }
                if ($request->has('bcc')) {
                    $bcc = array_values(array_filter($request->bcc));
                }

                $emailClass = (new PurchaseEmail($request->subject, $request->message, $file_paths, ['from' => $fromEmail]))->build();

                $params = [
                    'model_id'        => $request->id,
                    'from'            => $fromEmail,
                    'seen'            => 1,
                    'to'              => $request->sendto,
                    'subject'         => $request->subject,
                    'message'         => $emailClass->render(),
                    'template'        => 'simple',
                    'additional_data' => json_encode(['attachment' => $file_paths]),
                    'cc'              => $cc ?: null,
                    'bcc'             => $bcc ?: null,
                ];
                if ($request->object) {
                    if ($request->object == 'vendor') {
                        $params['model_type'] = 'Vendor::class';
                    } elseif ($request->object == 'user') {
                        $params['model_type'] = 'User::class';
                    } elseif ($request->object == 'supplier') {
                        $params['model_type'] = 'Supplier::class';
                    } elseif ($request->object == 'customer') {
                        $params['model_type'] = 'Customer::class';
                    } elseif ($request->object == 'order') {
                        $params['model_type'] = 'Order::class';
                    } elseif ($request->object == 'charity') {
                        $params['model_type'] = 'Charity::class';
                    }
                }

                $email = Email::create($params);
                \App\EmailLog::create(
                    [
                        'email_id'  => $email->id,
                        'email_log' => 'Email initiated',
                        'message'   => $email->to,
                    ]
                );

                \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
                if (isset($request->from) && $request->from == 'sop') {
                    return response()->json(['success' => 'You have send email successfully !']);
                } else {
                    return redirect()->back()->withSuccess('You have successfully sent email!');
                }
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function sendClanaderLinkEmail(request $request)
    {
        $objects = [
            'vendor'   => Vendor::class,
            'user'     => User::class,
            'supplier' => Supplier::class,
            'customer' => Customer::class,
            'charity'  => Charity::class,
        ];
        $multi_email = [];
        if (isset($request->send_to) && count($request->send_to) > 0) {
            $multi_email = $request->send_to;
        }

        $this->validate($request, [
            'subject' => 'required|min:3|max:255',
            'message' => 'required',
            'send_to' => 'required',
            'cc.*'    => 'nullable|email',
            'bcc.*'   => 'nullable|email',
        ]);
        try {
            foreach ($multi_email as $data) {
                if ($request->from_mail) {
                    $mail = \App\EmailAddress::where('from_address', $request->from_mail)->first();
                    if ($mail) {
                        $fromEmail = $mail->from_address;
                        $fromName  = $mail->from_name;
                        $config    = config('mail');
                        unset($config['sendmail']);
                        $configExtra = [
                            'driver' => $mail->driver,
                            'host'   => $mail->host,
                            'port'   => $mail->port,
                            'from'   => [
                                'address' => $mail->from_address,
                                'name'    => $mail->from_name,
                            ],
                            'encryption' => $mail->encryption,
                            'username'   => $mail->username,
                            'password'   => $mail->password,
                        ];
                        \Config::set('mail', array_merge($config, $configExtra));
                        (new \Illuminate\Mail\MailServiceProvider(app()))->register();
                    }
                }

                $file_paths = [];

                if ($request->hasFile('file')) {
                    foreach ($request->file('file') as $file) {
                        $filename = $file->getClientOriginalName();

                        $file->storeAs('documents', $filename, 'files');

                        $file_paths[] = "documents/$filename";
                    }
                }

                $cc = $bcc = [];
                if ($request->has('cc')) {
                    $cc = array_values(array_filter($request->cc));
                }
                if ($request->has('bcc')) {
                    $bcc = array_values(array_filter($request->bcc));
                }

                $emailClass = (new PurchaseEmail($request->subject, $request->message, $file_paths, ['from' => $fromEmail]))->build();

                $params = [
                    'model_id'        => $$request->id ?? null,
                    'from'            => $fromEmail,
                    'seen'            => 1,
                    'to'              => $data,
                    'subject'         => $request->subject,
                    'message'         => $emailClass->render(),
                    'template'        => 'simple',
                    'additional_data' => json_encode(['attachment' => $file_paths]),
                    'cc'              => $cc ?: null,
                    'bcc'             => $bcc ?: null,
                ];
                if ($request->object) {
                    if ($request->object == 'vendor') {
                        $params['model_type'] = 'Vendor::class';
                    } elseif ($request->object == 'user') {
                        $params['model_type'] = 'User::class';
                    } elseif ($request->object == 'supplier') {
                        $params['model_type'] = 'Supplier::class';
                    } elseif ($request->object == 'customer') {
                        $params['model_type'] = 'Customer::class';
                    } elseif ($request->object == 'order') {
                        $params['model_type'] = 'Order::class';
                    } elseif ($request->object == 'charity') {
                        $params['model_type'] = 'Charity::class';
                    }
                }

                $email = Email::create($params);

                \App\EmailLog::create(
                    [
                        'email_id'  => $email->id,
                        'email_log' => 'Email initiated',
                        'message'   => $email->to,
                    ]
                );

                \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
            }

            return redirect()->back()->withSuccess('You have successfully sent email!');
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return redirect()->back()->withErrors($msg);
        }
    }

    public function getMailTemplate(request $request)
    {
        if (isset($request->mailtemplateid)) {
            $data            = MailinglistTemplate::select('static_template', 'subject')->where('id', $request->mailtemplateid)->first();
            $static_template = $data->static_template;
            $subject         = $data->subject;
            if (! $static_template) {
                return response()->json(['error' => 'unable to get template', 'success' => false]);
            }

            return response()->json(['template' => $static_template, 'subject' => $subject, 'success' => true]);
        }

        return response()->json(['error' => 'unable to get template', 'success' => false]);
    }
}
