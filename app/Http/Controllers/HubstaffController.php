<?php

namespace App\Http\Controllers;

use App\Article;
use App\HubstaffMember;
use App\User;
use Illuminate\Http\Request;
use Hubstaff\Hubstaff;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Input;
use Session;

define("STATE_MEMBERS", "MEMBERS");

define("SESSION_ACCESS_TOKEN", "access_token");
define("SESSION_REFRESH_TOKEN", "refresh_token");

class HubstaffController extends Controller
{

    private function getLoginUrl()
    {
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

        return $decoded_json->authorization_endpoint . '?' . http_build_query($params);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $value = session(SESSION_ACCESS_TOKEN);
        $members = HubstaffMember::all();
        $users = User::all('id', 'name');

        if (!$value) {
            return view(
                'hubstaff.members',
                [
                    'auth' => [
                        'should_show_login' => true,
                        'link' => $this->getLoginUrl()
                    ],
                    'members' => $members,
                    'users' => $users
                ]
            );
        } else {
            return view(
                'hubstaff.members',
                [
                    'members' => $members,
                    'users' => $users
                ]
            );
        }
    }

    public function getMembers()
    {
        $httpClient = new Client();

        $access_token = session(SESSION_ACCESS_TOKEN);

        $url = 'https://api.hubstaff.com/v2/organizations/' . getenv('HUBSTAFF_ORG_ID') . '/members';

        $response = $httpClient->get(
            $url,
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

        $access_token = session(SESSION_ACCESS_TOKEN);

        if (!$access_token) {
            return view(
                'hubstaff.projects',
                [
                    'auth' =>  $this->getLoginUrl(),
                ]
            );
        }

        $url = 'https://api.hubstaff.com/v2/organizations/' . getenv('HUBSTAFF_ORG_ID') . '/projects';
        $httpClient = new Client();
        $response = $httpClient->get(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $access_token
                ]
            ]
        );

        $projects = [];
        if ($response->getStatusCode() == 200) {
            $responseJson = json_decode($response->getBody()->getContents());
            $projects = $responseJson->projects;
        }

