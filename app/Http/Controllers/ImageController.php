<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Images;
use Image;
use Storage;
use Auth;
use Carbon\Carbon;
use App\Setting;
use App\Tag;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ImageController extends Controller
{

  public function __construct() {

		$this->middleware('permission:social-create', ['except' => ['approveImage']]);
		// $this->middleware( 'permission:social-manage', [ 'only' => [ 'create', 'store' ] ] );
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if (!isset($request->sortby) || $request->sortby == 'asc') {
        $images = Images::where('status', '1')->paginate(Setting::get('pagination'));
      } else {
        $images = Images::where('status', '1')->latest()->paginate(Setting::get('pagination'));
      }

      return view('images.index')->withImages($images);
    }

    public function approved()
    {
      $images = Images::whereNotNull('approved_date')->where('status', '1')->latest()->paginate(Setting::get('pagination'));

      return view('images.approved')->withImages($images);
    }

    public function final()
    {
      $images = Images::where('status', '2')->latest()->paginate(Setting::get('pagination'));

      return view('images.final')->withImages($images);
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
        'images'  => 'required'
      ]);

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $key => $image) {
          $filename = time() . $key . '.' . $image->getClientOriginalExtension();
          $location = public_path('uploads/social-media/') . $filename;

          Image::make($image)->encode('jpg', 65)->save($location);

          $new_image = new Images;
          $new_image->filename = $filename;
          $new_image->status = $request->status;
          $new_image->save();
        }
      }

      if ($request->status == '1') {
        return redirect()->route('image.grid')->with('success', 'The image(s) were successfully uploaded');
      } elseif ($request->status == '2') {
        return redirect()->route('image.grid.approved')->with('success', 'The image(s) were successfully uploaded');
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $image = Images::find($id);

      return view('images.show')->withImage($image);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $image = Images::find($id);

      return view('images.edit')->withImage($image);
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
      $image = Images::find($id);

      if ($request->hasfile('image')) {
        Storage::disk('uploads')->delete("social-media/$image->filename");

        $filename = time() . '.' . $request->file('image')->getClientOriginalExtension();
        $location = public_path('uploads/social-media/') . $filename;

        Image::make($request->file('image'))->encode('jpg', 65)->save($location);

        $image->filename = $filename;
      }

      $image->publish_date = $request->publish_date;
      $image->save();

      $tags = Tag::all();
      $tags_array = [];
      $image->tags()->detach();

      if (count($tags) > 0) {
        foreach ($tags as $key => $tag) {
          $tags_array[$key] = $tag->tag;
        }
      }

      if (isset($request->tags)) {
        foreach ($request->tags as $tag) {
          if (!in_array($tag, $tags_array)) {
            $new_tag = Tag::create(['tag' => $tag]);
          } else {
            $new_tag = Tag::where('tag', $tag)->first();
          }

          $image->tags()->attach($new_tag);
        }
      }

      return redirect()->route('image.grid.edit', $image->id)->with('success', 'You have successfully updated image');
    }

    public function approveImage($id)
    {
      $image = Images::find($id);

      $image->approved_user = Auth::id();
      $image->approved_date = Carbon::now();

      $image->save();

      if ($image->status == '1') {
        return redirect()->route('image.grid')->with('success', 'You have successfully approved image');
      } elseif ($image->status == '2') {
        return redirect()->route('image.grid.final.approval')->with('success', 'You have successfully approved image');
      }

    }

    public function attachImage(Request $request)
    {
      if ($request->images) {
        foreach (json_decode($request->images) as $image) {
          $new_image = new Images;
          $media = Media::find($image);
          $new_image->save();

          $new_image->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->route('image.grid')->with('success', 'You have successfully attached images');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $image = Images::find($id);

      Storage::disk('uploads')->delete("social-media/$image->filename");

      $image->tags()->detach();
      $image->detachMedia(config('constants.media_tags'));
      $image->delete();

      return redirect()->route('image.grid')->with('success', 'The image was successfully deleted');
    }
}
