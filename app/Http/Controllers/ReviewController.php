<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\Setting;
use App\ReviewSchedule;
use App\Review;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
   		$this->middleware('permission:review-view');
   	}

    public function index(Request $request)
    {
      $filter_platform = $request->platform ?? '';
      $filter_posted_date = $request->posted_date ?? '';

      if ($request->platform != null) {
        $accounts = Account::where('platform', $request->platform)->latest()->paginate(Setting::get('pagination'));
        $review_schedules = ReviewSchedule::where('status', '!=', 'posted')->where('platform', $request->platform);
        $posted_reviews = ReviewSchedule::where('status', 'posted')->where('platform', $request->platform);
      } else {
        $accounts = Account::latest()->paginate(Setting::get('pagination'));
      }

      if ($request->posted_date != null) {
        if ($request->platform != null) {
          $review_schedules = $review_schedules->where('posted_date', $request->posted_date);
          $posted_reviews = $posted_reviews->where('posted_date', $request->posted_date);
        } else {
          $review_schedules = ReviewSchedule::where('status', '!=', 'posted')->where('posted_date', $request->posted_date);
          $posted_reviews = ReviewSchedule::where('status', 'posted')->where('posted_date', $request->posted_date);
        }
      }

      if ($request->platform == null && $request->posted_date == null) {
        $review_schedules = ReviewSchedule::where('status', '!=', 'posted');
        $posted_reviews = ReviewSchedule::where('status', 'posted');
      }

      $review_schedules = $review_schedules->latest()->paginate(Setting::get('pagination'), ['*'], 'review-page');
      $posted_reviews = $posted_reviews->latest()->paginate(Setting::get('pagination'), ['*'], 'posted-page');

      return view('reviews.index', [
        'accounts'            => $accounts,
        'review_schedules'    => $review_schedules,
        'posted_reviews'      => $posted_reviews,
        'filter_platform'     => $filter_platform,
        'filter_posted_date'  => $filter_posted_date
      ]);
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


      return redirect()->route('review.index')->withSuccess('You have successfully added an account!');
    }

    public function accountStore(Request $request)
    {
      $this->validate($request, [
        'first_name'      => 'sometimes|nullable|string',
        'last_name'       => 'sometimes|nullable|string',
        'email'           => 'sometimes|nullable|email',
        'password'        => 'required|min:3',
        'dob'             => 'sometimes|nullable|date',
        'platform'        => 'required|string',
        'followers_count' => 'sometimes|nullable|numeric',
        'posts_count'     => 'sometimes|nullable|numeric',
        'dp_count'        => 'sometimes|nullable|numeric'
      ]);

      $data = $request->except('_token');

      Account::create($data);

      return redirect()->route('review.index')->withSuccess('You have successfully added an account!');
    }

    public function scheduleStore(Request $request)
    {
      $this->validate($request, [
        'date'          => 'required|date',
        'platform'      => 'sometimes|nullable|string',
        'review_count'  => 'sometimes|nullable|numeric',
        'status'        => 'required|string',
      ]);

      $data = $request->except(['_token', 'review']);

      // dd($request->review[0]);

      // preg_match_all('/(#\w*)/', $request->review[0], $match);
      //
      // dd($match);

      $review_schedule = ReviewSchedule::create($data);

      foreach ($request->review as $review) {
        if ($review) {
          $new_review = new Review;
          $new_review->review_schedule_id = $review_schedule->id;
          $new_review->review = $review;
          $new_review->save();
        }
      }

      return redirect()->route('review.index')->withSuccess('You have successfully added a review schedule!');
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

    public function updateStatus(Request $request, $id)
    {
      $review = Review::find($id);
      $review->status = $request->status;
      $review->save();

      return response()->json(['status' => $request->status]);
    }

    public function accountUpdate(Request $request, $id)
    {
      $this->validate($request, [
        'first_name'      => 'sometimes|nullable|string',
        'last_name'       => 'sometimes|nullable|string',
        'email'           => 'sometimes|nullable|email',
        'password'        => 'required|min:3',
        'dob'             => 'sometimes|nullable|date',
        'platform'        => 'required|string',
        'followers_count' => 'sometimes|nullable|numeric',
        'posts_count'     => 'sometimes|nullable|numeric',
        'dp_count'        => 'sometimes|nullable|numeric'
      ]);

      $data = $request->except(['_token', '_method']);

      Account::find($id)->update($data);

      return redirect()->route('review.index')->withSuccess('You have successfully updated an account!');
    }

    public function scheduleUpdate(Request $request, $id)
    {
      $this->validate($request, [
        'account_id'    => 'sometimes|nullable|numeric',
        'date'          => 'required|date',
        'posted_date'   => 'sometimes|nullable|date',
        'platform'      => 'sometimes|nullable|string',
        'review_count'  => 'sometimes|nullable|numeric',
        'review_link'   => 'sometimes|nullable|string',
        'status'        => 'required|string',
      ]);

      $data = $request->except(['_token', '_method', 'review']);

      $review_schedule = ReviewSchedule::find($id);
      $review_schedule->update($data);

      foreach ($review_schedule->reviews as $review) {
        $review->delete();
      }

      foreach ($request->review as $review) {
        if ($review) {
          $new_review = new Review;
          $new_review->review_schedule_id = $review_schedule->id;
          $new_review->review = $review;
          $new_review->save();
        }
      }

      return redirect()->route('review.index')->withSuccess('You have successfully added a review schedule!');
    }

    public function scheduleUpdateStatus(Request $request, $id)
    {
      $review_schedule = ReviewSchedule::find($id);
      $review_schedule->status = $request->status;
      $review_schedule->save();

      return response('success');
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

    public function accountDestroy($id)
    {
      $account = Account::find($id);

      $account->delete();

      return redirect()->route('review.index')->withSuccess('You have successfully deleted an account!');
    }

    public function scheduleDestroy($id)
    {
      $schedule = ReviewSchedule::find($id);

      foreach ($schedule->reviews as $review) {
        $review->delete();
      }

      $schedule->delete();

      return redirect()->route('review.index')->withSuccess('You have successfully deleted scheduled review!');
    }
}
