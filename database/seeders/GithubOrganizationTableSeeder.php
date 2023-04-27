<?php

namespace Database\Seeders;

use App\Github\GithubRepository;
use App\Github\GithubOrganization;
use Illuminate\Database\Seeder;

class GithubOrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organizationObj = array(
                'name' => 'MMMagento',
                'username' => 'MioModaMagento',
                'token' => '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'
            );

        $organization = GithubOrganization::updateOrCreate(
                [
                    'name' => 'MMMagento',
                ],
                $organizationObj
            );

        $organizationCount = GithubOrganization::count();

        if($organizationCount == 1){
            $isUpdated = GithubRepository::whereNull('github_organization_id')->update(['github_organization_id' => $organization->id]);
        }
    }
}
