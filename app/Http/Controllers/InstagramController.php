<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Image;
use App\ImageSchedule;
use App\Services\Instagram\Instagram;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Facebook\Facebook;

class InstagramController extends Controller
{
    private $instagram;
    private $facebook;

    public function __construct(Instagram $instagram, Facebook $facebook)
    {
        $this->instagram = $instagram;
        $this->facebook = $facebook;

    }

    public function index() {

    }


    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * This method gives the list of posts
     * that is in Instagram account
     */
    public function showPosts(Request $request) {
        $url = null;

        if ($request->has('next') && !empty($request->get('next'))) {
            $url = substr($request->get('next'), 32);
        } else if ($request->has('previous') && !empty($request->get('previous'))) {
            $url = substr($request->get('previous'), 32);
        }

        [$posts, $paging] = $this->instagram->getMedia($url);

        return view('instagram.index', compact(
            'posts',
            'paging'
        ));
    }

    /**
     * @param Request $request
     * This method will store photo to
     * Instagram Business account
     */
    public function store(Request $request) {
        $this->validate($request, [
           'image' => 'required|image'
        ]);
    }

    public function getComments(Request $request) {
        $this->validate($request, [
            'post_id' => 'required'
        ]);

        $comments = $this->instagram->getComments($request->get('post_id'));

        return response()->json($comments);
    }

    public function postComment(Request $request) {
        $this->validate($request, [
            'message' => 'required',
            'post_id' => 'required'
        ]);

        if ($request->has('comment_id') && !empty($request->get('comment_id'))) {
            $commentId = $request->get('comment_id');
            $comment = $this->instagram->postReply($commentId, $request->get('message'));
            return response()->json($comment);
        }

        $comment = $this->instagram->postComment($request->get('post_id'), $request->get('message'));
        return response()->json($comment);
    }

    public function showImagesToBePosted(Request $request, Image $images) {

        $selected_categories = 1;
        $selected_brands = [];
        $price = [0, 10000000];

        if ($request->has('category')) {
            $selected_categories = $request->get('category');
            $categories = Category::whereIn('id', $selected_categories)->with('childs')->get();
        }

        if ($request->has('price')) {
            $price = $request->get('price');
            $price = explode(',', $price);
            $images = $images->whereBetween('price', $price);
        }

        if ($request->has('brand')) {
            $selected_brands = $request->get('brand');
            $images = $images->whereIn('brand', $selected_brands);
        }

        $images = $images->orderBy('created_at', 'DESC')->paginate(25);

        $category_selection = Category::attr(['name' => 'category[]','class' => 'form-control'])
            ->selected($selected_categories)
            ->renderAsDropdown();


        $brands = Brand::all();

        return view('instagram.images_to_be_posted', compact('images', 'categories', 'brands', 'category_selection', 'selected_brands', 'price'));

    }

    public function postMedia(Request $request) {
        $this->validate($request, [
            'image_id' => 'required|exists:images,id',
        ]);

        if ($request->get('is_scheduled') === 'on') {
            $this->validate($request, [
                'date' => 'required|date',
                'hour' => 'required|numeric|min:0|max:23',
                'minute' => 'required|numeric|min:0|max:59',
            ]);

            $date = explode('-', $request->get('date'));
            $date = Carbon::create($date[0], $date[1], $date[2], $request->get('hour'), $request->get('minute'), 0);
            $date = $date->toDateTimeString();


            $schedule = new ImageSchedule();
            $schedule->image_id = $request->get('image_id');
            $schedule->facebook = ($request->get('facebook') === 'on') ? 1 : 0;
            $schedule->instagram = ($request->get('instagram') === 'on') ? 1 : 0;
            $schedule->description = $request->get('description');
            $schedule->scheduled_for = $date;
            $schedule->status = 0;
            $schedule->save();

            return response()->json([
                'status' => 'success',
                'post_status' => $schedule->status,
                'time' => $schedule->scheduled_for->diffForHumans(),
                'posted_to' => [
                    'facebook' => $schedule->facebook,
                    'instagram' => $schedule->instagram
                ],
                'message' => 'This post has been scheduled for post.'
            ]);

        }

        $image = Image::findOrFail($request->get('image_id'));

        $schedule = new ImageSchedule();
        $schedule->image_id = $request->get('image_id');
        $schedule->facebook = ($request->get('facebook') === 'on') ? 1 : 0;
        $schedule->instagram = ($request->get('instagram') === 'on') ? 1 : 0;
        $schedule->description = $request->get('description');
        $schedule->scheduled_for = date('Y-m-d');
        $schedule->status = 0;
        $schedule->save();

        if ($request->get('facebook') === 'on') {
            $this->facebook->postMedia($image);
            ImageSchedule::whereIn('image_id', $this->facebook->getImageIds())->update([
                'status' => 1
            ]);
        }

        if ($request->get('instagram') === 'on') {
            $this->instagram->postMedia($image);
            ImageSchedule::whereIn('image_id', $this->instagram->getImageIds())->update([
                'status' => 1
            ]);
        }

        return response()->json([
            'status' => 'success',
            'post_status' => $schedule->status,
            'time' => $schedule->scheduled_for->diffForHumans(),
            'message' => 'This post has been scheduled for post.'
        ]);

    }

    public function postMediaNow($image)
    {
        $image = Image::findOrFail($image);

        $this->facebook->postMedia($image);
        ImageSchedule::whereIn('image_id', $this->facebook->getImageIds())->update([
            'status' => 1,
            'scheduled_for' => date('Y-m-d')
        ]);

        return response()->json([
            'status' => 'success',
            'post_status' => $image->schedule->status,
            'time' => $image->schedule->scheduled_for->diffForHumans(),
            'message' => 'This post has been posted successfully!.'
        ]);
    }
}
