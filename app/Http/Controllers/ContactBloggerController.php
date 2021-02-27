<?php

namespace App\Http\Controllers;

use App\BloggerEmailTemplate;
use App\Contact;
use App\ContactBlogger;
use App\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactBloggerController extends Controller
{
    public function __construct()
    {
      //  $this->middleware('permission:blogger-all');
        $this->middleware(function ($request, $next) {
            session()->flash('active_tab','contact_tab');
            return $next($request);
        });
    }

    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required','email'=>'required|email','instagram_handle'=>'required']);
        $blogger_contact = new ContactBlogger($request->all());
        $blogger_contact->status = 'pending';
        $blogger_contact->save();

        $email_template = BloggerEmailTemplate::first();
        $subject = $request->get('email_subject') ?: optional($email_template)->subject;
        $message = $request->get('email_message') ?: optional($email_template)->message;
        Mail::to($request->user())->send(new \App\Mails\Manual\ContactBlogger($subject, $message));
        Email::create([
            'model_id'        => $blogger_contact->id,
            'model_type'      => ContactBlogger::class,
            'from'            => 'contact@sololuxury.co.in',
            'to'              => $blogger_contact->email,
            'send'            => 1,
            'subject'         => $subject,
            'message'         => $message,
            'template'				=> 'contact-blogger',
            'additional_data'	=> ''
        ]);
        return redirect()->back()->withSuccess('Information stored successfully along with push email to the blogger!');
    }

    public function update(ContactBlogger $contact_blogger, Request $request)
    {
        $this->validate($request, ['name' => 'required','email'=>'required|email','instagram_handle'=>'required','status','quote']);
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
