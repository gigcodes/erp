<?php

namespace App\Http\Controllers;

use Auth;
use App\StoreWebsite;
use App\ChatMessage;
use App\StoreWebsiteUsers;
use App\LogStoreWebsiteUser;
use Illuminate\Http\Request;
use App\ProductPushErrorLog;
use App\StoreWebsiteUserHistory;
use Illuminate\Support\Facades\Validator;
use seo2websites\MagentoHelper\MagentoHelperv2;

class MagentoUserFromErpController extends Controller
{
    public function index(Request $request)
    {
        $storeWebsites = StoreWebsiteUsers::select(
            'store_websites.id as store_website_id',
            'store_website_users.username', 
            'store_website_users.password', 
            'store_websites.website',
            'store_websites.title',
            'store_website_users.website_mode',
            'store_websites.store_code_id',
            'store_websites.created_at',
            'store_websites.magento_url',
            'store_website_users.user_role',
            'store_website_users.is_active',
            'store_website_users.id',
        )->join('store_websites', function ($q)  {
            $q->on('store_websites.id', '=', 'store_website_users.store_website_id');
            $q->where('store_websites.website_source','=','magento');
        })
        ->where('store_website_users.is_deleted', 0)
        ->whereNull('store_websites.deleted_at')
        ->groupBy('store_website_users.username','store_websites.website','store_website_users.website_mode','store_websites.store_code_id')
        ->orderBy('store_website_users.id','DESC');
        
        //Apply store_website_id if exists
        if($request->get('store_website_id')) {
            $storeWebsites->where('store_websites.id', $request->get('store_website_id'));
        }

        //Apply username if exists
        if($request->get('username')) {
            $storeWebsites->where('store_website_users.username', $request->get('username'));
        }

        //show 20 records per page
        $storeWebsites = $storeWebsites->paginate(20);

        //For select website filter list
        $allStoreWebsites = StoreWebsite::where('website_source','=','magento')
        ->whereNotNull('magento_username')
        ->whereNull('deleted_at')
        ->pluck('website','id')
        ->toArray();
        
        //For select role filter list 
        $magentoRoles = [];

        return view('magento-user-from-erp.index', compact('storeWebsites','magentoRoles', 'allStoreWebsites'));
    }

