<?php

namespace App\Http\Controllers\Github;

use App\Github\GithubGroup;
use App\Github\GithubGroupMember;
use App\Github\GithubRepository;
use App\Github\GithubRepositoryGroup;
use App\Github\GithubUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Route;

class GroupController extends Controller
{

    private $client;

    function __construct()
    {
        $this->client = new Client([
            'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
        ]);
    }

    public function listGroups()
    {
        $groups = GithubGroup::with('users')->get();
        return view('github.groups', ['groups' => $groups]);
    }

    public function groupDetails()
    {
        $groupId = Route::current()->parameter('groupId');
        $group = GithubGroup::find($groupId);
        $repositories = $group->repositories;
        $users = $group->users;

        return view(
            'github.group_details',
            [
                'group' => $group,
                'repositories' => $repositories,
                'users' => $users
            ]
        );
    }

    public function removeUsersFromGroup()
    {
        $groupId = Route::current()->parameter('groupId');
        $userId = Route::current()->parameter('userId');

        $githubUser = GithubUser::find($userId);

        //https://api.github.com/teams/:team_id/memberships/:username
        $url = 'https://api.github.com/teams/' . $groupId . '/memberships/' . $githubUser->username;
        $this->client->delete($url);

        GithubGroupMember::where('github_groups_id', $groupId)->where('github_users_id', $userId)->delete();

        return redirect()->back();
    }

    public function removeRepositoryFromGroup()
    {
        $groupId = Route::current()->parameter('groupId');
        $repoId = Route::current()->parameter('repoId');

        $repo = GithubRepository::find($repoId);

        //https://api.github.com/teams/:team_id/repos/:owner/:repo
        $url = 'https://api.github.com/teams/' . $groupId . '/repos/' . getenv('GITHUB_ORG_ID') . '/' . $repo->name;
        $this->client->delete($url);

        GithubRepositoryGroup::where('github_repositories_id', $repoId)->where('github_groups_id', $groupId)->delete();

        return redirect()->back();
    }
}
