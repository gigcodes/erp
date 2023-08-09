<?php

namespace Database\Seeders;

use App\GitMigrationErrorLog;
use Illuminate\Database\Seeder;
use App\Github\GithubRepository;
use App\Github\GithubBranchState;
use App\Github\GithubOrganization;
use App\Github\GithubRepositoryUser;
use App\Github\GithubRepositoryGroup;

class GithubOrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organizationObj = [
            'name' => 'MMMagento',
            'username' => 'MioModaMagento',
            'token' => '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',
        ];

        $organization = GithubOrganization::updateOrCreate(
            [
                'name' => 'MMMagento',
            ],
            $organizationObj
        );

        $organizationCount = GithubOrganization::count();

        if ($organizationCount == 1) {
            $isUpdated = GithubRepository::whereNull('github_organization_id')->update(['github_organization_id' => $organization->id]);

            $isStateUpdated = GithubBranchState::whereNull('github_organization_id')->update(['github_organization_id' => $organization->id]);

            $isLogUpdated = GitMigrationErrorLog::whereNull('github_organization_id')->update(['github_organization_id' => $organization->id]);

            $isGroupUpdated = GithubRepositoryGroup::whereNull('github_organization_id')->update(['github_organization_id' => $organization->id]);

            $isUserUpdated = GithubRepositoryUser::whereNull('github_organization_id')->update(['github_organization_id' => $organization->id]);
        }
    }
}
