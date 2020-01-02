<?php

namespace App\Http\Controllers;

use App\Article;
use App\HubstaffMember;
use Illuminate\Http\Request;
use Hubstaff\Hubstaff;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Session;

define("STATE_MEMBERS", "MEMBERS");

define("SESSION_ACCESS_TOKEN", "access_token");
define("SESSION_REFRESH_TOKEN", "refresh_token");

class HubstaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $value = session(SESSION_ACCESS_TOKEN);
        $members = HubstaffMember::all();

        if (!$value) {
            $url = 'https://account.hubstaff.com/.well-known/openid-configuration';

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $response = curl_exec($ch);
            curl_close($ch);

            $decoded_json = json_decode($response);

            $params = array(
                'client_id' => getenv('HUBSTAFF_CLIENT_ID'),
                'response_type' => 'code',
                'nonce' => sha1(time()),
                'redirect_uri' => getenv('APP_URL') . '/hubstaff/redirect',
                'scope' => 'hubstaff:read hubstaff:write',
                'state' => STATE_MEMBERS
            );

            return view(
                'hubstaff.members',
                [
                    'auth' => [
                        'should_show_login' => true,
                        'link' => $decoded_json->authorization_endpoint . '?' . http_build_query($params)
                    ],
                    'members' => $members
                ]
            );
        }else{
            return view(
                'hubstaff.members',
                [
                    'members' => $members
                ]
            );
        }
    }

    public function getMembers()
    {
        $httpClient = new Client();

        $access_token = session(SESSION_ACCESS_TOKEN);

        $response = $httpClient->get(
            'https://api.hubstaff.com/v2/organizations/197350/members',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $access_token
                ]
            ]
        );

        if ($response->getStatusCode() == 200) {
            $responseJson = json_decode($response->getBody()->getContents());
            foreach ($responseJson->members as $member) {
                //eloquent insert
                HubstaffMember::updateOrCreate(
                    [
                        'hubstaff_user_id' => $member->user_id
                    ],
                    [
                        'hubstaff_user_id' => $member->user_id
                    ]
                );
            }
        }
        // redirect to members list
        return redirect('hubstaff/members');
    }


    public function getProjects()
    {

        $url        = 'https://api.hubstaff.com/v2/organizations/197350/projects?status=active';
        $projects   = $this->get_data($url);
        $data   = array();
        if (!empty($projects->projects)) {
            $data['error'] = false;
            $data[]         = $projects->projects;
        } else {
            $data['error'] = true;
            $data['error_description'] = $projects->error_description;
        }
        return view('hubstaff.projects', [
            'projects' => $data,
        ]);
    }

    public function getTasks()
    {

        $url        = 'https://api.hubstaff.com/v2/organizations/197350/tasks';
        $tasks      = $this->get_data($url);
        $data   = array();
        if (!empty($tasks->projects)) {
            $data['error'] = false;
            $data[]         = $tasks->projects;
        } else {
            $data['error'] = true;
            $data['error_description'] = $tasks->error_description;
        }
        return view('hubstaff.tasks', [
            'tasks' => $data,
        ]);
    }


    public function get_data($url)
    {

        $ch = curl_init($url);

        $app_token = '2YuxAoBm9PHUtruFNYTnA9HhvI3xMEGSU-EICdO5VoM';
        $auth_token = 'Bearer 6f2bab2f1813745b689d3446f37d11bf177ca40ede4f9985155fd9e485039f36';
        //$auth_token = 'Bearer e2f8c8e136c73b1e909bb1021b3b4c29';
        //$auth_token = 'je_2A29CStS3J-YPasj1UIjlg2qpYNs-hoLmw8SToe8';

        $http_header = array(
            "App-Token: 2YuxAoBm9PHUtruFNYTnA9HhvI3xMEGSU-EICdO5VoM",
            "Authorization: " . $auth_token,
            "Content-Type: application/x-www-form-urlencoded;charset=UTF-8",
            "Accept: application/json",
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    /**
     * Get Broken Links Details
     * Function for display
     * 
     * @return json response
     */
    public function updateTitle(Request $request)
    {
        $article = Article::findOrFail($request['id']);
        $article->title = $request['article_title'];
        $article->save();
        return response()->json([
            'type' => 'success',
            'message' => 'Title Updated'
        ]);
    }

    /**
     * Updated Title
     * Function for display
     * 
     * @return json response
     */
    public function updateDescription(Request $request)
    {
        $article = Article::findOrFail($request['id']);
        $article->description = $request['article_desc'];
        $article->save();
        return response()->json([
            'type' => 'success',
            'message' => 'Description Updated'
        ]);
    }

    public function redirect(Request $request)
    {
        echo '<h1>Processing your request</h1>';

        $code = $request->query()['code'];
        $state = $request->query()['state'];
        if ($code) {

            $params = array(
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => getenv('APP_URL') . '/hubstaff/redirect',
            );

            $ch = curl_init('https://account.hubstaff.com/access_tokens?' . http_build_query($params));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, getenv('HUBSTAFF_CLIENT_ID') . ":" . getenv('HUBSTAFF_CLIENT_SECRET'));
            $response = curl_exec($ch);
            curl_close($ch);

            $json_decoded_response = json_decode($response);

            session([
                SESSION_ACCESS_TOKEN => $json_decoded_response->access_token,
                SESSION_REFRESH_TOKEN => $json_decoded_response->refresh_token
            ]);

            Session::save();

            if ($state == STATE_MEMBERS) {
                return redirect('hubstaff/members');
            }
        }
    }
}
