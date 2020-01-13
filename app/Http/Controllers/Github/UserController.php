<?php

namespace App\Http\Controllers\Github;

use App\Github\GithubRepository;
use App\Github\GithubRepositoryUser;
use App\Github\GithubUser;
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


    private function refreshUsersForOrganization(){
        $url = "https://api.github.com/orgs/". getenv('GITHUB_ORG_ID') ."/members";
        $response = $this->client->get($url);
        $users = json_decode($response->getBody()->getContents());
        $returnUser = [];
        foreach ($users as $user) {
            $dbUser = [
                'id' => $user->id,
                'username' => $user->login,
            ];

            $updatedUser = GithubUser::updateOrCreate(
                [
                    'id' => $user->id
                ],
                $dbUser
            );
            $returnUser[] = $updatedUser;
        }
        return $returnUser;
    }

    public function listOrganizationUsers()
    {
        $this->refreshUsersForOrganization();
        $users = GithubUser::with('repositories')->get();
        return view('github.org_users', ['users' => $users]);
    }

    public function repositoryNames($user){
        return array_map(
            function ($repository){
                return $repository->name;
            },
            $user->repositories->toArray()
        );
    }

    private function refreshUsersForRespository(string $repositoryName)
    {

        $githubRepository = GithubRepository::where('name', $repositoryName)->first();

        $url = "https://api.github.com/repos/" . getenv('GITHUB_ORG_ID') . "/" . $repositoryName . "/collaborators";
        $response = $this->client->get($url);

        $users = json_decode($response->getBody()->getContents());

        $returnUsers = [];
        foreach ($users as $user) {
            $dbUser = [
                'id' => $user->id,
                'username' => $user->login,
            ];

            GithubUser::updateOrCreate(
                [
                    'id' => $user->id
                ],
                $dbUser
            );

            $rights = null;
            if ($user->permissions->admin == true) {
                $rights = 'admin';
            } else if ($user->permissions->push == true) {
                $rights = 'push';
            } else if ($user->permissions->pull == true) {
                $rights = 'pull';
            }



            GithubRepositoryUser::updateOrCreate(
                [
                    'github_users_id' => $user->id,
                    'github_repositories_id' => $githubRepository->id
                ],
                [
                    'github_users_id' => $user->id,
                    'github_repositories_id' => $githubRepository->id,
                    'rights' => $rights
                ]
            );

            $returnUsers[] = [
                'id' => $user->id,
                'username' => $user->login,
                'rights' => $rights
            ];
        }
        return $returnUsers;
    }

    public function listUsersOfRepository()
    {
        $name = Route::current()->parameter('name');
        $users = $this->refreshUsersForRespository($name);
        
        return view('github.repository_users', ['users' => $users]);
    }
}
