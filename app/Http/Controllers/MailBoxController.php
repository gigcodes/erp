<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;
use App\Email;
use Carbon\Carbon;
use App\CronJobReport;
use App\Models\EmailBox;
use App\DigitalMarketingPlatform;

class MailBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $email = null)
    {
        $user = Auth::user();
        $admin = $user->isAdmin();
        $usernames = [];
        if (! $admin) {
            $emaildetails = \App\EmailAssign::select('id', 'email_address_id')->with('emailAddress')->where(['user_id' => $user->id])->get();
            if ($emaildetails) {
                foreach ($emaildetails as $_email) {
                    $usernames[] = $_email->emailAddress->username;
                }
            }
        }

        // Set default type as incoming
        $type = 'incoming';
        $seen = '0';
        $from = ''; //Purpose : Add var -  DEVTASK-18283

        $term = $request->term ?? '';
        $sender = $request->sender ?? '';
        $receiver = $request->receiver ?? '';
        $status = $request->status ?? '';
        $category = $request->category ?? '';
        $mailbox = $request->mail_box ?? '';
        $email_model_type = $request->email_model_type?? '';
        $email_box_id = $request->email_box_id ?? '';

        $date = $request->date ?? '';
        $type = $request->type ?? $type;
        $seen = $request->seen ?? $seen;
        $query = (new Email())->newQuery();
        $trash_query = false;

        if (count($usernames) > 0) {
            $query = $query->where(function ($query) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $query->orWhere('from', 'like', '%'.$_uname.'%');
                }
            });

            $query = $query->orWhere(function ($query) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $query->orWhere('to', 'like', '%'.$_uname.'%');
                }
            });
        }

        if(empty($category)){
            $query = $query->whereHas('category', function($q){
                $q->whereIn('priority', ['HIGH', 'UNDEFINED']);
            })
            ->orWhere('email_category_id', '<=', 0);
        }

        //START - Purpose : Add Email - DEVTASK-18283
        if ($email != '' && $receiver == '') {
            $receiver = $email;
            $from = 'order_data';
            $seen = 'both';
            $type = 'outgoing';
        }
        //END - DEVTASK-18283

        // If type is bin, check for status only
        if ($type == 'bin') {
            $trash_query = true;
            $query = $query->where('status', 'bin');
        } elseif ($type == 'draft') {
            $query = $query->where('is_draft', 1)->where('status', '<>', 'pre-send');
        } elseif ($type == 'pre-send') {
            $query = $query->where('status', 'pre-send');
        } 
        else if(!empty($request->type)){
            $query = $query->where(function ($query) use ($type) {
                $query->where('type', $type)->where('status', '<>', 'bin')->where('is_draft', '<>', 1)->where('status', '<>', 'pre-send');
            });
        }
        else {
            $query = $query->where(function ($query) use ($type) {
                $query->where('type', $type)->orWhere('type', 'open')->orWhere('type', 'delivered')->orWhere('type', 'processed');
            })->where('status', '<>', 'bin')->where('is_draft', '<>', 1)->where('status', '<>', 'pre-send');;
        }
        if ($email_model_type)
        {
            $model_type = explode(',', $email_model_type);
                $query = $query->where(function ($query) use ($model_type) {
                    $query->whereIn('model_type', $model_type);
                });
        }
        if ($date) {
            $query = $query->whereDate('created_at', $date);
        }
        if ($term) {
            $query = $query->where(function ($query) use ($term) {
                $query->where('from', 'like', '%'.$term.'%')
                    ->orWhere('to', 'like', '%'.$term.'%')
                    ->orWhere('subject', 'like', '%'.$term.'%')
                    ->orWhere('message', 'like', '%'.$term.'%');
            });
        }

        if (! $term) {
            if ($sender) {
                $sender = explode(',', $request->sender);
                $query = $query->where(function ($query) use ($sender) {
                    $query->whereIn('from', $sender);
                });
            }
            if ($receiver) {
                $receiver = explode(',', $request->receiver);
                $query = $query->where(function ($query) use ($receiver) {
                    $query->whereIn('to', $receiver);
                });
            }
            if ($status) {
                $status = explode(',', $request->status);
                $query = $query->where(function ($query) use ($status) {
                    $query->whereIn('status', $status);
                });
            }
            if ($category) {
                $category = explode(',', $request->category);
                $query = $query->where(function ($query) use ($category) {
                    $query->whereIn('email_category_id', $category);
                });
            }
        }
        
        if (! empty($mailbox)) {
            $mailbox = explode(',', $request->mail_box);
            $query = $query->where(function ($query) use ($mailbox) {
                $query->orWhere('to', $mailbox);
            });
        }

        if (isset($seen)) {
            if ($seen != 'both') {
                $query = $query->where('seen', $seen);
            }
        }

        // If it isn't trash query remove email with status trashed
        if (! $trash_query) {
            $query = $query->where(function ($query) use ($type) {
                $isDraft = ($type == 'draft') ? 1 : 0;
                return $query->where('status', '<>', 'bin')->orWhereNull('status')->where('is_draft', $isDraft);
            });
        }

        if($email_box_id){
            $emailBoxIds = explode(',', $email_box_id);

            $query =  $query->where(function ($query) use ($emailBoxIds) {
                $query->whereIn('email_box_id', $emailBoxIds);
            });
        }else{
            $query =  $query->where(function ($query) use ($email_box_id) {
                $query->whereNotNull('email_box_id');
            });
        }

        if ($admin == 1) {
            $query = $query->orderByDesc('created_at');
            $emails = $query->paginate(30)->appends(request()->except(['page']));
        } else {
            if (count($usernames) > 0) {
                $query = $query->where(function ($query) use ($usernames) {
                    foreach ($usernames as $_uname) {
                        $query->orWhere('from', 'like', '%'.$_uname.'%');
                    }
                });

                $query = $query->orWhere(function ($query) use ($usernames) {
                    foreach ($usernames as $_uname) {
                        $query->orWhere('to', 'like', '%'.$_uname.'%');
                    }
                });

                $query = $query->orderByDesc('created_at');
                $emails = $query->paginate(30)->appends(request()->except(['page']));
            } else {
                $emails = (new Email())->newQuery();
                $emails = $emails->whereNull('id');
                $emails = $emails->paginate(30)->appends(request()->except(['page']));
            }
        }

        //Get Cron Email Histroy
        $reports = CronJobReport::where('cron_job_reports.signature', 'fetch:all_emails')
            ->join('cron_jobs', 'cron_job_reports.signature', 'cron_jobs.signature')
            ->whereDate('cron_job_reports.created_at', '>=', Carbon::now()->subDays(10))
            ->select(['cron_job_reports.*', 'cron_jobs.last_error'])->paginate(15);

        //Get All Status
        $email_status = DB::table('email_status')->get();

        //Get List of model types
        $emailModelTypes = Email::emailModelTypeList();
        
        //Get All Category
        $email_categories = DB::table('email_category')->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('emails.search', compact('emails', 'date', 'term', 'type', 'email_categories', 'email_status', 'emailModelTypes'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $emails->links(),
                'count' => $emails->total(),
                'emails' => $emails,
            ], 200);
        }

        // suggested search for email forwarding
        $search_suggestions = $this->getAllEmails();

        // dd(array_values($search_suggestions));

        // if($request->AJAX()) {
        //     return view('emails.search',compact('emails'));
        // }

        // dont load any data, data will be loaded by tabs based on ajax
        // return view('emails.index',compact('emails','date','term','type'))->with('i', ($request->input('page', 1) - 1) * 5);
        $digita_platfirms = DigitalMarketingPlatform::all();
        
        $totalEmail = Email::whereNotNull('email_box_id')->count();

        $emailBoxes = EmailBox::select('id', 'box_name')->get();

        return view('mailbox.index', ['emails' => $emails, 'type' => 'email', 'search_suggestions' => $search_suggestions, 'email_status' => $email_status, 'email_categories' => $email_categories, 'emailModelTypes' => $emailModelTypes, 'reports' => $reports, 'digita_platfirms' => $digita_platfirms, 'receiver' => $receiver, 'from' => $from, 'totalEmail' => $totalEmail, 'emailBoxes' => $emailBoxes])->with('i', ($request->input('page', 1) - 1) * 5);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getAllEmails()
    {
        $available_models = ['supplier' => \App\Supplier::class, 'vendor' => \App\Vendor::class,
            'customer' => \App\Customer::class, 'users' => \App\User::class, ];
        $email_list = [];
        foreach ($available_models as $key => $value) {
            $email_list = array_merge($email_list, $value::whereNotNull('email')->pluck('email')->unique()->all());
        }
        // dd($email_list);
        return array_values(array_unique($email_list));
    }
}
