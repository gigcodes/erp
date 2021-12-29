<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\DeveloperTask;
use App\Role;
use App\Setting;
use App\SiteDevelopment;
use App\SiteDevelopmentArtowrkHistory;
use App\SiteDevelopmentCategory;
use App\SiteDevelopmentMasterCategory;
use App\StoreWebsite;
use App\Task;
use App\User;
use App\ChatMessage;
use App\ChatMessagesQuickData;
use App\Hubstaff\HubstaffMember;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

use App\Http\Controllers\TaskModuleController;


class SiteDevelopmentController extends Controller
{
//
    public function index($id = null, Request $request)
    {

        $input = $request->input();
        $masterCategories = SiteDevelopmentMasterCategory::pluck('title', 'id')->toArray();
        //Getting Website Details
        $website = StoreWebsite::find($id);

        $categories = SiteDevelopmentCategory::select('site_development_categories.*', 'site_developments.site_development_master_category_id', DB::raw('(SELECT id from site_developments where site_developments.site_development_category_id = site_development_categories.id AND `website_id` = ' . $id . ' ORDER BY created_at DESC limit 1) as site_development_id'));

        if ($request->k != null) {
            $categories = $categories->where("site_development_categories.title", "like", "%" . $request->k . "%");
        }

        $ignoredCategory = \App\SiteDevelopmentHiddenCategory::where("store_website_id", $id)->pluck("category_id")->toArray();

        if (request('status') == "ignored") {
            $categories = $categories->whereIn('site_development_categories.id', $ignoredCategory);
        } else {
            $categories = $categories->whereNotIn('site_development_categories.id', $ignoredCategory);
        }

        //$categories = $categories->paginate(Setting::get('pagination'));
        // $categories = $categories->paginate(20);

        $categories->join('site_developments', function ($q) use ($id) {
            $q->on('site_developments.site_development_category_id', '=', 'site_development_categories.id')
                ->where('site_developments.website_id', $id);

        });
        /* Status filter */
        if ($request->status) {
            //$categories->where('site_developments.status' , $request->status);
            $categories->havingRaw('(SELECT status from site_developments where site_developments.site_development_category_id = site_development_categories.id AND `website_id` = ' . $id . ' ORDER BY created_at DESC limit 1) = ' . $request->status);
        }

        $categories->groupBy('site_development_categories.id');

        if ($request->order) {
            if ($request->order == 'title') {
                $categories->orderBy('site_development_categories.title', 'asc');
            } else if ($request->order == 'communication') {
                $categories = $categories->leftJoin('store_development_remarks', 'store_development_remarks.store_development_id', '=', 'site_developments.id');
                $categories = $categories->orderBy('store_development_remarks.created_at', 'DESC');
            }
        } else {
            $categories->orderBy('title', 'asc');
        }
        $categories = $categories->paginate(Setting::get('pagination'));

        foreach ($categories as $category) {
            $finalArray = [];
            $site_developement_id = $category->site_development_id;
            $taskStatistics['Devtask'] = DeveloperTask::where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select();

            $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
            $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
            $taskStatistics = $query->get();
            //print_r($taskStatistics);
            $othertask = Task::where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select();
            $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
            $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
            $othertaskStatistics = $query1->get();
            $merged = $othertaskStatistics->merge($taskStatistics);
            foreach ($merged as $m) {
                /*if($m['task_type'] == 'task' ) {
                $object = Task::find($m['id']);
                } else {
                $object = DeveloperTask::find($m['id']);
                }*/
                $chatMessage = $m->whatsappAll()->orderBy('id', 'desc')->pluck('message')->first();
                $m['message'] = $chatMessage;
            }
            $category->assignedTo = $merged;
        }

        //Getting   Roles Developer
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

//dd($allStatus);

        $statusCount = \App\SiteDevelopment::join("site_development_statuses as sds", "sds.id", "site_developments.status")
            ->where("site_developments.website_id", $id)
            ->where("site_developments.status", $request->status)
            ->groupBy("sds.id")
            ->select(["sds.name", \DB::raw("count(sds.id) as total")])
            ->orderBy("name", "desc")
            ->get();

        $allUsers = User::select('id', 'name')->get();

        $users = User::select('id', 'name')->whereIn('id', $userIDs)->get();
        $store_websites = StoreWebsite::pluck("title","id")->toArray();
    


        if ($request->ajax() && $request->pagination == null) {
            return response()->json([
                'tbody' => view('storewebsite::site-development.partials.data', compact('input', 'masterCategories', 'categories', 'users', 'website', 'allStatus', 'ignoredCategory', 'statusCount', 'allUsers','store_websites'))->render(),
                'links' => (string) $categories->render(),
            ], 200);
        }

        return view('storewebsite::site-development.index', compact('input', 'masterCategories', 'categories', 'users', 'website', 'allStatus', 'ignoredCategory', 'statusCount', 'allUsers','store_websites'));
    }

