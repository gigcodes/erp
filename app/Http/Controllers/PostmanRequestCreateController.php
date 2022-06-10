<?php

namespace App\Http\Controllers;

use App\PostmanRequestCreate;
use App\PostmanFolder;
use App\PostmanHistory;
use App\Setting;
use App\User;
use Illuminate\Http\Request; 
use App\PostmanRequestHistory;
use App\PostmanResponse;
use App\PostmanRequestJsonHistory;

class PostmanRequestCreateController extends Controller
{

    public function createPostmanHistory($postmanId,$type){
        try {
            $postHis = new PostmanHistory();
            $postHis->user_id = \Auth::user()->id;
            $postHis->postman_id = $postmanId;
            $postHis->type = $type;
            $postHis->save();
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $postmans = PostmanRequestCreate::select('postman_request_creates.*', 'pf.name', 'postman_responses.response', 'postman_responses.id AS resId')
        ->leftJoin('postman_folders AS pf', 'pf.id', 'postman_request_creates.folder_name')
        ->leftJoin('postman_responses', function($query) 
        {
            $query->on('postman_responses.request_id','=','postman_request_creates.id')->orderBy('postman_request_creates.id', 'DESC');
            //->whereRaw('answers.id IN (select MAX(a2.id) from answers as a2 join users as u2 on u2.id = a2.user_id group by u2.id)');
        })
        //->get();
        ->groupBy('postman_request_creates.id')
        ->orderBy('postman_request_creates.id', 'DESC')
        ->paginate(Setting::get('pagination'));
        //dd($postmans);
        $folders = PostmanFolder::all();
        $users = User::all();
        $userID = \Auth::user()->id;
        $userAdmin = User::where('id', $userID)->first();
        $addAdimnAccessID = '';
        if($userAdmin->isAdmin()){
            $addAdimnAccessID = $userID;
        }
        
        return view("postman.index", compact('postmans', 'folders', 'users', 'userID', 'addAdimnAccessID'));
    }

    public function folderIndex()
    {
        $folders = PostmanFolder::paginate(Setting::get('pagination'));
        return view("postman.folder", compact('folders'));
    }

    public function search(Request $request)
    {
        $postmans = new PostmanRequestCreate();
        if (!empty($request->folder_name)) {
            $postmans = $postmans->where("folder_name", "like", "%".$request->folder_name."%");
        }
        if (!empty($request->request_type)) {
            $postmans = $postmans->where("request_type", "like", "%".$request->request_type."%");
        }
        if (!empty($request->request_name)) {
            $postmans = $postmans->where("request_name", "like", "%".$request->request_name."%");
        }
        $postmans = $postmans->select('postman_request_creates.*', 'pf.name')->leftJoin('postman_folders AS pf', 'pf.id', 'postman_request_creates.folder_name')->paginate(Setting::get('pagination'));
        $folders = PostmanFolder::all();
        $userID = \Auth::user()->id;
        $userAdmin = User::where('id', $userID)->first();
        $addAdimnAccessID = '';
        if($userAdmin->isAdmin()){
            $addAdimnAccessID = $userID;
        }return view("postman.index", compact('postmans', 'folders', 'users', 'userID', 'addAdimnAccessID'));
    }

    public function folderSearch(Request $request)
    {
        $folders = new PostmanFolder();
        if (!empty($request->folder_name)) {
            $folders = $folders->where("name", "like", "%".$request->folder_name."%");
        }
        
        $folders = $folders->paginate(Setting::get('pagination'));
        
        return view("postman.folder", compact('folders'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $created_user_permission = '';
            if(isset($request->id) && $request->id > 0){
                $postman = PostmanRequestCreate::find($request->id);
                $type = 'Update';
                if($postman->body_json != $request->body_json){
                    $jsonVersion = PostmanRequestJsonHistory::create(
                        [
                            'user_id' => \Auth::user()->id,
                            'request_id' => $request->id,
                            'request_data' => $request->body_json,
                        ]
                    );
                    //dd($jsonVersion->id);
                    PostmanRequestJsonHistory::where('id', $jsonVersion->id)->update(['version_json' => 'v'.$jsonVersion->id]);
                    
                }
            } else {
                $postman = new PostmanRequestCreate();
                $type = 'Created';
                $created_user_permission = ','.\Auth::user()->id;
               // $this->updatePostmanCollectionAPI($request);
            }
            $postman->folder_name = $request->folder_name;
            $postman->request_name = $request->request_name;
            $postman->request_type = $request->request_types;
            $postman->request_url = $request->request_url;
            $postman->params = $request->params;
            $postman->authorization_type = $request->authorization_type;
            $postman->authorization_token = $request->authorization_token;
            $postman->request_headers = $request->request_headers;
            $postman->body_type = $request->body_type;
            $postman->body_json = $request->body_json;
            $postman->pre_request_script = $request->pre_request_script;
            $postman->tests = $request->tests;
            $postman->user_permission = implode(",",$request->user_permission).$created_user_permission;
            $postman->save();
            $this->createPostmanHistory($postman->id, $type);
            if($type == 'Created'){
                
                $this->createPostmanFolder($postman->folder_name, $request->folder_real_name);
                $this->createPostmanRequestAPI($postman->folder_name,$request);
            }

            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }

    }

    public function jsonVersion(Request $request){
        try{
            $jsonVersion = PostmanRequestJsonHistory::create(
                [
                    'user_id' => \Auth::user()->id,
                    'request_id' => $request->id,
                    'request_data' => $request->json_data,
                ]
            );
            PostmanRequestJsonHistory::where('id', $jsonVersion->id)->update(['version_json' => 'v'.$jsonVersion->id]);
            $jsonVersion = PostmanRequestJsonHistory::where('id', $jsonVersion->id)->first();
            return response()->json(['code' => 200, 'data' => $jsonVersion, 'message' => 'Added successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
    public function folderStore(Request $request)
    {
        try{
            if(isset($request->id) && $request->id > 0)
                $folder = PostmanFolder::find($request->id);
            else
                $folder = new PostmanFolder();
            $folder->name = $request->folder_name;
            $folder->save();
            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PostmanRequestCreate  $postmanRequestCreate
     * @return \Illuminate\Http\Response
     */
    public function edit(PostmanRequestCreate $postmanRequestCreate, Request $request)
    {
        try{
            $postman = PostmanRequestCreate::find($request->id);
            $ops = '';
            $folders = PostmanFolder::all();
            foreach($folders as $folder){
                $selected = '';
                if($postman->folder_name == $folder->id)
                    $selected = 'selected';
                $ops .= '<option value="'.$folder->id.'" '.$selected.'>'.$folder->name.'</option>';
            }
            
            return response()->json(['code' => 200, 'data' => $postman, 'ops' => $ops, 'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function folderEdit(PostmanFolder $postmanFolder, Request $request)
    {
        try{
            $folders = PostmanFolder::find($request->id);
            return response()->json(['code' => 200, 'data' => $folders,'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PostmanRequestCreate  $postmanRequestCreate
     * @return \Illuminate\Http\Response
     */
    public function destroy(PostmanRequestCreate $postmanRequestCreate, Request $request)
    {
        try{
            $postman = PostmanRequestCreate::where('id', '=', $request->id)->delete();
            return response()->json(['code' => 200, 'data' => $postman,'message' => 'Deleted successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function folderDestroy(PostmanFolder $postmanFolder, Request $request)
    {
        try{
            $folders = PostmanFolder::where('id', '=', $request->id)->delete();
            return response()->json(['code' => 200, 'data' => $folders,'message' => 'Deleted successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function postmanHistoryLog(Request $request)
    {
        try{
            $postHis = PostmanHistory::select('postman_historys.*', 'u.name AS userName')
            ->leftJoin('users AS u', 'u.id', 'postman_historys.user_id')
            ->where('postman_id', '=', $request->id)->orderby('id', 'DESC')->get();
            return response()->json(['code' => 200, 'data' => $postHis,'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function postmanRequestHistoryLog(Request $request)
    {
        try{
            $postHis = PostmanRequestHistory::select('postman_request_histories.request_url','postman_request_histories.created_at', 'postman_request_histories.id', 'u.name AS userName')
            ->leftJoin('users AS u', 'u.id', 'postman_request_histories.user_id')
            ->where('request_id', '=', $request->id)->orderby('id', 'DESC')->get();
            return response()->json(['code' => 200, 'data' => $postHis,'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
    public function postmanResponseHistoryLog(Request $request)
    {
        try{
            $postHis = PostmanResponse::select('postman_responses.*', 'u.name AS userName')
            ->leftJoin('users AS u', 'u.id', 'postman_responses.user_id')
            ->where('request_id', '=', $request->id)->orderby('id', 'DESC')->get();
            // $html = '';
            // foreach($postHis AS $postManH) {
            //     $html += '<td>'.$postManH->id.'</td>';
            //     $html += '<td>'.$postManH->userName.'</td>';
            //     $html += '<td>'.json_encode($postManH->response).'</td>';
            //     $html += '<td>'.$postManH->created_at.'</td>';
            // }
            return response()->json(['code' => 200, 'data' => $postHis,'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function removeUserPermission(Request $request){
        try{
            $postHis = PostmanRequestCreate::select('*')->where('id', '=', $request->id)->orderby('id', 'DESC')->first();
            $users = explode(",", $postHis->user_permission);
            //dump($users);
            if (($key = array_search($request->user_id, $users)) !== false) {
                unset($users[$key]);
            }
            $postHis = PostmanRequestCreate::select('*')->where('id', '=', $request->id)->update(['user_permission' => implode(",",$users)]);
            return response()->json(['code' => 200, 'data' => $postHis,'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
    
    public function getPostmanWorkSpaceAPI(){
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.getpostman.com/workspaces',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'X-API-Key: PMAK-628e2e514dda7828c6d31346-c9d017e28c87fc3ab2f27fee66118eec62
        '
        ),
        ));
       
        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }

    public function getAllPostmanCollectionApi() {
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.getpostman.com/collections',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'X-Api-Key: PMAK-628e2e514dda7828c6d31346-c9d017e28c87fc3ab2f27fee66118eec62',
            'Content-Type: application/json',
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    public function createPostmanCollectionAPI(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.getpostman.com/collections',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "collection": {
                "info": {
                    "name": "Sample Collection 909",
                    "description": "This is just a sample collection.",
                    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
                },
                "item": [
                    {
                        "name": "This is a folder",
                        "item": [
                            {
                                "name": "Sample POST Request",
                                "request": {
                                    "url": "https://postman-echo.com/post",
                                    "method": "POST",
                                    "header": [
                                        {
                                            "key": "Content-Type",
                                            "value": "application/json"
                                        }
                                    ],
                                    "body": {
                                        "mode": "raw",
                                        "raw": "{\\"data\\": \\"123\\"}"
                                    },
                                    "description": "This is a sample POST Request"
                                }
                            }
                        ]
                    },
                    {
                        "name": "Sample GET Request",
                        "request": {
                            "url": "https://postman-echo/get",
                            "method": "GET",
                            "description": "This is a sample GET Request"
                        }
                    }
                ]
            }
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'X-API-Key: PMAK-628e2e514dda7828c6d31346-c9d017e28c87fc3ab2f27fee66118eec62
        '
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    public function getPostmanCollectionAndCreateAPI(Request $request) 
    {
        $requestData['folder_name'] = isset($request->folder_real_name) ? $request->folder_real_name : 'test New Folder';
        $requestData['request_name'] = isset($request->request_name) ? $request->request_name : 'This is New Request';
        $requestData['request_url'] = isset($request->request_url) ? $request->request_url : 'https://google.com';
        $requestData['request_type'] = isset($request->request_type) ? $request->request_type : 'POST';
        $requestData['body_json'] = isset($request->body_json) ? $request->body_json : '{"id": "1", "name":"hello"}';

        // Create folder
        //$this->createPostmanFolder($request->folder_name, $request->folder_real_name);
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.getpostman.com/collections/40e314b8-610d-4396-824f-2d7896ac1914',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'X-API-Key: PMAK-628e2e514dda7828c6d31346-c9d017e28c87fc3ab2f27fee66118eec62
        '
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        
        $collect = (array) json_decode($response);
        $collectNew = $collect;
        foreach($collect['collection']->item AS $key => $val){
            $vals = (array)$val;
            foreach($vals AS $ikey => $ival){
                if($ival == $requestData['folder_name']) {   
                    //print_r($ival); 
                    $collectNew['collection']->item[$key]->item[] = array(
                        "name" => $requestData['request_name'],
                        "request" => array(
                            "url" => $requestData['request_url'],
                            "method" => $requestData['request_type'],
                            "header" => [
                                array("key" => "Content-Type", "value" => "application/json")
                            ],
                            "body" => array(
                                "mode" => "raw",
                                "raw" => $requestData['body_json']
                            ),
                            "description" => "This is a sample POST Request"
                        )
                    );
                    //dd($key);
                }
            }
        } //end foreach
        if($request->isjson) {
           
            echo '<pre>';print_r(($collectNew)); exit;
        }
        return json_encode((object) $collectNew);
    }

    /**
     * Create exiting postman folder inside request 
     *
     * @param Request $request
     * @return JsonResponce
     */
    public function updatePostmanCollectionAPI(Request $request)
    {

        
       /* $collect['collection']['info'] = array(
                                            "name" => "Nikunj ERP",
                                            "description" => "This is just a sample collection.",
                                            "_postman_id" => "174bad7c-07e3-45f3-914f-36cf84e5586f",
                                            "schema" => "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
                                            );
        */
        

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.getpostman.com/collections/40e314b8-610d-4396-824f-2d7896ac1914',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS =>/*'{
            "collection": {
                "info": {
                    "name": "Nikunj ERP",
                    "description": "This is just a sample collection.",
                    "_postman_id": "174bad7c-07e3-45f3-914f-36cf84e5586f",
                    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
                },
                "item": [
                    {
                        "name": "This is a folder",
                        "item": [
                           
                            {
                                "name": "Sample POST Request3",
                                "request": {
                                    "url": "https://postman-echo.com/post",
                                    "method": "POST",
                                    "header": [
                                        {
                                            "key": "Content-Type",
                                            "value": "application/json"
                                        }
                                    ],
                                    "body": {
                                        "mode": "raw",
                                        "raw": "{\\"data\\": \\"123\\"}"
                                    },
                                    "description": "This is a sample POST Request"
                                }
                            }
                        ]
                    },
                    {
                        "name": "Sample GET Request",
                        "request": {
                            "url": "https://postman-echo/get",
                            "method": "GET",
                            "description": "This is a sample GET Request"
                        }
                    },
                    {
                        "name": "Sample GET Request2",
                        "request": {
                            "url": "https://postman-echo/get",
                            "method": "GET",
                            "description": "This is a sample GET Request"
                        }
                    }
                ]
            }
        }'*/
        $this->getPostmanCollectionAndCreateAPI($request)
        ,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'X-API-Key: PMAK-628e2e514dda7828c6d31346-c9d017e28c87fc3ab2f27fee66118eec62'
        ),
        )
    );

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }
    
    public function createPostmanFolder($fID = '', $fName = ''){
        
        if($fID == '')
            $fID = '1';
        if($fName == '')
            $fName = 'test New Folder';
        
        
        $curl = curl_init(); 
        curl_setopt_array($curl, array( CURLOPT_URL => 'https://api.getpostman.com/collections/40e314b8-610d-4396-824f-2d7896ac1914/folders', 
        CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', 
        CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, 
        CURLOPT_FOLLOWLOCATION => true, 
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
        CURLOPT_CUSTOMREQUEST => 'POST', 
        CURLOPT_POSTFIELDS =>'{ 
            "id": "'.$fID.'", 
            "name": "'.$fName.'", 
            "description": "This is a '.$fName.' folder." 
        }', 
        CURLOPT_HTTPHEADER => array( 
            'Accept: application/vnd.postman.v2+json', 
            'X-API-Key: PMAK-628e2e514dda7828c6d31346-c9d017e28c87fc3ab2f27fee66118eec62', 
            'Content-Type: application/json' ), 
        )); 
        $response = curl_exec($curl);
        curl_close($curl); 
        echo $response;
       // dd($fID, $fName, $response);
    }

    public function createPostmanRequestAPI($fID = '',$request = ''){
        $requestData['id'] = isset($request->id) ? $request->id : '1';
        $requestData['folder_name'] = isset($request->folder_real_name) ? $request->folder_real_name : 'test New Folder';
        $requestData['request_name'] = isset($request->request_name) ? $request->request_name : 'This is New Request';
        $requestData['request_url'] = isset($request->request_url) ? $request->request_url : 'https://google.com';
        $requestData['request_type'] = isset($request->request_type) ? $request->request_type : 'POST';
        $requestData['body_json'] = isset($request->body_json) ? $request->body_json : '{"id": "1", "name":"hello"}';
        $requestData['isjson'] = isset($request->isjson) ? $request->isjson : 'isjson';
        $requestData['body_type'] = isset($request->body_type) ? $request->body_type : 'row';

        if($fID == '')
            $fID = '1';
        if( $requestData['folder_name'] == '')
            $fName = 'test New Folder';
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.getpostman.com/collections/40e314b8-610d-4396-824f-2d7896ac1914/requests?folder='.$fID,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "id": "'.$requestData['id'].'",
            "name": "'.$requestData['request_name'].'",
            "description": "This is an '.$requestData['request_name'].'.",
            "headers": "",
            "url": "'.$requestData['request_url'].'",
            "preRequestScript": "",
            "pathVariables": {},
            "method": "'.$requestData['request_type'].'",
            "rawModeData": "'.$requestData['body_type'].'"
            "data": [
                '.$requestData['body_json'].'
            ],
            "dataMode": "params",
            "tests": "var data = JSON.parse(responseBody);"
        }',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/vnd.postman.v2+json',
            'X-api-key: PMAK-628e2e514dda7828c6d31346-c9d017e28c87fc3ab2f27fee66118eec62',
            'Content-Type: application/json',
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;



        if($requestData['isjson']) {
           
            echo '<pre>';print_r(($response)); exit;
        }


        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        // CURLOPT_URL => 'https://api.getpostman.com/collections',
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => '',
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 0,
        // CURLOPT_FOLLOWLOCATION => true,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => 'POST',
        // CURLOPT_POSTFIELDS =>'{
        //     "collection": {
        //         "info": {
        //             "name": "ERP",
        //             "description": "This is just a sample collection.",
        //             "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
        //         },
        //         "item": [
        //             {
        //                 "name": "ERP Folder",
        //                 "item": [
        //                     {
        //                         "name": "Sample GET Request",
        //                         "request": {
        //                             "url": "https://postman-echo.com/post-new",
        //                             "method": "POST",
        //                             "header": [
        //                                 {
        //                                     "key": "Content-Type",
        //                                     "value": "application/json"
        //                                 }
        //                             ],
        //                             "body": {
        //                                 "mode": "raw",
        //                                 "raw": "{\\"data\\": \\"123\\"}"
        //                             },
        //                             "description": "This is a sample POST Request"
        //                         }
        //                     },
        //                     {
        //                         "name": "Sample GET Request",
        //                         "request": {
        //                             "url": "https://postman-echo/get",
        //                             "method": "GET",
        //                             "description": "This is a sample GET Request"
        //                         }
        //                     }
        //                 ]
        //             }
        //         ]
        //     }
        // }',
        // CURLOPT_HTTPHEADER => array(
        //     'Content-Type: application/json',
        //     'X-API-Key: PMAK-628e2e514dda7828c6d31346-c9d017e28c87fc3ab2f27fee66118eec62
        // '
        // ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // echo $response;
    }

    public function sendPostmanRequestAPI(Request $request){

        try{
            $postman = PostmanRequestCreate::where('id', $request->id)->first();
            if(empty($postman)) {
                return response()->json(['code' => 500, 'message' => 'Request Data not found']);
            } else {
                PostmanRequestHistory::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'request_id' => $postman->id,
                        'request_data' => $postman->body_json,
                        'request_url' => $postman->request_url,
                        'request_headers' => "'Content-Type: application/json',
                                            'Authorization: '".$postman->authorization_type."',
                                            'Cookie: PHPSESSID=l15g0ovuc3jpr98tol956voan6'"
                    ]
                );
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => $postman->request_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $postman->request_type,
                CURLOPT_POSTFIELDS =>$postman->body_json,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: '.$postman->authorization_type."'",
                    'Cookie: PHPSESSID=l15g0ovuc3jpr98tol956voan6'
                ),
                ));
                
                $response = curl_exec($curl);

                curl_close($curl);
            
                //dd($response);
                PostmanResponse::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'request_id' => $postman->id,
                        'response' => json_encode($response),
                        'request_url' => $postman->request_url,
                        'request_data' => $postman->body_json
                    ]
                );
            }
            return response()->json(['code' => 200, 'data' => [], 'message' => 'Postman requested successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
        
    }

    
}
