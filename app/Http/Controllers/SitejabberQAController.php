<?php

namespace App\Http\Controllers;

use App\Account;
use App\ActivitiesRoutines;
use App\BrandReviews;
use App\NegativeReviews;
use App\QuickReply;
use App\Review;
use App\Setting;
use App\SitejabberQA;
use GuzzleHttp\Client;
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
        $question->is_approved = 1;
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
        $negativeReviews = NegativeReviews::all();
        $accounts = Account::where('platform', 'sitejabber')->orderBy('updated_at', 'DESC')->get();
        $brandReviews = BrandReviews::where('used', 0)->take(100)->get();
        $accountsRemaining = Account::whereDoesntHave('reviews')->where('platform', 'sitejabber')->count();
        $remainingReviews = Review::whereHas('account')->whereNotIn('status', ['posted', 'posted_one'])->count();
        $totalAccounts = $accounts->count();
        $sjs = SitejabberQA::where('type', 'question')->get();
        $setting = ActivitiesRoutines::where('action', 'sitejabber_review')->first();
        $quickReplies = QuickReply::all();
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

        return view('sitejabber.accounts', compact('accounts', 'sjs', 'setting', 'setting2', 'setting3', 'accountsRemaining', 'totalAccounts', 'remainingReviews', 'brandReviews', 'negativeReviews', 'quickReplies'));
    }

    public function reviews() {
        $reviews = Review::where('platform', 'sitejabber')->get();

        return view('sitejabber.reviews', compact('reviews'));
    }

    public function attachBrandReviews($id) {
        $reviewx = BrandReviews::findOrFail($id);
        $account = Account::whereDoesntHave('reviews')->where('platform', 'sitejabber')->orderBy('created_at', 'DESC')->first();

        $review = new Review();
        $review->account_id = $account->id;
        $review->review = $reviewx->body;
        $review->platform = 'sitejabber';
        $review->title = $reviewx->title;
        $review->save();

        $reviewx->used = 1;
        $reviewx->save();
        $account->touch();

        return redirect()->back()->with('message', 'Attached to a customer!');

    }

    public function detachBrandReviews($id) {
        $reviewx = BrandReviews::findOrFail($id);

        $reviewx->delete();

        return redirect()->back()->with('message', 'Attached to a customer!');

    }

    public function confirmReviewAsPosted($id) {
        Review::where('id', $id)->update([
            'status' => 'posted'
        ]);

        return redirect()->back();
    }

    public function sendSitejabberQAReply(Request $request, Client $client) {
        $id = $request->get('rid');
        $negativeReview = NegativeReviews::where('id', $id)->first();
        if (!$negativeReview) {
            return response()->json([
                'status' => 'success'
            ]);
        }

        $comment = $request->get('comment');
        $reply = $request->get('reply');
//        dd($comment, $reply);

        $negativeReview->reply = $reply;
        $negativeReview->save();


        $response = $client->post('http://144.202.53.198/postReply', [
            'form_params' => [
                'comment' => $comment,
                'reply' => $reply
            ],
        ]);

        $data = $response->getBody()->getContents();

        return response()->json([
            'status' => 'success'
        ]);
    }
}