    public function SendTask(Request $request)
    {
        $id = $request->id;

        // $user_id = Auth::id();
        if ($request->type == 'TASK') {
            $task = \App\Task::find($request->taskdata);
            $user = User::find($task->assign_to);

        } else {
            $task = DeveloperTask::find($request->taskdata);
            $user = User::find($task->user_id);
        }
        $taskdata = $request->taskdata;

        $media = \Plank\Mediable\Media::find($request->id);

        $admin = Auth::user();

        $userid = Auth::id();
        $msg = $media->getUrl();
        if ($user && $user->phone) {
            if ($request->type == 'TASK') {

                $params = \App\ChatMessage::create([
                    'id' => $id,
                    'user_id' => $userid,
                    'task_id' => $request->task_id,

                    'sent_to_user_id' => $user->id,

                    'erp_user' => $task->assign_to,
                    'contact_id' => $task->assign_to,
                    'message' => $media->getUrl(),

                ]);
                $params = \App\ChatMessage::create([
                    'id' => $id,
                    'user_id' => $user->id,
                    'task_id' => $taskdata,

                    'sent_to_user_id' => $userid,

                    'erp_user' => $task->assign_to,
                    'contact_id' => $task->assign_to,
                    'message' => $media->getUrl(),

                ]);
            } else {
                $params = \App\ChatMessage::create([
                    'id' => $id,
                    'user_id' => $userid,
                    'task_id' => $request->task_id,
                    'developer_task_id' => $task->id,
                    'sent_to_user_id' => $user->id,
                    'issue_id' => $task->id,
                    'erp_user' => $task->assign_to,
                    'contact_id' => $task->assign_to,
                    // 'approved' => '1',
                    // 'status' => '2',
                    'message' => $media->getUrl(),

                ]);
            }

            if ($params) {
                app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg);

                return response()->json([
                    'message' => 'Successfully Send File',
                ], 200);
            }
            return response()->json([
                'message' => 'Something Was Wrong',
            ], 500);

            return response()->json(["message" => "Sorry required fields is missing like id , userid"], 500);
        }
    }

    public function addMasterCategory(Request $request)
    {
        if ($request->text) {

            //Cross Check if title is present
            $categoryCheck = SiteDevelopmentMasterCategory::where('title', $request->text)->first();

            if (empty($categoryCheck)) {
                //Save the Category
                $develop = new SiteDevelopmentMasterCategory;
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

    public function addCategory(Request $request)
    {
        if ($request->text) {

            //Cross Check if title is present
            $categoryCheck = SiteDevelopmentCategory::where('title', $request->text)->first();
            $websiteId = $request->websiteId;


            if (empty($categoryCheck)) {
                //Save the Category
                $develop = new SiteDevelopmentCategory;
                $develop->title = $request->text;
                $develop->master_category_id = $request->master_category_id;
                $develop->save();

                $all_website = StoreWebsite::get();

                $site_id = 0;

                foreach ($all_website as $key => $value) {
                    $site = new SiteDevelopment;
                    $site->site_development_category_id = $develop->id;
                    $site->site_development_master_category_id = $develop->master_category_id;
                    $site->website_id = $value->id;
                    $site->save();

                    if($websiteId == $value->id){
                        $site_id = $site->id;
                    }

                }
                $requests = array(
                    '_token' => $request->_token,
                    'task_subject' => $request->websiteId,
                    'task_detail' => "TEST".$request->websiteId.' '.$request->text.' '.$request->master_category_id,
                    'task_asssigned_to' => 412,
                    'category_id'=>49,
                    'site_id'=>$site_id,
                    'task_type'=>0,
                    'repository_id'=>null,
                    'cost'=>null,
                    'task_id'=>null,
                    'customer_id'=>null,
                );
                $check = $this->createTaskFromSortcuts($requests);

                return response()->json(["code" => 200, "messages" => 'Category Saved Sucessfully']);
            } else {
				
				$all_website = StoreWebsite::get();
				$i=1;

                $site_id = 0;

				foreach ($all_website as $key => $value) {
					$develop = SiteDevelopment::where('site_development_category_id',$categoryCheck->id )->where('website_id', $value->id)->first();
					if(empty($develop)) {
						$i=0;
						$site = new SiteDevelopment;
						$site->site_development_category_id = $categoryCheck->id;
						$site->site_development_master_category_id = $categoryCheck->master_category_id;
						$site->website_id = $value->id;
						$site->save();

                        if($websiteId == $value->id){
                            $site_id = $site->id;
                        }
					}
						
				}
                $requests = array(
                    '_token' => $request->_token,
                    'task_subject' => $request->websiteId,
                    'task_detail' => "TEST".$request->websiteId.' '.$request->text.' '.$request->master_category_id,
                    'task_asssigned_to' => 412,
                    'category_id'=>49,
                    'site_id'=>$site_id,
                    'task_type'=>0,
                    'repository_id'=>null,
                    'cost'=>null,
                    'task_id'=>null,
                    'customer_id'=>null,
                );
                $check = $this->createTaskFromSortcuts($requests);

				if($i ==1){
					return response()->json(["code" => 500, "messages" => 'Category Already Exist']);
				} else{

					return response()->json(["code" => 200, "messages" => 'Category Saved Sucessfully',]);
				}
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

        if ($request->type == 'designer_id') {
            $site->designer_id = $request->text;
        }

        if ($request->type == 'html_designer') {
            $site->html_designer = $request->text;
        }

        if ($request->type == 'site_development_master_category_id') {
            $site->site_development_master_category_id = $request->text;
            SiteDevelopment::where(['site_development_category_id' => $request->category])->update(['site_development_master_category_id' => $request->text]);
        }

        if ($request->type == 'tester_id') {
            $site->tester_id = $request->text;
        }

        if ($request->type == 'artwork_status') {
            $old_artwork = $site->artwork_status;
            if (!$old_artwork || $old_artwork == '') {
                $old_artwork = 'Yes';
            }
            $new_artwork = $request->text;
            $site->artwork_status = $request->text;
        }

        $site->site_development_category_id = $request->category;
        $site->website_id = $request->websiteId;
        $site->save();
        $html = '';
        if ($request->type == 'status') {
            $id = $site->id;
            $siteDev = SiteDevelopment::where('id', $id)->first();
            $status = ($siteDev) ? $siteDev->status : 0;
            if ($siteDev && $status > 0) {
                \App\SiteDevelopmentStatusHistory::create([
                    "site_development_id" => $id,
                    "status_id" => $siteDev->status,
                    "user_id" => auth()->user()->id,
                ]);
            }
            if ($status == 3) {
                $html .= "<i class='fa fa-ban save-status' data-text='4' data-site=" . $siteDev->id . " data-category=" . $siteDev->site_development_category_id . "  data-type='status' aria-hidden='true' style='color:red;'' title='Deactivate'></i>";
            } elseif ($status == 4 || $status == 0) {
                $html .= "<i class='fa fa-ban save-status' data-text='3' data-site=" . $siteDev->id . " data-category=" . $siteDev->site_development_category_id . "  data-type='status' aria-hidden='true' style='color:black;' title='Activate'></i>";
            }
        }
        if ($request->type == 'artwork_status') {
            $history = new SiteDevelopmentArtowrkHistory;
            $history->date = date('Y-m-d');
            $history->site_development_id = $site->id;
            $history->from_status = $old_artwork;
            $history->to_status = $new_artwork;
            $history->username = Auth::user()->name;
            $history->save();
        }

        return response()->json(["code" => 200, "messages" => 'Site Development Saved Sucessfully', 'html' => $html]);

    }

    public function getArtworkHistory($site_id)
    {
        $site = SiteDevelopment::find($site_id);
        $histories = [];
        if ($site) {
            $histories = SiteDevelopmentArtowrkHistory::where('site_development_id', $site->id)->get();
        }
        return response()->json(["code" => 200, "data" => $histories]);
    }

    public function statusHistory($site_id)
    {
        $site = SiteDevelopment::find($site_id);
        $histories = [];
        if ($site) {
            $hist = $site->statusHistories()->latest()->get();
            if (!$hist->isEmpty()) {
                foreach ($hist as $h) {
                    $histories[] = [
                        "id" => $h->id,
                        "status_name" => $h->status->name,
                        "user_name" => $h->user->name,
                        "created_at" => (string) $h->created_at,
                    ];
                }
            }

        }
        return response()->json(["code" => 200, "data" => $histories]);
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
        $category = $request->category;
        $store_website_id = $request->store_website_id;

        if ($category != null && $store_website_id != null) {
            if ($request->status) {
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
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function saveDocuments(Request $request)
    {
        $site = null;
        $documents = $request->input('document', []);
        if (!empty($documents)) {
            if ($request->id) {
                $site = SiteDevelopment::find($request->id);
            }

            if (!$site || $request->id == null) {
                $site = new SiteDevelopment;
                $site->title = "";
                $site->description = "";
                $site->website_id = $request->store_website_id;
                $site->site_development_category_id = $request->site_development_category_id;
                $site->save();
            }

            foreach ($request->input('document', []) as $file) {
                $path = storage_path('tmp/uploads/' . $file);
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
        $site = SiteDevelopment::find($request->id);

        $userList = [];

        if ($site->developer) {
            $userList[$site->developer->id] = $site->developer->name;
        }

        if ($site->designer) {
            $userList[$site->designer->id] = $site->designer->name;
        }

        $userList = array_filter($userList);
        // create the select box design html here
        $usrSelectBox = "";
        if (!empty($userList)) {
            $usrSelectBox = (string) \Form::select("send_message_to", $userList, null, ["class" => "form-control send-message-to-id"]);
        }

        $records = [];
        if ($site) {
            if ($site->hasMedia(config('constants.media_tags'))) {
                foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                    $records[] = [
                        "id" => $media->id,
                        'url' => $media->getUrl(),
                        'site_id' => $site->id,
                        'user_list' => $usrSelectBox,
                    ];
                }
            }
        }

        return response()->json(["code" => 200, "data" => $records]);
    }

    public function previewTaskImage($id)
    {
        $task = \App\Task::find($id);

        $records = [];
        if ($task) {
            $userList = User::pluck('name', 'id')->all();
            $task = \App\Task::find($id);
            $userName = '';
            $mediaDetail = array();
            // $usrSelectBox = "";
            // if (!empty($userList)) {
            //     $usrSelectBox = (string) \Form::select("send_message_to", $userList, null, ["class" => "form-control send-message-to-id"]);
            // }
            if ($task->hasMedia(config('constants.attach_image_tag'))) {
                foreach ($task->getMedia(config('constants.attach_image_tag')) as $media) {
                    $imageExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief', 'jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd'];
                    $explodeImage = explode('.', $media->getUrl());
                    $extension = end($explodeImage);

                    if (in_array($extension, $imageExtensions)) {
                        $isImage = true;
                    } else {
                        $isImage = false;
                    }

                    $mediaDetail = DB::table('media')->where('id', $media->id)->first();
                    if ($mediaDetail) {
                        $userName = User::where('id', $mediaDetail->user_id)->pluck('name')->first();
                    } else {
                        $userName = '';
                    }

                    $records[] = [
                        "media_id" => $id,
                        "id" => $media->id,
                        'url' => $media->getUrl(),
                        'task_id' => $task->id,
                        'isImage' => $isImage,
                        'userList' => $userList,
                        'userName' => $userName,
                        'created_at' => $media->created_at,
                    ];
                }
            }
        }

        $records = array_reverse($records);
        $title = 'Preview images';
        return view('storewebsite::site-development.partials.preview-task-images', compact('title', 'records'));
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
        if ($request->id != null && $request->site_id != null && $request->user_id != null) {
            $media = \Plank\Mediable\Media::find($request->id);
            $user = \App\User::find($request->user_id);
            if ($user) {
                if ($media) {
                    \App\ChatMessage::sendWithChatApi(
                        $user->phone,
                        null,
                        "Please find attached file",
                        $media->getUrl()
                    );
                    return response()->json(["code" => 200, "message" => "Document send succesfully"]);
                }
            } else {
                return response()->json(["code" => 200, "message" => "User or site is not available"]);
            }
        }

        return response()->json(["code" => 200, "message" => "Sorry required fields is missing like id, siteid , userid"]);
    }

    public function SendTaskSOP(Request $request)
    {

        $media = \Plank\Mediable\Media::find($request->id);
        $user = \App\User::find($request->user_id);

        $task = \App\Task::find($request->task_id);
        $username = \App\User::find($task->assign_to);

        // dd($username->name);
        $userid = Auth::id();

        if ($username) {
            $params = \App\Sop::create([
                'name' => $username->name,
                'content' => $media->getUrl(),

            ]);

            return response()->json(["message" => "File Added Successfully In Sop", 'success' => true]);
        } else {

            return response()->json(["message" => "Task is not assigned to any user", 'success' => false]);
        }

    }

    public function remarks(Request $request, $id)
    {
        // $response = \App\StoreDevelopmentRemark::join("users as u","u.id","store_development_remarks.user_id")->where("store_development_id",$id)
        // ->select(["store_development_remarks.*",\DB::raw("u.name as created_by")])
        // ->orderBy("store_development_remarks.created_at","desc")
        // ->get();
        $data = \App\SiteDevelopment::where('site_development_category_id', $request->cat_id)->where('website_id', $request->website_id)->get();
        $response = [];
        foreach ($data as $val) {

            $remarks = \App\StoreDevelopmentRemark::join('users as usr', 'usr.id', 'store_development_remarks.user_id')
                ->where('store_development_remarks.store_development_id', $val->id)
                ->select('store_development_remarks.*', 'usr.name as created_by')
                ->get()->toArray();
            array_push($response, $remarks);
        }
        return response()->json(["code" => 200, "data" => $response, 'site_id' => $id]);
    }

    public function saveRemarks(Request $request, $id)
    {
        \App\StoreDevelopmentRemark::create([
            "remarks" => $request->remark,
            "store_development_id" => $id,
            "user_id" => \Auth::user()->id,
        ]);

        $site_devs = \App\SiteDevelopment::where('site_development_category_id', $request->cat_id)->where('website_id', $request->website_id)->get()->pluck('id')->toArray();

        // $response = \App\StoreDevelopmentRemark::whereIn('store_development_id',$site_devs)->orderBy('id', 'DESC')->get();
        $response = \App\StoreDevelopmentRemark::join("users as u", "u.id", "store_development_remarks.user_id")->where("store_development_id", $id)
            ->select(["store_development_remarks.*", \DB::raw("u.name as created_by")])
            ->orderBy("store_development_remarks.remarks", "asc")
            ->get();
        return response()->json(["code" => 200, "data" => $response]);

    }

    public function previewImage($site_id)
    {
        $site = SiteDevelopment::find($site_id);
        $records = [];
        if ($site) {
            // $userList = [];

            // if ($site->developer_id) {
            //     $userList[$site->publisher->id] = $site->publisher->name;
            // }

            // if ($site->designer_id) {
            //     $userList[$site->creator->id] = $site->creator->name;
            // }
            // if ($site->designer_id) {
            //     $userList[$site->creator->id] = $site->creator->name;
            // }
            // $userList = array_filter($userList);

            if ($site->hasMedia(config('constants.media_tags'))) {
                foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                    $records[] = [
                        "id" => $media->id,
                        'url' => $media->getUrl(),
                        'site_id' => $site->id,
                    ];
                }
            }
        }
        $title = 'Preview images';
        return response()->json(["code" => 200, "data" => $records, 'title' => $title]);
        // return view('content-management.preview-website-images', compact('title','records'));
    }

    public function latestRemarks(Request $request, $id)
    {

        if ($request->status != '') {
            $remarks = DB::select(DB::raw('select * from (SELECT max(store_development_remarks.id) as remark_id,remarks,site_development_categories.title,store_development_remarks.created_at,site_development_categories.id as category_id, users.name as username,
            store_development_remarks.store_development_id,site_developments.id as site_id,store_development_remarks.user_id, site_developments.title as sd_title, sw.website as sw_website,site_developments.status as status
            FROM `store_development_remarks` inner join site_developments on site_developments.id = store_development_remarks.store_development_id inner join site_development_categories on site_development_categories.id = site_developments.site_development_category_id
            left join store_websites as sw on sw.id = site_developments.website_id
            join users on users.id = store_development_remarks.user_id
            where site_developments.website_id = ' . $id . ' and status =' . $request->status . ' group by store_development_id) as latest join store_development_remarks on store_development_remarks.id = latest.remark_id order by title asc'));
        } else {
            // $remarks = DB::select(DB::raw('select * from (SELECT max(store_development_remarks.id) as remark_id,remarks,site_development_categories.title,store_development_remarks.created_at,site_development_categories.id as category_id, users.name as username,
            // store_development_remarks.store_development_id,site_developments.id as site_id,store_development_remarks.user_id, site_developments.title as sd_title, sw.website as sw_website,site_developments.status as status
            // FROM `store_development_remarks` inner join site_developments on site_developments.id = store_development_remarks.store_development_id inner join site_development_categories on site_development_categories.id = site_developments.site_development_category_id
            // left join store_websites as sw on sw.id = site_developments.website_id
            // join users on users.id = store_development_remarks.user_id
            // where site_developments.website_id = '.$id.' group by store_development_id) as latest join store_development_remarks on store_development_remarks.id = latest.remark_id order by title asc'));

            $remarks = DB::select(DB::raw('select *,SDR.remarks As latest_remarks from (SELECT max(store_development_remarks.id) as remark_id,remarks,site_development_categories.title,store_development_remarks.created_at,site_development_categories.id as category_id, users.name as username,
            store_development_remarks.store_development_id,site_developments.id as site_id,store_development_remarks.user_id, site_developments.title as sd_title, sw.website as sw_website,site_developments.status as status
            FROM `store_development_remarks` inner join site_developments on site_developments.id = store_development_remarks.store_development_id inner join site_development_categories on site_development_categories.id = site_developments.site_development_category_id
            left join store_websites as sw on sw.id = site_developments.website_id
            join users on users.id = store_development_remarks.user_id
            where site_developments.website_id = ' . $id . ' group by category_id) as latest inner join store_development_remarks as SDR on SDR.id = latest.remark_id order by title asc'));
        }
        $username = [];
        foreach ($remarks as $remark) {
            $user = \App\User::find($remark->user_id);
            array_push($username, $user->name);
        }
        $allStatus = \App\SiteDevelopmentStatus::get();
        // $remarks = \App\StoreDevelopmentRemark::join('site_developments','site_developments.id','store_development_remarks.store_development_id')
        // ->join('site_development_categories','site_development_categories.id','site_developments.site_development_category_id')
        // ->orderBy('store_development_remarks.created_at','DESC')
        // ->groupBy('site_developments.site_development_category_id')
        // ->select('store_development_remarks.*','site_development_categories.title')->get();

        // $response = \App\StoreDevelopmentRemark::join("users as u","u.id","store_development_remarks.user_id")->where("store_development_id",$id)
        // ->select(["store_development_remarks.*",\DB::raw("u.name as created_by")])
        // ->orderBy("store_development_remarks.created_at","desc")
        // ->get();
        return response()->json(["code" => 200, "data" => $remarks, "username" => $username, 'status' => $allStatus]);
    }

    public function allartworkHistory($website_id)
    {
        $histories = \App\SiteDevelopment::
            join("site_development_artowrk_histories", "site_development_artowrk_histories.site_development_id", "site_developments.id")
            ->join("site_development_categories", "site_development_categories.id", "site_developments.site_development_category_id")
            ->where('site_developments.website_id', $website_id)
            ->select("site_development_artowrk_histories.*", 'site_development_categories.title')
            ->get();
        $title = 'Multi site artwork histories';
        return response()->json(["code" => 200, "data" => $histories]);
    }
    public function taskCount($site_developement_id)
    {
        $taskStatistics['Devtask'] = DeveloperTask::where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select();

        $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
        $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
        $taskStatistics = $query->get();
        //print_r($taskStatistics);
        $othertask = Task::where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select();
        $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
        $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
        $othertaskStatistics = $query1->get();
        $merged = $othertaskStatistics->merge($taskStatistics);

        return response()->json(["code" => 200, "taskStatistics" => $merged]);

    }
    public function deleteDevTask(Request $request)
    {

        $id = $request->input('id');
        if ($request->tasktype == 'Devtask') {
            $task = DeveloperTask::find($id);
        } elseif ($request->tasktype == 'Othertask') {
            $task = Task::find($id);
        }

        if ($task) {
            $task->delete();
        }

        if ($request->ajax()) {
            return response()->json(["code" => 200]);
        }

    }
    public function adminRemarkFlag(Request $request)
    {
        $remarks = \App\StoreDevelopmentRemark::find($request->remark_id);

        if ($remarks->admin_flagged == 0) {
            $remarks->admin_flagged = 1;
        } else {
            $remarks->admin_flagged = 0;
        }

        $remarks->save();

        return response()->json(['admin_flagged' => $remarks->admin_flagged]);
    }

    public function userRemarkFlag(Request $request)
    {
        $remarks_user = \App\StoreDevelopmentRemark::find($request->remark_id);

        if ($remarks_user->user_flagged == 0) {
            $remarks_user->user_flagged = 1;
        } else {
            $remarks_user->user_flagged = 0;
        }

        $remarks_user->save();

        return response()->json(['user_flagged' => $remarks_user->user_flagged]);
    }

    public function siteDevlopmentStatusUpdate(Request $request)
    {
        $allStatus = \App\SiteDevelopmentStatus::get();
        $site = SiteDevelopment::find($request->site_id);
        $site->status = $request->status;
        $site->save();
        return response()->json(['message' => "Status updated successfully", 'status' => $allStatus, 'site' => $site]);
    }

     public function createTaskFromSortcuts($request)
    {
        $created = 0;
        $message = '';
        $assignedUserId = 0;
        //$data = $request->except('_token');
        //print_r($data); die;
     /*   $this->validate($request, [
            'task_subject'  => 'required',
            'task_detail'   => 'required',
            'task_asssigned_to' => 'required_without:assign_to_contacts',*/
            //'cost'=>'sometimes|integer'
      /*  ]);*/
        $data['assign_from'] = Auth::id();
        $data['status'] = 3;
        
        $taskType = $request['task_type'];

            

        if ($taskType == "4" || $taskType == "5" || $taskType == "6") {
            $data = [];

            if (is_array($request['task_asssigned_to'])) {
                $data["assigned_to"] = $request['task_asssigned_to'];
            } else {
                $data["assigned_to"] = $request['task_asssigned_to'];
            }
            
            $data["subject"]        = $request["task_subject"];
            $data["task"]           = $request["task_detail"];
            $data["task_type_id"]   = 1;
            $data["site_developement_id"]   = $request["site_id"];
            $data["cost"]   = $request["cost"];
            $data["status"] = 'In Progress';
            $data["created_by"] = Auth::id();
            //echo $data["site_developement_id"]; die;
            
            if ($taskType == 5 || $taskType == 6) {
                $data["task_type_id"]   = 3;
            }
            $task = DeveloperTask::create($data);
            $created = 1;
            $message = '#DEVTASK-' . $task->id . ' => ' . $task->subject;
            $assignedUserId = $task->assigned_to;

            $newBranchName = null;
            if (!empty($request['repository_id']) && $request['repository_id'] > 0) {
                $newBranchName = $this->createBranchOnGithub(
                    $request['repository_id'],
                    $task->id,
                    $task->subject
                );
                if ($newBranchName) {
                    $task->github_branch_name = $newBranchName;
                    $task->save();
                }
            }

            if (is_string($newBranchName) && !empty($newBranchName)) {
                $message = $request["task_detail"] . PHP_EOL . "A new branch " . $newBranchName . " has been created. Please pull the current code and run 'git checkout " . $newBranchName . "' to work in that branch.";
            } else {
                $message = $request["task_detail"];
            }

            $requestData = new Request();
            $requestData->setMethod('POST');
            $requestData->request->add(['issue_id' => $task->id, 'message' => $message, 'status' => 1]);
            app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'issue');
        } else {

            if ($request['task_type'] == 'quick_task') {
                $data['is_statutory'] = 0;
                $data['category'] = 6;
                /*$data['model_type'] = $request['model_type'];
                $data['model_id'] = $request['model_id'];*/
            }

            if ($request['task_type'] == 'note-task') {
                $main_task = Task::find($request['task_id']);
                if (is_array($request['task_asssigned_to'])) {
                    $data["assign_to"] = $request['task_asssigned_to'];
                } else {
                    $data["assign_to"] = $request['task_asssigned_to'];
                }
            } else {
                if ($request['task_asssigned_to']) {
                    if (is_array($request['task_asssigned_to'])) {
                        $data["assign_to"] = $request['task_asssigned_to'];
                    } else {
                        $data["assign_to"] = $request['task_asssigned_to'];
                    }
                } else {
                    $data['assign_to'] = $request['assign_to_contacts'];
                }
            }
            //discussion task
            if ($request["task_type"] == 3) {
                $task = Task::find($request["task_subject"]);
                $data['is_statutory'] = $request["task_type"];
                $data['task_details'] = $request["task_detail"];
                $data['task_subject'] = $request["task_subject"];
                $data["customer_id"]    = $request["customer_id"];
                if ($request->category_id != null) {
                    $data['category']     = $request->category_id;
                }
                if (!$task) {
                    $task = Task::create($data);
                    $remarks = $request["task_subject"];
                    $created = 1;
                    $assignedUserId = $task->assign_to;
                    $message = '#TASK-' . $task->id . ' => ' . $task->task_subject. ". " . $task->task_details;
                } else {
                    $remarks = $task->task_subject;
                }
                $exist = Remark::where('taskid', $task->id)->where('remark', $remarks)->where('module_type', 'task-note')->first();
                if (!$exist) {
                    Remark::create([
                        'taskid'    => $task->id,
                        'remark'    => $remarks,
                        'module_type'   => 'task-note'
                    ]);
                }
                if ($request['note']) {
                    foreach ($request['note'] as $note) {
                        if ($note != null) {
                            Remark::create([
                                'taskid'    => $task->id,
                                'remark'    => $note,
                                'module_type'   => 'task-note'
                            ]);
                        }
                    }
                }
            } else {
                $data['is_statutory'] = $request["task_type"];
                $data['task_details'] = $request["task_detail"];
                $data['task_subject'] = $request["task_subject"];
                $data["customer_id"]    = $request["customer_id"];
                $data["site_developement_id"]   = $request['site_id'];
                $data["cost"]   = $request["cost"];
                if ($request['category_id'] != null) {
                    $data['category']     = $request['category_id'];
                }
                $task = Task::create($data);
                $created = 1;
                $assignedUserId = $task->assign_to;
                if ($task->is_statutory != 1) {
                    $message = "#" . $task->id . ". " . $task->task_subject . ". " . $task->task_details;
                } else {
                    $message = $task->task_subject . ". " . $task->task_details;
                }
            }


            if ($request['task_type'] != 'note-task') {
                if ($request['task_asssigned_to']) {
                    if (is_array($request['task_asssigned_to'])) {
                        foreach ($request['task_asssigned_to'] as $user_id) {
                            $task->users()->attach([$user_id => ['type' => User::class]]);
                        }
                    } else {
                        $task->users()->attach([$request['task_asssigned_to'] => ['type' => User::class]]);
                    }
                }

                

                if ($request['assign_to_contacts']) {
                    foreach ($request['assign_to_contacts'] as $contact_id) {
                        $task->users()->attach([$contact_id => ['type' => Contact::class]]);
                    }
                }
            }

        

            $params = [
                'number'       => null,
                'user_id'      => Auth::id(),
                'approved'     => 1,
                'status'       => 2,
                'task_id'      => $task->id,
                'message'      => $message
            ];

            if (count($task->users) > 0) {
                if ($task->assign_from == Auth::id()) {
                    foreach ($task->users as $key => $user) {
                        if ($key == 0) {
                            $params['erp_user'] = $user->id;
                        } else {
                            app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                        }
                    }
                } else {
                    foreach ($task->users as $key => $user) {
                        if ($key == 0) {
                            $params['erp_user'] = $task->assign_from;
                        } else {
                            if ($user->id != Auth::id()) {
                                app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                            }
                        }
                    }
                }
            }

            if (count($task->contacts) > 0) {
                foreach ($task->contacts as $key => $contact) {
                    if ($key == 0) {
                        $params['contact_id'] = $task->assign_to;
                    } else {
                        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($contact->phone, null, $params['message']);
                    }
                }
            }

            $chat_message = ChatMessage::create($params);
            ChatMessagesQuickData::updateOrCreate([
                'model' => \App\Task::class,
                'model_id' => $params['task_id']
                ], [
                'last_communicated_message' => @$params['message'],
                'last_communicated_message_at' => $chat_message->created_at,
                'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
            ]);

            $myRequest = new Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['messageId' => $chat_message->id]);

            app('App\Http\Controllers\WhatsAppController')->approveMessage('task', $myRequest);
        }


        if ($created) {
            // $hubstaff_project_id = getenv('HUBSTAFF_BULK_IMPORT_PROJECT_ID');
            $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

            $assignedUser = HubstaffMember::where('user_id', $assignedUserId)->first();
      
            $hubstaffUserId = null;
            $hubstaffTaskId = null;
            if ($assignedUser) {
                $hubstaffUserId = $assignedUser->hubstaff_user_id;
            }
            $taskSummery = substr($message, 0, 200);
            if ($hubstaffUserId) {
                $hubstaffTaskId = $this->createHubstaffTask(
                    $taskSummery,
                    $hubstaffUserId,
                    $hubstaff_project_id
                );
            }
             
      
            if ($hubstaffTaskId) {
                $task->hubstaff_task_id = $hubstaffTaskId;
                $task->save();
            }
            if ($hubstaffTaskId) {
                $hubtask = new HubstaffTask();
                $hubtask->hubstaff_task_id = $hubstaffTaskId;
                $hubtask->project_id = $hubstaff_project_id;
                $hubtask->hubstaff_project_id = $hubstaff_project_id;
                $hubtask->summary = $message;
                $hubtask->save();
            }
        }

        /*if ($request->ajax() && $request->from == 'task-page') {
            $hasRender = request("has_render", false);

            $task_statuses=TaskStatus::all();
            
            if (!empty($hasRender)) {
                $users      = Helpers::getUserArray(User::all());
                $priority   = \App\ErpPriority::where('model_type', '=', Task::class)->pluck('model_id')->toArray();

                if ($task->is_statutory == 1) {
                    $mode = "task-module.partials.statutory-row";
                }
                // else if($task->is_statutory == 3) {
                //  $mode = "task-module.partials.discussion-pending-raw";
                // }
                else {
                    $mode = "task-module.partials.pending-row";
                }

                $view = (string)view($mode, compact('task', 'priority', 'users', 'task_statuses'));
                return response()->json(["code" => 200, "statutory" => $task->is_statutory , "raw" => $view]);
            }
            return response('success');
        }*/

        return response()->json(["code" => 200, "data" => [], "message" => "Your quick task has been created!"]);
    }
}
