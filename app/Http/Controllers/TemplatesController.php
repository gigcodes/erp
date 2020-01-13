<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use App\Setting;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class TemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("template.index");
    }

    public function response()
    {
        $records = \App\Template::orderBy("id", "desc")->paginate(Setting::get('pagination'));
        foreach($records as &$item) {
            $media = $item->lastMedia(config('constants.media_tags'));
            $item->image = ($media) ? $media->getUrl() : "";
        }
        return response()->json([
            "code"       => 1,
            "result"     => $records,
            "pagination" => (string) $records->links(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $template = new \App\Template;
        if($request->auto_generate_product == 'on'){
           $request->merge(['auto_generate_product' => '1']);
        }
        
        $template->fill(request()->all());

        if ($template->save()) {
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('template-images')->upload();
                    $template->attachMedia($media, config('constants.media_tags'));
                }
            }
        }

        return response()->json(["code" => 1, "message" => "Template Created successfully!"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $template = \App\Template::where("id", $id)->first();

        if ($template) {
            $template->delete();
        }

        return response()->json(["code" => 1, "message" => "Template Deleted successfully!"]);
    }

    public function edit(Request $request)
    {
        $template = \App\Template::find($request->id);
        if($request->auto == 'on'){
           $template->auto_generate_product = 1;
        }else{
            $template->auto_generate_product = 0;
        }
        $template->name = $request->name;
        $template->no_of_images = $request->number;
        $template->update();

        if ($template->save()) {
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('template-images')->upload();
                    $template->attachMedia($media, config('constants.media_tags'));
                }
            }
        }

        return redirect()->back();
    
    }

}
