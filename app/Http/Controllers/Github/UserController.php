<?php

namespace App\Http\Controllers\Github;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Route;

class UserController extends Controller
{

    private $client;

    function __construct()
    {
        $this->client = new Client([
            'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
        ]);
    }



    public function listUsers(){
        return view('github.users');
    }

    private function refreshUsersForRespository(string $repositoryName){

        $url = "https://api.github.com/repos/".getenv('GITHUB_ORG_ID')."/".$repositoryName."/collaborators";
        $response = $this->client->get($url);

        $users = json_decode($response->getBody()->getContents());

        foreach($users as $user){

            $dbUser = [
                'id' => $user->id,
                'username' => $user->login,
            ];

            
        }

    }

    public function listUsersOfRepository(){
        $name = Route::current()->parameter('name');
        return response($name);
    }
}
