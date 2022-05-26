<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\ChatMessage;
use App\Http\Controllers\WhatsAppController;
use App\StoreWebsite;
use Auth;
use Crypt;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use App\SocialStrategySubject;
use App\Setting;
use App\User;
use App\SocialStrategy;
use App\StoreWebsiteUsers;
use seo2websites\MagentoHelper\MagentoHelperv2;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use App\ProductCancellationPolicie;
use App\StoreWebsiteUserHistory;
use App\StoreReIndexHistory;
use App\BuildProcessHistory;
use App\SiteDevelopmentCategory;
use App\SiteDevelopment;
use App\LogStoreWebsiteUser;
use Carbon\Carbon;
use App\Github\GithubRepository;


class StoreWebsiteController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $title = "List | Store Website";
        $services = Service::get();

        return view('storewebsite::index', compact('title','services'));
    }
    public function logWebsiteUsers($id)
    {
        $title = "List | Store Website User Logs";
        $logstorewebsiteuser = LogStoreWebsiteUser::where("store_website_id", $id)->orderBy('id', 'DESC')->get();

        return view('storewebsite::log_store_website_users', compact('title','logstorewebsiteuser'));
    }

    public function cancellation()
    {
        $title = "Cancellation Policy | Store Website";

        return view('storewebsite::cancellation', compact('title'));
    }

    /**
     * records Page
     * @param  Request $request [description]
     * @return
     */
    public function records(Request $request)
    {
        $records = StoreWebsite::whereNull("deleted_at");

        $keyword = request("keyword");
        if (!empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where("website", "LIKE", "%$keyword%")
                    ->orWhere("title", "LIKE", "%$keyword%")
                    ->orWhere("description", "LIKE", "%$keyword%");
            });
        }

        $records = $records->get();

        return response()->json(["code" => 200, "data" => $records, "total" => count($records)]);
    }

    
    public function saveCancellation(Request $request)
    {
        $id = $request->get("id", 0);
        $checkCacellation = ProductCancellationPolicie::find($id);
        if($checkCacellation != null){
            $checkCacellation->store_website_id = $request->store_website_id;
            $checkCacellation->days_cancelation = $request->days_cancelation;
            $checkCacellation->days_refund = $request->days_refund;
            $checkCacellation->percentage = $request->percentage;
            $checkCacellation->update();
        }else{
            $checkCacellation = new ProductCancellationPolicie();
            $checkCacellation->store_website_id = $request->store_website_id;
            $checkCacellation->days_cancelation = $request->days_cancelation;
            $checkCacellation->days_refund = $request->days_refund;
            $checkCacellation->percentage = $request->percentage;
            $checkCacellation->save();
        }
        return response()->json(["code" => 200, "data" => $checkCacellation]);
    }
    public function savelogwebsiteuser($log_case_id,$id,$username,$userEmail,$firstName,$lastName,$password,$website_mode,$msg)
    {
        $log = New LogStoreWebsiteUser();
        $log->log_case_id = $log_case_id;
        $log->store_website_id = $id;
        $log->username = $username;
        $log->username = $username;
        $log->useremail = $userEmail;
        $log->first_name = $firstName;
        $log->last_name = $lastName;
        $log->password = $password;
        $log->website_mode = $website_mode;
        $log->log_msg = $msg;
        $log->save();
    }

    /**
     * records Page
     * @param  Request $request [description]
     * @return
     */
    public function save(Request $request)
    {
        $post = $request->all();     
        $validator = Validator::make($post, [
            'title'   => 'required',
            'website' => 'required',
			'product_markup' => 'required'
        ]);

        if ($validator->fails()) {
            $outputString = "";
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . "<br>";
                }
            }
            return response()->json(["code" => 500, "error" => $outputString]);
        }

        $id = $request->get("id", 0);

        $records = StoreWebsite::find($id);

        if (!$records) {
            $records = new StoreWebsite;
        }else{
            if($request->is_debug_true){
                if(!$request->server_ip){
                    $outputString = 'Server IP is required to enable db logs';  
                    return response()->json(["code" => 500, "error" => $outputString]);
                }
                dd($records->is_debug_true);
                if($records->is_debug_true !== $request->is_debug_true){
                    $this->enableDBLog($request);
                }  
            }
        }
       
        $records->fill($post);

        $records->save();

		if(isset($post['username'])) {
			$this->savelogwebsiteuser("#1",$post['id'],$post['username'],$post['userEmail'],$post['firstName'],$post['lastName'],$post['password'],$post['website_mode'],"For this Website ".$post['id']." ,A new user has been created.");
		}
        if($request->staging_username && $request->staging_password) {
            $message = 'Staging Username: '.$request->staging_username.', Staging Password is: ' . $request->staging_password;
            $params['user_id'] = Auth::id();
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
        }
        
        if($request->mysql_username && $request->mysql_password) {
            $message = 'Mysql Username: '.$request->mysql_username.', Mysql Password is: ' . $request->mysql_password;
            $params['user_id'] = Auth::id();
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
        }

        if($request->mysql_staging_username && $request->mysql_staging_password) {
            $message = 'Mysql Staging Username: '.$request->mysql_staging_username.', Mysql Staging Password is: ' . $request->mysql_staging_password;
            $params['user_id'] = Auth::id();
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
        }

        

		if($id == 0) {
			$siteDevelopmentCategories  =  SiteDevelopmentCategory::all();
			foreach ($siteDevelopmentCategories as $develop) {
                $site = new SiteDevelopment;
                $site->site_development_category_id = $develop->id;
                $site->site_development_master_category_id = $develop->master_category_id;
                $site->website_id = $records->id;
                $site->save();
            }
		}
        return response()->json(["code" => 200, "data" => $records]);
    }

    public function saveUserInMagento(Request $request) {
        
        $post = $request->all();
        $validator = Validator::make($post, [
            'username'   => 'required',
            'firstName'   => 'required',
            'lastName'   => 'required',
            'userEmail'   => 'required',
            'password' => 'required',
            'websitemode' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = "";
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . "<br>";
                }
            }
            return response()->json(["code" => 500, "error" => $outputString]);
        }

        $this->savelogwebsiteuser("#2",$post['store_id'],$post['username'],$post['userEmail'],$post['firstName'],$post['lastName'],$post['password'],$post['websitemode'],"For this Website ".$post['store_id']." ,A user has been updated.");

        $checkUserNameExist = '';
        if(!empty($post['store_website_userid'])) {
            $checkUserExist = StoreWebsiteUsers::where('store_website_id',$post['store_id'])->where('is_deleted',0)->where('email',$post['userEmail'])->where('id','<>',$post['store_website_userid'])->first();
            if(empty($checkUserExist)) {
                $checkUserNameExist = StoreWebsiteUsers::where('store_website_id',$post['store_id'])->where('is_deleted',0)->where('username',$post['username'])->where('id','<>',$post['store_website_userid'])->first();
            }
        } else {
            $checkUserExist = StoreWebsiteUsers::where('store_website_id',$post['store_id'])->where('is_deleted',0)->where('email',$post['userEmail'])->first();
            if(empty($checkUserExist)) {
                $checkUserNameExist = StoreWebsiteUsers::where('store_website_id',$post['store_id'])->where('is_deleted',0)->where('username',$post['username'])->first();
            }
        }
        
        if(!empty($checkUserExist)) {
            return response()->json(["code" => 500, "error" => "User Email already exist!"]);
        }
        if(!empty($checkUserNameExist)) {
            return response()->json(["code" => 500, "error" => "Username already exist!"]);
        }

        $uppercase = preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9_@.\/#&+-]+)$/', $post['password']);
        if( !$uppercase || strlen( $post['password']) < 7)
        {
            return response()->json(["code" => 500, "error" => "Your password must be at least 7 characters.Your password must include both numeric and alphabetic characters."]);
        }

        $storeWebsite = StoreWebsite::find($post['store_id']);
        if(!empty($post['store_website_userid'])) {
            $getUser = StoreWebsiteUsers::where('id',$post['store_website_userid'])->first();
            $getUser->first_name = $post['firstName'];
            $getUser->last_name = $post['lastName'];
            $getUser->email = $post['userEmail'];
            $getUser->password = $post['password'];
            $getUser->website_mode = $post['websitemode'];
            $getUser->save();

            StoreWebsiteUserHistory::create([
                'store_website_id' => $getUser->store_website_id,
                'store_website_user_id' => $getUser->id,
                'model' => 'App\StoreWebsiteUsers',
                'attribute' => "username_password",
                'old_value' => 'updated',
                'new_value' => 'updated',
                'user_id' => Auth::id(),
            ]);


            if($getUser->is_deleted == 0){
                $magentoHelper = new MagentoHelperv2();
                $result = $magentoHelper->updateMagentouser($storeWebsite, $post);
                return response()->json(["code" => 200, "messages" => 'User details updated Sucessfully']);
            }else{
                return response()->json(["code" => 200, "messages" => 'User details updated Sucessfully']);
            }

            


        } else {
            $params['username'] = $post['username'];
            $params['first_name'] = $post['firstName'];
            $params['last_name'] = $post['lastName'];
            $params['email'] = $post['userEmail'];
            $params['password'] = $post['password'];
            $params['store_website_id'] = $post['store_id'];
            $params['website_mode'] = $post['websitemode'];
            
            $StoreWebsiteUsersid = StoreWebsiteUsers::create($params);

            if($post['userEmail'] && $post['password']) {
                $message = 'Email: '.$post['userEmail'].', Password is: ' . $post['password'];
                $params['user_id'] = Auth::id();
                $params['message'] = $message;
                $chat_message = ChatMessage::create($params);
            }

            $magentoHelper = new MagentoHelperv2();
            $result = $magentoHelper->addMagentouser($storeWebsite, $post);
            
            StoreWebsiteUserHistory::create([
                'store_website_id' => $StoreWebsiteUsersid->store_website_id,
                'store_website_user_id' => $StoreWebsiteUsersid->id,
                'model' => 'App\StoreWebsiteUsers',
                'attribute' => "username_password",
                'old_value' => 'new_added',
                'new_value' => 'new_added',
                'user_id' => Auth::id(),
            ]);    


            return response()->json(["code" => 200, "messages" => 'User details saved Sucessfully']);
        }
    }

    public function deleteUserInMagento(Request $request) {
        
        $post     = $request->all();
        $getUser  = StoreWebsiteUsers::where('id',$post['store_website_userid'])->first();
        $username = $getUser->username;
        $getUser->is_deleted = 1;
        $getUser->save();


        $this->savelogwebsiteuser("#3",$getUser['store_website_id'],$getUser['username'],$getUser['email'],$getUser['first_name'],$getUser['last_name'],$getUser['password'],$getUser['website_mode'],"For this Website ".$getUser['store_website_id']." ,User has been Deleted.");

        $storeWebsite = StoreWebsite::find($getUser->store_website_id);
        
        $magentoHelper = new MagentoHelperv2();
        $result = $magentoHelper->deleteMagentouser($storeWebsite, $username);
        

        StoreWebsiteUserHistory::create([
            'store_website_id' => $getUser->store_website_id,
            'store_website_user_id' => $getUser->id,
            'model' => 'App\StoreWebsiteUsers',
            'attribute' => "username_password",
            'old_value' => 'delete',
            'new_value' => 'delete',
            'user_id' => Auth::id(),
        ]);    

        return response()->json(["code" => 200, "messages" => 'User Deleted Sucessfully']);
    }

    /**
     * Edit Page
     * @param  Request $request [description]
     * @return
    */
    public function edit(Request $request, $id)
    {
        $storeWebsite = StoreWebsite::where("id", $id)->first();
        $services = Service::get();
        //->where('is_deleted',0)

        $storewebsiteusers = StoreWebsiteUsers::where('store_website_id',$id)->get();
        
        if ($storeWebsite) {
            return response()->json([
                "code" => 200, 
                "data" => $storeWebsite,
                "userdata" => $storewebsiteusers, 
                "services" => $services,
                "totaluser" => count($storewebsiteusers)]
            );
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }

    public function editCancellation(Request $request, $id)
    {
        $storeWebsite = ProductCancellationPolicie::where("store_website_id", $id)->first();
       // $storewebsiteusers = StoreWebsiteUsers::where('store_website_id',$id)->where('is_deleted',0)->get();
        if ($storeWebsite) {
            return response()->json(["code" => 200, "data" => $storeWebsite]);
        }

        return response()->json(["code" => 200, "data" => ["store_website_id"=> $id]]);
    }

    /**
     * delete Page
     * @param  Request $request [description]
     * @return
     */

    public function delete(Request $request, $id)
    {
        $storeWebsite = StoreWebsite::where("id", $id)->first();

        if ($storeWebsite) {
            $storeWebsite->delete();
            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);
    }

    public function updateSocialRemarks(Request $request , $id)
    {

        $storeWebsite = StoreWebsite::where("id", $id)->first();

        if ($storeWebsite) {

            $facebook_remarks = $request->get("facebook_remarks");
            if(!empty($facebook_remarks)) {
                $storeWebsite->facebook_remarks = $facebook_remarks;               
            }

            $instagram_remarks = $request->get("instagram_remarks");
            if(!empty($instagram_remarks)) {
                $storeWebsite->instagram_remarks = $instagram_remarks;               
            }
            
            $storeWebsite->save();

            return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500, "error" => "Wrong site id!"]);

    }

    public function socialStrategy($id, Request $request)
    {
        $website = StoreWebsite::find($id);
        $subjects = SocialStrategySubject::orderBy('id', 'desc');

        if ($request->k != null) {
            $subjects = $subjects->where("title", "like", "%" . $request->k . "%");
        }

        $subjects = $subjects->paginate(Setting::get('pagination'));
        foreach($subjects as $subject) {
            $subject->strategy = SocialStrategy::where('social_strategy_subject_id',$subject->id)->where('website_id',$id)->first();
        }
        $users = User::select('id', 'name')->get();

        if ($request->ajax() && $request->pagination == null) {
            return response()->json([
                'tbody' => view('storewebsite::social-strategy.partials.data', compact('subjects', 'users', 'website'))->render(),
                'links' => (string) $subjects->render(),
            ], 200);
        }

        return view('storewebsite::social-strategy.index', compact('subjects', 'users', 'website'));
    }

    public function submitSubject(Request $request) {
        if ($request->text) {
        $subjectCheck = SocialStrategySubject::where('title', $request->text)->first();

        if (empty($subjectCheck)) {
            $subject        = new SocialStrategySubject;
            $subject->title = $request->text;
            $subject->save();

            return response()->json(["code" => 200, "messages" => 'Subject Saved Sucessfully']);

        } else {

            return response()->json(["code" => 500, "messages" => 'Subject Already Exist']);
        }

        } else {
            return response()->json(["code" => 500, "messages" => 'Please Enter Text']);
        }
    }


    public function submitStrategy($id,Request $request) {


       $store_strategy = SocialStrategy::where('social_strategy_subject_id',$request->subject)->where('website_id',$request->site)->first();

        if(!$store_strategy) {
            $store_strategy = new SocialStrategy;
        }
        if ($request->type == 'description') {
            $store_strategy->description = $request->text;
        }


        if ($request->type == 'execution') {
            $store_strategy->execution_id = $request->text;
        }

        if ($request->type == 'content') {
            $store_strategy->content_id = $request->text;
        }


        $store_strategy->social_strategy_subject_id = $request->subject;
        $store_strategy->website_id   = $request->site;

        $store_strategy->save();

        return response()->json(["code" => 200, "messages" => 'Social strategy Saved Sucessfully']);
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
        $strategy      = null;
        $documents = $request->input('document', []);
        if (!empty($documents)) {
            if ($request->id) {
                $strategy = SocialStrategy::find($request->id);
            }

            if (!$strategy || $request->id == null) {
                $strategy                               = new SocialStrategy;
                $strategy->description                  = "";
                $sistrategyte->website_id                   = $request->store_website_id;
                $strategy->social_strategy_subject_id = $request->site_development_subject_id;
                $strategy->save();
            }

            foreach ($request->input('document', []) as $file) {
                $path  = storage_path('tmp/uploads/' . $file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('site-development/' . floor($strategy->id / config('constants.image_per_folder')))
                    ->upload();
                $strategy->attachMedia($media, config('constants.media_tags'));
            }

            return response()->json(["code" => 200, "data" => [], "message" => "Done!"]);
        } else {
            return response()->json(["code" => 500, "data" => [], "message" => "No documents for upload"]);
        }

    }


    public function listDocuments(Request $request, $id)
    {
        $site = SocialStrategy::find($request->id);

        $userList = [];

        if ($site->execution_id) {
            $userList[$site->execution->id] = $site->execution->name;
        }

        if ($site->content_id) {
            $userList[$site->content->id] = $site->content->name;
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
                        "id"        => $media->id,
                        'url'       => $media->getUrl(),
                        'site_id'   => $site->id,
                        'user_list' => $usrSelectBox,
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
        if ($request->id != null && $request->site_id != null && $request->user_id != null) {
            $media        = \Plank\Mediable\Media::find($request->id);
            $user         = \App\User::find($request->user_id);
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
            }else{
                return response()->json(["code" => 200, "message" => "User or site is not available"]);
            }
        }

        return response()->json(["code" => 200, "message" => "Sorry required fields is missing like id, siteid , userid"]);
    }


    public function remarks(Request $request, $id)
    {
        $response = \App\SocialStrategyRemark::join("users as u","u.id","social_strategy_remarks.user_id")->where("social_strategy_id",$request->id)
        ->select(["social_strategy_remarks.*",\DB::raw("u.name as created_by")])
        ->orderBy("social_strategy_remarks.created_at","desc")
        ->get();
        return response()->json(["code" => 200 , "data" => $response]);
    }

    public function saveRemarks(Request $request, $id)
    {
        \App\SocialStrategyRemark::create([
            "remarks" => $request->remark,
            "social_strategy_id" => $request->id,
            "user_id" => \Auth::user()->id,
        ]);

        $response = \App\SocialStrategyRemark::join("users as u","u.id","social_strategy_remarks.user_id")->where("social_strategy_id",$request->id)
        ->select(["social_strategy_remarks.*",\DB::raw("u.name as created_by")])
        ->orderBy("social_strategy_remarks.created_at","desc")
        ->get();
        return response()->json(["code" => 200 , "data" => $response]);

    }



    
    public function viewSubject(Request $request) {
        $subject = SocialStrategySubject::find($request->id);
        return response()->json(["code" => 200 , "data" => $subject]);
    }

    public function submitSubjectChange(Request $request, $id) {
        $subject = SocialStrategySubject::find($request->id);
        $subject->title = $request->subject_title;
        $subject->save();
        return response()->json(["code" => 200 , "message" => 'Successful']);
    }

    public function generateStorefile(Request $request)
    {
        $server = $request->get("for_server");

        $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'pem-generate.sh '.$server.' 2>&1';

        $allOutput   = array();
        $allOutput[] = $cmd;
        $result      = exec($cmd, $allOutput);

        \Log::info(print_r($allOutput,true));

        $string  = [];
        if(!empty($allOutput)) {
            $continuetoFill = false;
            foreach($allOutput as $ao) {
                if($ao == "-----BEGIN RSA PRIVATE KEY-----" || $continuetoFill) {
                   $string[] = $ao;
                   $continuetoFill = true; 
                }
            }
        }

        $content = implode("\n",$string);

        $nameF = $server.".pem";
        
        //header download
        header("Content-Disposition: attachment; filename=\"" . $nameF . "\"");
        header("Content-Type: application/force-download");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header("Content-Type: application/x-pem-file");

        echo $content;
        die;
    }

    public function magentoUserList(Request $request)
    {
        $users = StoreWebsiteUsers::where('is_deleted',0)->get();
        return response()->json(["code" => 200, "data" => $users]);
    }

    public function userHistoryList(Request $request)
    {
        $histories = StoreWebsiteUserHistory::with('websiteuser','storewebsite')
            ->where('store_website_id',$request->id)
            ->latest()
            ->get();

        $resultArray = [];

        foreach($histories as $history){
            $resultArray[] = [
                'date'         => $history->created_at->format('Y-m-d H:i:s'),
                'website_mode' => $history->websiteuser->website_mode,
                'username'     => $history->websiteuser->username,
                'first_name'   => $history->websiteuser->first_name,
                'last_name'    => $history->websiteuser->last_name,
                'action'       => $history->new_value,
            ];
        }

        return response()->json(["code" => 200, "data" => $resultArray]);
    }
    
    public function storeReindexHistory(Request $request){
        
        $website = StoreWebsite::find($request->id);
        $date = Carbon::now()->subDays(7);
        $histories = StoreReIndexHistory::where('server_name',$website->title)->where('created_at','>=',$date)
            ->latest()
            ->get();

        $resultArray = [];

        foreach($histories as $history){
            $resultArray[] = [
                'date'         => $history->created_at->format('Y-m-d H:i:s'),
                'server_name'  => $history->server_name,
                'username'     => $history->username,
                'action'       => $history->action,
            ];
        }

        return response()->json(["code" => 200, "data" => $resultArray]);
        
    }
    
    
    /**
     * Build Process Page
     * @param  Request $request [description]
     * @return
    */
    public function buildProcess(Request $request, $id) {
        $storeWebsite = StoreWebsite::where("id", $id)->first();
        if ($storeWebsite) {
            return response()->json([
                "code" => 200, 
                "data" => $storeWebsite,                
            ]);
        }
        return response()->json(["code" => 500, "error" => "Wrong site id!"]);        
    }
    
    public function buildProcessSave(Request $request){
        
        $post = $request->all();
        
        $validator = Validator::make($post, [
            'reference' => 'required',
            'repository' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = "";
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . "<br>";
                }
            }            
            
            return response()->json(["code" => 500, "error" => "Please fill required fields."]);            
        }
       
        if(!empty($request->store_website_id)){
            
            $StoreWebsite = StoreWebsite::find($request->store_website_id);
            
            if($StoreWebsite != null){
                
                $StoreWebsite->build_name = $request->repository;
                $StoreWebsite->repository = $request->repository;
                $StoreWebsite->reference = $request->reference;
                $StoreWebsite->update();
                
                if($StoreWebsite):
                    
                    $jobName = $request->repository;
                    $repository = $request->repository;
                    $ref = $request->reference;
                    $staticdep = 1;
                    
                    $jenkins = new \JenkinsKhan\Jenkins('http://apibuild:117ed14fbbe668b88696baa43d37c6fb48@build.theluxuryunlimited.com:8080'); 
                    $jenkins->launchJob($jobName, ['repository'=>$repository,'ref'=>$ref,'staticdep' => 0]);           
                    if($jenkins->getJob($jobName)):
					$job = $jenkins->getJob($jobName);
					$builds = $job->getBuilds();
						$buildDetail = 'Build Name: '.$jobName . '<br> Build Repository: '.$repository.'<br> Reference: '.$ref;
						$record = ['store_website_id'=>$request->store_website_id, 'created_by'=> Auth::id(), 'text'=>$buildDetail, 'build_name'=>$jobName,'build_number'=>$builds[0]->getNumber()];
						BuildProcessHistory::create($record);
                        return response()->json(["code" => 200, "error" => "Process builed complete successfully."]);
                    else:
                        return response()->json(["code" => 500, "error" => "Please try again, Jenkins job not created"]);
                    endif;
                    
                endif;
                                
            }
            
            return response()->json(["code" => 500, "error" => "Please fill required fields."]);
            
        }        
    }
	
    /**
     * This function is use to add company website address.
     *
     * @param Request $request
     * @param int $store_website_id 
     * @return JsonResponce
     */
	public function addCompanyWebsiteAddress(Request $request, $store_website_id) 
    {
        $StoreWebsite = StoreWebsite::where('id', '=', $store_website_id)->first();
        if ($StoreWebsite != null ) {
		    return response()->json([
                "code" => 200, 
                "data" => $StoreWebsite,                
            ]);
        }
        return response()->json(["code" => 500, "error" => "Wrong site id!"]);     
	}
	
    /**
     * This function is use to Update company's website address.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateCompanyWebsiteAddress(Request $request) 
    {
        $post = $request->all();
            $validator = Validator::make($post, [
            'website_address' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = "";
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . "<br>";
                }
            }            
            return response()->json(["code" => 500, "error" => "Please fill required fields."]);            
        }
       
        if(!empty($request->store_website_id)){
            $StoreWebsite = StoreWebsite::find($request->store_website_id);
            if ($StoreWebsite != null) {
                $StoreWebsite->website_address = $request->website_address;
                $StoreWebsite->update();
                return response()->json(["code" => 200 , "message" => "Address has been saved"]);
            }
            return response()->json(["code" => 500, "error" => "Please fill required fields."]);
        }  
    }

	public function syncStageToMaster($storeWebId) {
		$websiteDetails = StoreWebsite::where('id', $storeWebId)->select('server_ip', 'repository_id')->first();
		if($websiteDetails != null and $websiteDetails['server_ip'] != null and $websiteDetails['repository_id'] != null) {
			$repo = GithubRepository::where('id', $websiteDetails['repository_id'])->pluck('name')->first();
			if($repo != null) {
				$cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'sync-staticfiles.sh -r '.$repo.' -s '.$websiteDetails['server_ip'];
				$allOutput = array(); 
				$allOutput[] = $cmd; 
				$result = exec($cmd, $allOutput); //Execute command
				\Log::info(print_r(["Command Output",$allOutput],true));
				return response()->json(["code" => 200 , "message" => "Command executed"]);
			} else {
				return response()->json(["code" => 500 , "message" => "Repository Not found."]);
			}
		} else {
			return response()->json(["code" => 500 , "message" => "Request has been failed."]);
		}
	}


    public function enableDBLog($website){
        $cmd = "bash " . getenv('DEPLOYMENT_SCRIPTS_PATH') . "magento-debug.sh --server ".$website->server_ip." --debug ".($website->is_debug_true ? 'true' : 'false')." 2>&1";
        \Log::info('[SatyamTest] '.$cmd);
        $allOutput   = array();
        $allOutput[] = $cmd;
        $result      = exec($cmd, $allOutput);
        \Log::info(print_r($allOutput,true));
        return $result;
    }
    public function checkMagentoToken(Request $request){
        $token = $request->id;
        $magentoHelper = new MagentoHelperv2();
        $result = $magentoHelper->checkToken($token,$request->url);
        if($result){
            return response()->json(["code" => 200 , "message" => "Token is valid"]);
        }else{
            return response()->json(["code" => 500 , "message" => "Token is invalid"]);
        }

    }

}