     /**
     * Create User.
     *
     * @param  Request  $request [description]
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function magentoUserCreate(Request $request)
    {
        
        $post = $request->all();
        $validator = Validator::make($post, [
            'userName' => 'required',
            'userEmail' => 'required|email',
            'firstName' => 'required',
            'lastName' => 'required',
            'webSite' => 'required',
            'userRole' => 'required',
            'websiteMode' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . '<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $storeWebsites = StoreWebsite::where('id', '=', $post['webSite'])->orWhere('parent_id', '=', $post['webSite'])->get();
        $count = 0;
        foreach ($storeWebsites as $key => $storeWebsite) {
            $this->savelogwebsiteuser('#2', $storeWebsite->id, $post['userName'], $post['userEmail'], $post['firstName'], $post['lastName'], $post['password'], $post['websitemode'], 'For this Website ' . $storeWebsite->id . ' ,A user has been created with specific roles.');

            $checkUserNameExist = '';
            if (! empty($post['store_website_userid'])) {
                $checkUserExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('email', $post['userEmail'])->where('id', '<>', $post['store_website_userid'])->first();
                if (empty($checkUserExist)) {
                    $checkUserNameExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('username', $post['userName'])->where('id', '<>', $post['store_website_userid'])->first();
                }
            } else {
                $checkUserExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('email', $post['userEmail'])->first();
                if (empty($checkUserExist)) {
                    $checkUserNameExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('username', $post['userName'])->first();
                }
            }

            if (! empty($checkUserExist)) {
                return response()->json(['code' => 500, 'error' => 'User Email already exist!']);
            }
            if (! empty($checkUserNameExist)) {
                return response()->json(['code' => 500, 'error' => 'Username already exist!']);
            }

            $uppercase = preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9_@.\/#&+-]+)$/', $post['password']);
            if (! $uppercase || strlen($post['password']) < 7) {
                return response()->json(['code' => 500, 'error' => 'Your password must be at least 7 characters.Your password must include both numeric and alphabetic characters.']);
            }

            $params['username'] = $post['userName'];
            $params['email'] = $post['userEmail'];
            $params['first_name'] = $post['firstName'];
            $params['last_name'] = $post['lastName'];
            $params['store_website_id'] = $post['webSite'];
            $params['user_role'] = $post['userRole'];
            $params['website_mode'] = $post['websiteMode'];
            $params['password'] = $post['password'];
            $params['is_active'] = 1;

            $StoreWebsiteUsersid = StoreWebsiteUsers::create($params);

            if ($post['userEmail'] && $post['password']) {
                $message = 'Email: ' . $post['userEmail'] . ', Password is: ' . $post['password'];
                $params['user_id'] = Auth::id();
                $params['message'] = $message;
                ChatMessage::create($params);
            }

            $magentoHelper = new MagentoHelperv2();
            $magentoHelper->addMagentouser($storeWebsite, $post);

            StoreWebsiteUserHistory::create([
                'store_website_id' => $StoreWebsiteUsersid->store_website_id,
                'store_website_user_id' => $StoreWebsiteUsersid->id,
                'model' => \App\StoreWebsiteUsers::class,
                'attribute' => 'username_password',
                'old_value' => 'new_added',
                'new_value' => 'new_added',
                'user_id' => Auth::id(),
            ]);
            $count++;
        }
        if ($count == $key + 1) {
            return response()->json(['code' => 200, 'messages' => 'User details saved Successfully']);
        } else {
            return response()->json(['code' => 500, 'messages' => 'Something went wrong']);
        }
    }

    /**
     * Save log webstie.
     *@return \Illuminate\Http\JsonResponse|void
     */
    public function savelogwebsiteuser($log_case_id, $id, $username, $userEmail, $firstName, $lastName, $password, $website_mode, $msg)
    {
        $log = new LogStoreWebsiteUser();
        $log->log_case_id = $log_case_id;
        $log->store_website_id = $id;
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
     * Get roles.
     *
     * @param  Request  $request [description]
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function getRoles(Request $request)
    {
        //For select website filter list
        $magentStoreData = StoreWebsite::where([
            ['website_source', '=', 'magento'],
            ['id', '=', $request->website_id],
        ])
        ->whereNotNull('magento_username')
        ->whereNull('deleted_at')
        ->first();
        
        $magento_url = $magentStoreData['magento_url'];

        if( !empty ( $magento_url ) ){

            $config = array(
                'shop_url' => $magentStoreData['magento_url'],
                'username' => $magentStoreData['magento_username'],
                'password' => $magentStoreData['magento_password'],
            );
    
            $url = $config['shop_url'] . "/rest/V1/integration/admin/token";
    
            $data = "{ \"username\": \"" . $config['username'] . "\", \"password\": \"" . $config['password'] . "\"}";
    
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'accept: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpcode != 200) {
                ProductPushErrorLog::log($url, 0, $response, 'error', $magentStoreData['id']);
            }
            if (!$response) {
    
                return false;
            }
    
            echo $token = str_ireplace('"', '', $response); die;

            $client = new \GuzzleHttp\Client();
            $endpoint = rtrim($magento_url, '/').'/rest/all/V1/adminroles';
            $headers = [
                'Authorization' => 'Bearer ' . $magentStoreData['api_token'],        
                'Accept'        => 'application/json',
            ];
            $response = $client->request('GET', $endpoint, [
                'headers' => $headers
            ]);
            $content = $response->getBody();
            echo "<pre>";print_r($content);die;
        }else{
            return response()->json(['code' => 500, 'error' => 'Not found store url.']);
        }
        
        return response()->json(['code' => 200, 'messages' => 'Store url', 'data' => $magentStoreData]);
    }

    /**
     * Handle account status.
     *
     * @param  Request  $request [description]
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function accountStatus(Request $request)
    {
        $status = $request->get('status');
        $update_id = $request->get('update_id');
        $StoreWebsiteUsers = StoreWebsiteUsers::find($update_id);
        $StoreWebsiteUsers->is_active = ($status == 'true') ? 1 : 0;
        $StoreWebsiteUsers->save();

        return response()->json(['code' => 200, 'messages' => 'Status updated.']);

    }
}
