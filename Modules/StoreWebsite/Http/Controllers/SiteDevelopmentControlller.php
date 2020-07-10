<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\Role;
use App\Setting;
use App\SiteDevelopment;
use App\SiteDevelopmentCategory;
use App\StoreWebsite;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class SiteDevelopmentController extends Controller
{

    public function index($id = null, Request $request)
    {

        //Getting Website Details
        $website = StoreWebsite::find($id);

        $categories = SiteDevelopmentCategory::orderBy('id', 'desc');

        if ($request->k != null) {
            $categories = $categories->where("title", "like", "%" . $request->k . "%");
        }

        $ignoredCategory = \App\SiteDevelopmentHiddenCategory::where("store_website_id", $id)->pluck("category_id")->toArray();

        if (request('status') == "ignored") {
            $categories = $categories->whereIn('id', $ignoredCategory);
        } else {
            $categories = $categories->whereNotIn('id', $ignoredCategory);
        }

        $categories = $categories->paginate(Setting::get('pagination'));

        //Getting Roles Developer
        $role = Role::where('name', 'LIKE', '%Developer%')->first();

        //User Roles with Developers
        $roles = DB::table('role_user')->select('user_id')->where('role_id', $role->id)->get();

        foreach ($roles as $role) {
            $userIDs[] = $role->user_id;
        }

        if (!isset($userIDs)) {
            $userIDs = [];
        }

        $allStatus = \App\SiteDevelopmentStatus::pluck("name", "id")->toArray();
        $users     = User::select('id', 'name')->whereIn('id', $userIDs)->get();

        if ($request->ajax() && $request->pagination == null) {
            return response()->json([
                'tbody' => view('storewebsite::site-development.partials.data', compact('categories', 'users', 'website', 'allStatus', 'ignoredCategory'))->render(),
                'links' => (string) $categories->render(),
            ], 200);
        }

        return view('storewebsite::site-development.index', compact('categories', 'users', 'website', 'allStatus', 'ignoredCategory'));
    }

    public function addCategory(Request $request)
    {
        if ($request->text) {

            //Cross Check if title is present
            $categoryCheck = SiteDevelopmentCategory::where('title', $request->text)->first();

            if (empty($categoryCheck)) {
                //Save the Category
                $develop        = new SiteDevelopmentCategory;
                $develop->title = $request->text;
                $develop->save();

                return response()->json(["code" => 200, "messages" => 'Category Saved Sucessfully']);

            } else {

                return response()->json(["code" => 500, "messages" => 'Category Already Exist']);
            }

        } else {
            return response()->json(["code" => 500, "messages" => 'Please Enter Text']);
        }
    }

    public function addSiteDevelopment(Request $request)
    {

        if ($request->site) {
            $site = SiteDevelopment::find($request->site);
        } else {
            $site = new SiteDevelopment;
        }

        if ($request->type == 'title') {
            $site->title = $request->text;
        }

        if ($request->type == 'description') {
            $site->description = $request->text;
        }

        if ($request->type == 'status') {
            $site->status = $request->text;
        }

        if ($request->type == 'developer') {
            $site->developer_id = $request->text;
        }

        $site->site_development_category_id = $request->category;
        $site->website_id                   = $request->websiteId;

        $site->save();

        return response()->json(["code" => 200, "messages" => 'Site Development Saved Sucessfully']);

    }

    public function editCategory(Request $request)
    {

        $category = SiteDevelopmentCategory::find($request->categoryId);
        if ($category) {
            $category->title = $request->category;
            $category->save();
        }

        return response()->json(["code" => 200, "messages" => 'Category Edited Sucessfully']);
    }

    public function disallowCategory(Request $request)
    {
        $category         = $request->category;
        $store_website_id = $request->store_website_id;

        if ($category != null && $store_website_id != null) {

            if ($request->status == "false") {
                \App\SiteDevelopmentHiddenCategory::where('store_website_id', $request->store_website_id)->where('category_id', $request->category)->delete();
            } else {
                $siteDevHiddenCat = \App\SiteDevelopmentHiddenCategory::updateOrCreate(
                    ['store_website_id' => $request->store_website_id, 'category_id' => $request->category],
                    ['store_website_id' => $request->store_website_id, 'category_id' => $request->category]
                );
            }

            return response()->json(["code" => 200, "data" => [], "message" => "Data updated Sucessfully"]);
        }

        return response()->json(["code" => 500, "data" => [], "message" => "Required field missing like store website or category"]);
    }

    public function uploadDocuments(Request $request)
    {
        $path = storage_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function saveDocuments(Request $request)
    {
        $site      = null;
        $documents = $request->input('document', []);
        if (!empty($documents)) {
            if ($request->id) {
                $site = SiteDevelopment::find($request->id);
            }

            if (!$site || $request->id == null) {
                $site                               = new SiteDevelopment;
                $site->title                        = "";
                $site->description                  = "";
                $site->website_id                   = $request->store_website_id;
                $site->site_development_category_id = $request->site_development_category_id;
                $site->save();
            }

            foreach ($request->input('document', []) as $file) {
                $path  = storage_path('tmp/uploads/' . $file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('site-development/' . floor($site->id / config('constants.image_per_folder')))
                    ->upload();
                $site->attachMedia($media, config('constants.media_tags'));
            }

            return response()->json(["code" => 200, "data" => [], "message" => "Done!"]);
        } else {
            return response()->json(["code" => 500, "data" => [], "message" => "No documents for upload"]);
        }

    }

    public function listDocuments(Request $request, $id)
    {
        $site    = SiteDevelopment::find($request->id);
        $records = [];
        if ($site) {
            if ($site->hasMedia(config('constants.media_tags'))) {
                foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                    $records[] = [
                        "id"      => $media->id,
                        'url'     => $media->getUrl(),
                        'site_id' => $site->id,
                    ];
                }
            }
        }

        return response()->json(["code" => 200, "data" => $records]);
    }

    public function deleteDocument(Request $request)
    {
        if ($request->id != null) {
            $media = \Plank\Mediable\Media::find($request->id);
            if ($media) {
                $media->delete();
                return response()->json(["code" => 200, "message" => "Document delete succesfully"]);
            }
        }

        return response()->json(["code" => 500, "message" => "No document found"]);
    }

    public function sendDocument(Request $request)
    {
        if ($request->id != null && $request->site_id != null) {
            $media        = \Plank\Mediable\Media::find($request->id);
            $siteDevloper = SiteDevelopment::find($request->site_id);
            if ($siteDevloper && $siteDevloper->developer) {
                if ($media) {
                    \App\ChatMessage::sendWithChatApi(
                        $siteDevloper->developer->phone,
                        null,
                        "Please find attached file",
                        $media->getUrl()
                    );
                    return response()->json(["code" => 200, "message" => "Document send succesfully"]);
                }
            }
        }

        return response()->json(["code" => 200, "message" => "Sorry there is no attachment"]);
    }

}