        return view(
            'hubstaff.projects',
            [
                'projects' => $projects,
            ]
        );
    }

    public function editProject(Request $request)
    {

        $access_token = session(SESSION_ACCESS_TOKEN);

        $projectId = $request->route('id');

        $httpClient = new Client();
        $url = 'https://api.hubstaff.com/v2/projects/' . $projectId;
        $response = $httpClient->get(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $access_token
                ]
            ]
        );
        $project = array(
            'id' => $projectId,
            'name' => '',
            'description' => ''
        );
        if ($response->getStatusCode() == 200) {
            $responseJson = json_decode($response->getBody()->getContents());
            $project['name'] = isset($responseJson->project->name) ? $responseJson->project->name : '';
            $project['description'] = isset($responseJson->project->description) ? $responseJson->project->description : '';
        }
        return view(
            'hubstaff.projectedit',
            [
                'project' => $project
            ]
        );
    }

    public function editProjectData()
    {
        $projectName = Input::get('name');
        $projectDescription = Input::get('description');
        $projectId = Input::get('id');

        $access_token = session(SESSION_ACCESS_TOKEN);
        $url = 'https://api.hubstaff.com/v2/projects/' . $projectId;
        $httpClient = new Client();
        $response = $httpClient->put(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $access_token,
                    'Content-Type' => 'application/json'
                ],

                RequestOptions::BODY => json_encode([
                    'name' => $projectName,
                    'description' => $projectDescription
                ])
            ]
        );
        if ($response->getStatusCode() == 200) {
            return redirect('hubstaff/projects');
        } else {
            echo '<h1>Error in saving data to hubstaff</h1>';
        }
    }

    public function getTasks()
    {
        $access_token = session(SESSION_ACCESS_TOKEN);

        if (!$access_token) {
            return view(
                'hubstaff.projects',
                [
                    'auth' =>  $this->getLoginUrl(),
                ]
            );
        }

        $url = 'https://api.hubstaff.com/v2/organizations/' . getenv('HUBSTAFF_ORG_ID') . '/tasks?status=active%2Ccompleted';
        $httpClient = new Client();
        $response = $httpClient->get(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $access_token
                ]
            ]
        );

        $tasks = [];
        if ($response->getStatusCode() == 200) {
            $responseJson = json_decode($response->getBody()->getContents());
            $tasks = $responseJson->tasks;
        }

        return view(
            'hubstaff.tasks',
            [
                'tasks' => $tasks,
            ]
        );
    }

    public function addTaskFrom()
    {
        $access_token = session(SESSION_ACCESS_TOKEN);
        $httpClient = new Client();
        $url = 'https://api.hubstaff.com/v2/organizations/' . getenv('HUBSTAFF_ORG_ID') . '/projects';
        $response = $httpClient->get(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $access_token
                ]
            ]
        );

        $usersDatabase = HubstaffMember::whereNotNull('hubstaff_user_id')
            ->leftJoin('users', 'users.id', '=', 'hubstaff_members.user_id')
            ->select('users.name', 'hubstaff_members.hubstaff_user_id')
            ->get();

        $users = [];
        foreach ($usersDatabase as $user) {
            $users[$user->hubstaff_user_id] = $user->name;
        }

        $projects = [];

        if ($response->getStatusCode() == 200) {
            $responseJson = json_decode($response->getBody()->getContents());

            foreach ($responseJson->projects as $project) {
                $projects[$project->id] = $project->name;
            }
        }

        return view(
            'hubstaff.taskedit',
            [
                'projects' => $projects,
                'isNew' => true,
                'users' => $users
            ]
        );
    }

    public function addTask()
    {
        $taskSummary = Input::get('summary');
        $project_id = Input::get('project_id');
        $assignee_id = Input::get('assignee_id');

        $access_token = session(SESSION_ACCESS_TOKEN);

        $url = 'https://api.hubstaff.com/v2/projects/' . $project_id . '/tasks';

        $httpClient = new Client();
        $response = $httpClient->post(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $access_token,
                    'Content-Type' => 'application/json'
                ],

                RequestOptions::BODY => json_encode([
                    'summary' => $taskSummary,
                    'assignee_id' => $assignee_id
                ])
            ]
        );

        if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
            return redirect('hubstaff/tasks');
        } else {
            echo '<h1>Error in saving data to hubstaff</h1>';
        }
    }

    public function editTaskForm(Request $request)
    {
        $access_token = session(SESSION_ACCESS_TOKEN);
        $taskId = $request->route('id');

        $httpClient = new Client();



        $url = 'https://api.hubstaff.com/v2/tasks/' . $taskId;
        $response = $httpClient->get(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $access_token
                ]
            ]
        );
        $task = array(
            'id' => $taskId,
            'summary' => '',
            'project_id' => 'none',
        );
        if ($response->getStatusCode() == 200) {
            $responseJson = json_decode($response->getBody()->getContents());
            $task['summary'] = isset($responseJson->task->summary) ? $responseJson->task->summary : '';
            $task['project_id'] = isset($responseJson->task->project_id) ? $responseJson->task->project_id : 'none';
        } else {
            return response('<h1>Error in getting task data</h1>');
        }

        $url = 'https://api.hubstaff.com/v2/organizations/' . getenv('HUBSTAFF_ORG_ID') . '/projects';
        $response = $httpClient->get(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $access_token
                ]
            ]
        );
        $projects = [];

        $usersDatabase = HubstaffMember::whereNotNull('hubstaff_user_id')
            ->leftJoin('users', 'users.id', '=', 'hubstaff_members.user_id')
            ->select('users.name', 'hubstaff_members.hubstaff_user_id')
            ->get();

        $users = [];
        foreach ($usersDatabase as $user) {
            $users[$user->hubstaff_user_id] = $user->name;
        }

        if ($response->getStatusCode() == 200) {
            $responseJson = json_decode($response->getBody()->getContents());

            foreach ($responseJson->projects as $project) {
                $projects[$project->id] = $project->name;
            }
            return view(
                'hubstaff.taskedit',
                [
                    'projects' => $projects,
                    'task' => $task,
                    'users' => $users
                ]
            );
        }
    }

    public function editTask()
    {
        $taskId = Input::get('id');
        $taskSummary = Input::get('summary');
        $project_id = Input::get('project_id');

        $access_token = session(SESSION_ACCESS_TOKEN);
        $url = 'https://api.hubstaff.com/v2/tasks/' . $taskId;

        $httpClient = new Client();
        $response = $httpClient->put(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $access_token,
                    'Content-Type' => 'application/json'
                ],

                RequestOptions::BODY => json_encode([
                    'summary' => $taskSummary,
                    'project_id' => $project_id,
                    'lock_version' => 0
                ])
            ]
        );

        if ($response->getStatusCode() == 200) {
            return redirect('hubstaff/tasks');
        } else {
            echo '<h1>Error in saving data to hubstaff</h1>';
        }
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

    public function linkUser(Request $request)
    {
        $bodyContent = $request->getContent();
        $jsonDecodedBody = json_decode($bodyContent);

        $userId = $jsonDecodedBody->user_id;
        $hubstaffUserId = $jsonDecodedBody->hubstaff_user_id;

        if (!$userId || !$hubstaffUserId) {
            return response()->json(
                [
                    'error' => 'Missing parameters',
                ],
                400
            );
        }

        HubstaffMember::where('hubstaff_user_id', $hubstaffUserId)
            ->update([
                'user_id' => $userId
            ]);


        return response()->json([
            'message' => 'link success'
        ]);
    }

    public function debug()
    {
        echo "debug";
    }
}
