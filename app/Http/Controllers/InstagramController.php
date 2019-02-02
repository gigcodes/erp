<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Customer;
use App\Image;
use App\ImageSchedule;
use App\ScheduleGroup;
use App\Services\Instagram\DirectMessage;
use App\Services\Instagram\Instagram;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Facebook\Facebook;

class InstagramController extends Controller
{
    private $instagram;
    private $facebook;
    private $messages;

//    public function __construct(Instagram $instagram, Facebook $facebook, DirectMessage $messages)
    public function __construct(Instagram $instagram, Facebook $facebook)
    {
        $this->instagram = $instagram;
        $this->facebook = $facebook;
//        $this->messages = $messages;

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

    public function showImagesToBePosted(Request $request) {

        $images = Image::where('status', 2);

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

            $scheduleGroup = new ScheduleGroup();
            $scheduleGroup->images = [$request->get('image_id')];
            $scheduleGroup->scheduled_for = $date;
            $scheduleGroup->description = $request->get('description');
            $scheduleGroup->save();

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

    public function postMediaNow($schedule)
    {
        $schedule = ScheduleGroup::findOrFail($schedule);
        $images = $schedule->images->get()->all();


        if ($images[0]->schedule->facebook) {
            $this->facebook->postMedia($images, $schedule->description);
            ImageSchedule::whereIn('image_id', $this->facebook->getImageIds())->update([
                'status' => 1,
                'scheduled_for' => date('Y-m-d h:i:00')
            ]);
        }
        if ($images[0]->schedule->instagram) {
            $this->instagram->postMedia($images);
            ImageSchedule::whereIn('image_id', $this->instagram->getImageIds())->update([
                'status' => 1,
                'scheduled_for' => date('Y-m-d h:i:00')
            ]);
        }

        $schedule->status = 1;
        $schedule->scheduled_for = date('Y-m-d h:i:00');
        $schedule->save();

        return response()->json([
            'status' => 'success',
            'post_status' => $schedule->status,
            'time' => $schedule->scheduled_for->diffForHumans(),
            'message' => 'This schedule has been posted successfully!.'
        ]);
    }

    public function showSchedules(Request $request) {
        $imagesWithoutSchedules = Image::whereDoesntHave('schedule')->where('status', 2)->orderBy('created_at', 'DESC')->get();
        $imagesWithSchedules = ScheduleGroup::where('status', 0)->get();
        $postedImages = Image::whereHas('schedule', function($query) {
            $query->where('status', 1);
        })->orderBy('created_at', 'DESC')->get();

        return view('instagram.schedules', compact('imagesWithoutSchedules', 'imagesWithSchedules', 'postedImages'));

    }

    public function getScheduledEvents() {
        $imagesWithSchedules = ScheduleGroup::where('status', 0)->get()->toArray();
        $imagesWithSchedules = array_map(function($item) {
            return [
                'title' => substr($item['description'], 0, 500).'...',
                'start' => $item['scheduled_for'],
                'image_names' => array_map(function ($img) {
                    return [
                        'id' => $img['id'],
                        'name' => $img['filename'] ? asset('uploads/social-media') . '/' . $img['filename'] : 'http://lorempixel.com/555/300/black',
                    ];
                }, $item['images']->get(['id', 'filename'])->toArray())
            ];
        }, $imagesWithSchedules);

        return response()->json($imagesWithSchedules);
    }

    public function postSchedules(Request $request) {
        $this->validate($request, [
            'images' => 'required',
            'description' => 'required',
            'date' => 'required|date',
            'hour' => 'required|numeric|min:0|max:23',
            'minute' => 'required|numeric|min:0|max:59',
        ]);


        $images = $request->get('images');
        $descriptions = $request->get('description');
        $date = explode('-', $request->get('date'));
        $date = Carbon::create($date[0], $date[1], $date[2], $request->get('hour'), $request->get('minute'), 0);
        $date = $date->toDateTimeString();

        foreach ($images as $image) {
            $schedule = new ImageSchedule();
            $schedule->image_id = $image;
            $schedule->facebook = ($request->get('facebook') === 'on') ? 1 : 0;
            $schedule->instagram = ($request->get('instagram') === 'on') ? 1 : 0;
            $schedule->description = $descriptions[$image] ?? '';
            $schedule->scheduled_for = $date;
            $schedule->status = 0;
            $schedule->save();
        }

        $scheduleGroup = new ScheduleGroup();
        $scheduleGroup->images = $images;
        $scheduleGroup->description = $request->get('caption') ?? '';
        $scheduleGroup->scheduled_for = $date;
        $scheduleGroup->save();

        return redirect()->back()->with('message', 'The images has been successfully scheduled for post!');

    }

    public function cancelSchedule($schedule)
    {
        $schedule = ScheduleGroup::findOrFail($schedule);

        $images = $schedule->images->get();
        foreach ($images as $image) {
            $image->schedule()->delete();
        }

        $schedule->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'This schedule has been deleted successfully!.'
        ]);

    }

//    public function getThread($thread) {
//        $thread = $this->messages->getThread($thread)->asArray();
//        $thread = $thread['thread'];
//        $currentUserId = $this->messages->getCurrentUserId();
//        $threadJson['messages'] = array_map(function($item) use ($currentUserId) {
//            $text = '';
//            if ($item['item_type'] == 'text') {
//                $text = $item['text'];
//            } else if ($item['item_type'] == 'like') {
//                $text = $item['like'];
//            } else if ($item['item_type'] == 'media') {
//                $text = $item['media']['image_versions2']['candidates'][0]['url'];
//            }
//            return [
//                'id' => $item['item_id'],
//                'text' => $text,
//                'item_type' => $item['item_type'],
//                'type' => ($item['user_id']===$currentUserId) ? 'sent' : 'received'
//            ];
//        }, $thread['items']);
//
//        $threadJson['profile_picture'] = $thread['users'][0]['profile_pic_url'];
//        $threadJson['username'] = $thread['users'][0]['username'];
//        $threadJson['name'] = $thread['users'][0]['full_name'];
//
//        return response()->json($threadJson);
//    }
//
//    public function replyToThread($thread, Request $request)
//    {
//        if ($request->hasFile('photo')) {
//            $file = $request->file('photo');
//            $this->messages->sendImage(['thread' => $thread], $file);
//        }
//        $this->messages->sendMessage(['thread' => $thread], $request->get('message'));
//        return $this->getThread($thread);
//    }
}
