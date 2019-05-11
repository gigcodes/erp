<?php

namespace App\Http\Controllers;

use App\File;
use App\HashtagPostComment;
use App\Product;
use Illuminate\Http\Request;
use App\Account;
use App\Setting;
use App\ReviewSchedule;
use App\Review;
use App\Complaint;
use App\Instruction;
use App\Customer;
use App\StatusChange;
use App\Helpers;
use App\User;
use Auth;
use InstagramAPI\Instagram;

Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
//   		$this->middleware('permission:review-view');
   	}

    public function index(Request $request)
    {
      $filter_platform = $request->platform ?? '';
      $filter_posted_date = $request->posted_date ?? '';
      $users_array = Helpers::getUserArray(User::all());

      // $revs = Review::all();
      //
      // foreach ($revs as $rev) {
      //   // $rev->is_approved = $rev->status;
      //   // $rev->account_id = $rev->account_id;
      //   // $rev->customer_id = $rev->customer_id;
      //   $rev->status = $rev->review_schedule->status;
      //   $rev->save();
      // }

      if ($request->platform != null) {
        $accounts = Account::where('platform', $request->platform)->latest()->paginate(Setting::get('pagination'));
        // $review_schedules = ReviewSchedule::where('status', '!=', 'posted')->where('platform', $request->platform);
        $review_schedules = Review::with('review_schedule')->where('status', '!=', 'posted')->where('platform', $request->platform);
        // $posted_reviews = ReviewSchedule::where('status', 'posted')->where('platform', $request->platform);
        $posted_reviews = Review::with('review_schedule')->where('status', 'posted')->where('platform', $request->platform);

        $complaints = Complaint::where('platform', $request->platform);
      } else {
        $accounts = Account::latest()->paginate(Setting::get('pagination'));
      }

      if ($request->posted_date != null) {
        if ($request->platform != null) {
          $review_schedules = $review_schedules->where('posted_date', $request->posted_date);
          $posted_reviews = $posted_reviews->where('posted_date', $request->posted_date);
          $complaints = $complaints->where('date', $request->posted_date);
        } else {
          $review_schedules = Review::with('review_schedule')->where('status', '!=', 'posted')->where('posted_date', $request->posted_date);
          // $review_schedules = ReviewSchedule::where('status', '!=', 'posted')->where('posted_date', $request->posted_date);
          // $posted_reviews = ReviewSchedule::where('status', 'posted')->where('posted_date', $request->posted_date);
          $posted_reviews = Review::with('review_schedule')->where('status', 'posted')->where('posted_date', $request->posted_date);
          $complaints = Complaint::where('date', $request->posted_date);
        }
      }

      if ($request->platform == null && $request->posted_date == null) {
        $review_schedules = Review::where('status', '!=', 'posted');
        // $review_schedules = ReviewSchedule::where('status', '!=', 'posted');
        $posted_reviews = Review::with('review_schedule')->where('status', 'posted');
        $complaints = (new Complaint)->newQuery();
      }

      // $review_schedules = $review_schedules->orWhere(function ($query) {
      //   return $query->where('status', 'posted')->whereHas('Reviews', function ($q) {
      //     return $q->where('is_approved', 0)->orWhere('is_approved', 2);
      //   });
      // })
      $review_schedules = $review_schedules->latest()->paginate(Setting::get('pagination'), ['*'], 'review-page');

      $posted_reviews = $posted_reviews->latest()->paginate(Setting::get('pagination'), ['*'], 'posted-page');
      $complaints = $complaints->where('thread_type', 'thread')->latest()->paginate(Setting::get('pagination'), ['*'], 'complaints-page');

      $customers = Customer::select(['id', 'name', 'email', 'instahandler', 'phone'])->get();
      $accounts_array = Account::select(['id', 'first_name', 'last_name', 'email'])->get();

        $instagram_dm_reviews = Review::where('platform', 'instagram_dm')->get();

      return view('reviews.index', [
        'accounts'            => $accounts,
        'customers'           => $customers,
        'review_schedules'    => $review_schedules,
        'posted_reviews'      => $posted_reviews,
        'complaints'      => $complaints,
        'filter_platform'     => $filter_platform,
        'filter_posted_date'  => $filter_posted_date,
        'users_array'  => $users_array,
        'accounts_array'  => $accounts_array,
          'instagram_dm_reviews' => $instagram_dm_reviews
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
          $new_review->posted_date = $request->date;
          $new_review->platform = $request->platform;
          $new_review->status = $request->status;
          $new_review->save();
        }
      }

      Instruction::create([
        'customer_id'   => '841',
        'instruction'   => 'Approve Reviews',
        'assigned_from' => Auth::id(),
        'assigned_to'   => 6
      ]);

      return redirect()->route('review.index')->withSuccess('You have successfully added a review schedule!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $account = Account::findOrFail($id);
        return view('reviews.show', compact('account'));
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
      $this->validate($request, [
        'review'        => 'required|string',
        'posted_date'   => 'sometimes|nullable|date',
        'review_link'   => 'sometimes|nullable|string',
        'serial_number' => 'sometimes|nullable|string',
        'platform'      => 'sometimes|nullable|string',
        'account_id'    => 'sometimes|nullable|numeric',
        'customer_id'   => 'sometimes|nullable|numeric'
      ]);

      $review = Review::find($id);

      $data = $request->except(['_token', '_method']);

      $review->update($data);

      return redirect()->route('review.index')->withSuccess('You have successfully updated the review!');
    }

    public function updateStatus(Request $request, $id)
    {
      $review = Review::find($id);
      $review->is_approved = $request->is_approved;
      $review->save();

      return response()->json(['status' => $request->is_approved]);
    }

    public function updateReview(Request $request, $id)
    {
      $review = Review::find($id);
      $review->review = $request->review;
      $review->save();

      return response('success');
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
        'customer_id'   => 'sometimes|nullable|numeric',
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

          if ($review_schedule->status == 'posted') {
            $new_review->status = 'posted';
            $new_review->is_approved = 1;
          }

          $new_review->save();
        }
      }

      return redirect()->route('review.index')->withSuccess('You have successfully added a review schedule!');
    }

    public function scheduleUpdateStatus(Request $request, $id)
    {
      // $review_schedule = ReviewSchedule::find($id);
      // $review_schedule->status = $request->status;
      // $review_schedule->save();
      //
      // foreach ($review_schedule->reviews as $review) {
      //   if ($review_schedule->status == 'posted' && $review->is_approved == 1) {
      //     $review->status = 'posted';
      //     $review->save();
      //   }
      // }

      $review = Review::find($id);

      StatusChange::create([
        'model_id'    => $review->id,
        'model_type'  => Review::class,
        'user_id'     => Auth::id(),
        'from_status' => $review->status,
        'to_status'   => $request->status
      ]);

      $review->status = $request->status;
      $review->save();

      // foreach ($review_schedule->reviews as $review) {
      //   if ($review_schedule->status == 'posted' && $review->is_approved == 1) {
      //     $review->status = 'posted';
      //     $review->save();
      //   }
      // }

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
      $review = Review::find($id);

      $review->delete();

      return redirect()->route('review.index')->withSuccess('You have successfully deleted a review!');
    }

    public function accountDestroy($id)
    {
      $account = Account::find($id);

      $account->delete();

      return redirect()->route('review.index')->withSuccess('You have successfully deleted an account!');
    }

    public function scheduleDestroy($id)
    {
      // $schedule = ReviewSchedule::find($id);
      //
      // foreach ($schedule->reviews as $review) {
      //   $review->delete();
      // }
      //
      // $schedule->delete();
      //
      // return redirect()->route('review.index')->withSuccess('You have successfully deleted scheduled review!');

      $review = Review::find($id);

      $review->delete();

      return redirect()->route('review.index')->withSuccess('You have successfully deleted a review!');
    }


    public function createFromInstagramHashtag(Request $request) {

        $this->validate($request, [
            'post' => 'required',
            'comment' => 'required',
            'poster' => 'required',
            'commenter' => 'required',
            'media_id' => 'required',
            'date' => 'required',
            'code' => 'required'
        ]);

        $review =  new Complaint();
        $review->customer_id = null;
        $review->platform = 'instagram';
        $review->complaint = '<strong>@'.$request->get('poster'). ' => ' .$request->get('post').'</strong><li>@'.$request->get('commenter').' => '.$request->get('comment').'</li>';
        $review->link = 'https://instagram.com/p/' . $request->get('code');
        $review->status = 'pending';
        $review->plan_of_action = 'instagram_reply';
        $review->where = 'INSTAGRAM_HASHTAG';
        $review->username = $request->get('poster');
        $review->name = $request->get('poster');
        $review->thread_type = 'thread';
        $review->date = $request->get('date');
        $review->media_id = $request->get('media_id');
        $review->receipt_username = $request->get('commenter');
        $review->save();

        return redirect()->back()->with('message', 'Comment sent for review');

    }

    public function replyToPost(Request $request) {

        $this->validate($request, [
            'media_id' => 'required',
            'id' => 'required',
            'username' => 'required',
            'message' => 'required',
        ]);

        $account = Account::find($request->get('id'));

        $instagram = new Instagram();
        $instagram->login($account->last_name, $account->password);

        $mediaId = $request->get('media_id');
        $message = $request->get('message');
        $username = $request->get('username');
        $message = "@$username $message";
        $instagram->media->comment($mediaId, $message);

        return redirect()->back()->with('message', "Replied sent to @$username by @".$account->last_name);
    }

    public function sendDm(Request $request) {

        $this->validate($request, [
            'product_id' => 'required',
            'id' => 'required',
            'username' => 'required',
            'message' => 'required',
        ]);

        $account = Account::find($request->get('id'));

        $instagram = new Instagram();
        $instagram->login($account->last_name, $account->password);

        $message = $request->get('message');
        $username = $request->get('username');

        $product = Product::findOrFail($request->get('product_id'));


        $id = $instagram->people->getUserIdForName($username);

        $file = $product->imageurl;

        $file = explode('/', $file);
        $file = $file[count($file)-1];

        $file = 'uploads/'.$file;

        $instagram->direct->sendText([
            'users' => [$id]
        ], $message);
        $instagram->direct->sendPhoto([
            'users' => [$id]
        ], $file);

        return redirect()->back()->with('message', "Message sent to @$username by @".$account->last_name);
    }

}
