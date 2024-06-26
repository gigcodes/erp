<?php

namespace App\Http\Controllers;

use App\Email;
use App\ContactBlogger;
use Illuminate\Http\Request;
use App\BloggerEmailTemplate;

class ContactBloggerController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            session()->flash('active_tab', 'contact_tab');

            return $next($request);
        });
    }

    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required', 'email' => 'required|email', 'instagram_handle' => 'required']);
        $blogger_contact         = new ContactBlogger($request->all());
        $blogger_contact->status = 'pending';
        $blogger_contact->save();

        $email_template = BloggerEmailTemplate::first();
        $subject        = $request->get('email_subject') ?: optional($email_template)->subject;
        $message        = $request->get('email_message') ?: optional($email_template)->message;
        $from_email     = \App\Helpers::getFromEmail();

        $emailClass = (new \App\Mails\Manual\ContactBlogger($subject, $message, $from_email))->build();

        $email = Email::create([
            'model_id'         => $blogger_contact->id,
            'model_type'       => ContactBlogger::class,
            'from'             => $from_email,
            'to'               => $blogger_contact->email,
            'subject'          => $emailClass->subject,
            'message'          => $emailClass->render(),
            'template'         => 'contact-blogger',
            'additional_data'  => '',
            'status'           => 'pre-send',
            'store_website_id' => null,
            'is_draft'         => 0,
        ]);
        \App\EmailLog::create([
            'email_id'  => $email->id,
            'email_log' => 'Email initiated',
            'message'   => $email->to,
        ]);
        \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');

        return redirect()->back()->withSuccess('Information stored successfully along with push email to the blogger!');
    }

    public function update(ContactBlogger $contact_blogger, Request $request)
    {
        $this->validate($request, ['name' => 'required', 'email' => 'required|email', 'instagram_handle' => 'required', 'status', 'quote']);
        $contact_blogger->fill($request->all());
        $contact_blogger->save();

        return redirect()->back()->withSuccess('Information updated successfully!');
    }

    public function destroy(ContactBlogger $contact_blogger)
    {
        $contact_blogger->delete();

        return redirect()->back()->withSuccess('Information deleted successfully!');
    }
}
