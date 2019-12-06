<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;
use Hubstaff\Hubstaff;

class HubstaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        $url = 'https://api.hubstaff.com/v2/organizations/197350/members';
        //$url = 'https://api.hubstaff.com/v2/organizations/197350/projects?status=active';
        //$url = 'https://api.hubstaff.com/v2/users/me';
        $data   = array();
        $members = $this->get_data($url);
        if(!empty($members->members)) {
            $data['error'] = false;
            foreach ($members->members as $member) {
                $member_url = 'https://api.hubstaff.com/v2/users/' . $member->user_id;
                $users = $this->get_data($member_url);
                $data[] = $users->user;
            }
        }else{
            $data['error'] = true;
            $data['error_description'] = $members->error_description;
        }

        return view('hubstaff.members', [
            'members' => $data,
        ]);
    }


    public function getProjects(){

        $url        = 'https://api.hubstaff.com/v2/organizations/197350/projects?status=active';
        $projects   = $this->get_data($url);
        $data   = array();
        if(!empty($projects->projects)) {
            $data['error'] = false;
            $data[]         = $projects->projects;
        }else{
            $data['error'] = true;
            $data['error_description'] = $projects->error_description;
        }
        return view('hubstaff.projects', [
            'projects' => $data,
        ]);
    }

    public function getTasks(){

        $url        = 'https://api.hubstaff.com/v2/organizations/197350/tasks';
        $tasks      = $this->get_data($url);
        $data   = array();
        if(!empty($tasks->projects)) {
            $data['error'] = false;
            $data[]         = $tasks->projects;
        }else{
            $data['error'] = true;
            $data['error_description'] = $tasks->error_description;
        }
        return view('hubstaff.tasks', [
            'tasks' => $data,
        ]);
    }


    public function get_data($url) {

        $ch = curl_init($url);

        $app_token = '2YuxAoBm9PHUtruFNYTnA9HhvI3xMEGSU-EICdO5VoM';
        $auth_token = 'Bearer 6f2bab2f1813745b689d3446f37d11bf177ca40ede4f9985155fd9e485039f36';
        //$auth_token = 'Bearer e2f8c8e136c73b1e909bb1021b3b4c29';
        //$auth_token = 'je_2A29CStS3J-YPasj1UIjlg2qpYNs-hoLmw8SToe8';

        $http_header = array(
            "App-Token: 2YuxAoBm9PHUtruFNYTnA9HhvI3xMEGSU-EICdO5VoM",
            "Authorization: ".$auth_token,
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
    public function updateTitle(Request $request) {
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
    public function updateDescription(Request $request) {
        $article = Article::findOrFail($request['id']);
        $article->description = $request['article_desc'];
        $article->save();
        return response()->json([
            'type' => 'success',
            'message' => 'Description Updated'
        ]);
    }

}
