<?php

namespace App\Http\Controllers;

use App\Account;
use App\ActivitiesRoutines;
use App\Review;
use App\Setting;
use App\SitejabberQA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SitejabberQAController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sjs = SitejabberQA::where('type', 'question')->get();

        return view('sitejabber.index', compact('sjs'));

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
        $this->validate($request, [
            'question' => 'required'
        ]);


        $question = new SitejabberQA();
        $question->status = 0;
        $question->text = $request->get('question');
        $question->type = 'question';
        $question->save();

        return redirect()->back()->with('message', 'Question added successfully. Note: This will be posted within 24 hours.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SitejabberQA  $sitejabberQA
     * @return \Illuminate\Http\Response
     */
    public function show(SitejabberQA $sitejabberQA)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SitejabberQA  $sitejabberQA
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $this->validate($request, [
            'range' => 'required',
            'range2' => 'required',
            'range3' => 'required',
        ]);

        $setting = ActivitiesRoutines::where('action', 'sitejabber_review')->first();
        if (!$setting) {
            $setting = new ActivitiesRoutines();
        }
        $setting->action = 'sitejabber_review';
        $setting->times_a_day = $request->get('range');
        $setting->save();
        $setting2 = ActivitiesRoutines::where('action', 'sitejabber_account_creation')->first();
        if (!$setting2) {
            $setting2 = new ActivitiesRoutines();
        }
        $setting2->action = 'sitejabber_account_creation';
        $setting2->times_a_day = $request->get('range2');
        $setting2->save();


        $setting3 = ActivitiesRoutines::where('action', 'sitejabber_qa_post')->first();
        if (!$setting3) {
            $setting3 = new ActivitiesRoutines();
        }
        $setting3->action = 'sitejabber_qa_post';
        $setting3->times_a_week = $request->get('range3');
        $setting3->save();


        return redirect()->back()->with('message', 'Sitejabber review settings updated!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SitejabberQA  $sitejabberQA
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        $sj = SitejabberQA::findOrFail($id);

        $sju = new SitejabberQA();
        $sju->parent_id = $id;
        $sju->url = $sj->url;
        $sju->text = $request->get('reply');
        $sju->type = 'reply';
        $sju->author = 'TBD';
        $sju->status = 0;
        $sju->save();

        return redirect()->back()->with('message', 'Comment added successfully! And will be posted anytime within 24 hours!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SitejabberQA  $sitejabberQA
     * @return \Illuminate\Http\Response
     */
    public function destroy(SitejabberQA $sitejabberQA)
    {
        //
    }

    public function accounts() {
        $accounts = Account::where('platform', 'sitejabber')->orderBy('created_at', 'DESC')->get();
        $accountsRemaining = Account::whereDoesntHave('reviews')->where('platform', 'sitejabber')->count();
        $remainingReviews = Review::whereHas('account')->whereNotIn('status', ['posted', 'posted_one'])->count();
        $totalAccounts = $accounts->count();
        $sjs = SitejabberQA::where('type', 'question')->get();
        $setting = ActivitiesRoutines::where('action', 'sitejabber_review')->first();
        if (!$setting) {
            $setting = new ActivitiesRoutines();
            $setting->action = 'sitejabber_review';
            $setting->times_a_day = 5;
            $setting->save();
        }
        $setting2 = ActivitiesRoutines::where('action', 'sitejabber_account_creation')->first();
        if (!$setting2) {
            $setting2 = new ActivitiesRoutines();
            $setting2->action = 'sitejabber_account_creation';
            $setting2->times_a_day = 5;
            $setting2->save();
        }

        $setting3 = ActivitiesRoutines::where('action', 'sitejabber_qa_post')->first();
        if (!$setting3) {
            $setting3 = new ActivitiesRoutines();
            $setting3->action = 'sitejabber_qa_post';
            $setting3->times_a_week = 1;
            $setting3->save();
        }

        return view('sitejabber.accounts', compact('accounts', 'sjs', 'setting', 'setting2', 'setting3', 'accountsRemaining', 'totalAccounts', 'remainingReviews'));
    }

    public function reviews() {
        $reviews = Review::where('platform', 'sitejabber')->get();

        return view('sitejabber.reviews', compact('reviews'));
    }
}
