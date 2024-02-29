<?php

namespace Database\Seeders;

use App\AffiliateProviders;
use Illuminate\Database\Seeder;

class AffiliateProvider extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $affiliate = AffiliateProviders::where('provider_name', 'Tapfiliate')->first();
        if (! $affiliate) {
            AffiliateProviders::create(
                [
                    'provider_name' => 'Tapfiliate',
                    'status'        => 1,
                ]
            );
        }
    }
}
