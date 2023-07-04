<?php
namespace Database\Seeders;

use App\ConfigRefactor;
use App\ConfigRefactorSection;
use Illuminate\Database\Seeder;

class ConfigRefactorSectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $sampleDatas = self::sampleData();

        foreach($sampleDatas as $sampleData)
        {
            ConfigRefactorSection::firstOrCreate([
                'name' => $sampleData['name'], 
                'type' => $sampleData['type']
            ]);
        }

        $configRefactorSections = ConfigRefactorSection::all();
        foreach($configRefactorSections as $configRefactorSection)
        {
            ConfigRefactor::firstOrCreate([
                'config_refactor_section_id' => $configRefactorSection->id, 
            ]);
        }
    }

    public static function sampleData()
    {
        return [
            [
                'name' => 'amasty_base',
                'type' => 'ND'
            ],
            [
                'name' => 'amgeoip',
                'type' => 'ND'
            ],
            [
                'name' => 'amasty_cross_links',
                'type' => 'ND'
            ],
            [
                'name' => 'amasty_checkout',
                'type' => 'ND'
            ],
            [
                'name' => 'ammeta',
                'type' => 'ND'
            ],
            [
                'name' => 'amseohtmlsitemap',
                'type' => 'ND'
            ],
            [
                'name' => 'amseotoolkit',
                'type' => 'ND'
            ],
            [
                'name' => 'amasty_seourl',
                'type' => 'ND'
            ],
            [
                'name' => 'amseorichdata',
                'type' => 'ND'
            ],
            [
                'name' => 'amsorting',
                'type' => 'ND'
            ],
            [
                'name' => 'amxmlsitemap',
                'type' => 'ND'
            ],
            [
                'name' => 'region',
                'type' => 'ND'
            ],
            [
                'name' => 'multicurrency',
                'type' => 'ND'
            ],
            [
                'name' => 'client',
                'type' => 'ND'
            ],
            [
                'name' => 'widget',
                'type' => 'ND'
            ],
            [
                'name' => 'bss_geoip',
                'type' => 'ND'
            ],
            [
                'name' => 'bss_geoip_currency',
                'type' => 'ND'
            ],
            [
                'name' => 'bss_sociallogin',
                'type' => 'ND'
            ],
            [
                'name' => 'bss_store_flag',
                'type' => 'ND'
            ],
            [
                'name' => 'settings',
                'type' => 'ND'
            ],
            [
                'name' => 'connector_api_credentials',
                'type' => 'ND'
            ],
            [
                'name' => 'sync_settings',
                'type' => 'ND'
            ],
            [
                'name' => 'abandoned_carts',
                'type' => 'ND'
            ],
            [
                'name' => 'connector_automation',
                'type' => 'ND'
            ],
            [
                'name' => 'connector_dynamic_content',
                'type' => 'ND'
            ],
            [
                'name' => 'transactional_emails',
                'type' => 'ND'
            ],
            [
                'name' => 'connector_configuration',
                'type' => 'ND'
            ],
            [
                'name' => 'connector_data_mapping',
                'type' => 'ND'
            ],

            [
                'name' => 'admin',
                'type' => 'DE'
            ],
            [
                'name' => 'design',
                'type' => 'DE'
            ],
            [
                'name' => 'dev',
                'type' => 'DE'
            ],
            [
                'name' => 'system',
                'type' => 'DE'
            ],
            [
                'name' => 'web',
                'type' => 'DE'
            ],
            [
                'name' => 'general',
                'type' => 'DE'
            ],
            [
                'name' => 'theme',
                'type' => 'DE'
            ],
            [
                'name' => 'currency',
                'type' => 'DE'
            ],
            [
                'name' => 'customer',
                'type' => 'DE'
            ],
            [
                'name' => 'cms',
                'type' => 'DE'
            ],
            [
                'name' => 'catalog',
                'type' => 'DE'
            ],
            [
                'name' => 'indexer',
                'type' => 'DE'
            ],
            [
                'name' => 'export',
                'type' => 'DE'
            ],
            [
                'name' => 'cataloginventory',
                'type' => 'DE'
            ],
            [
                'name' => 'sales',
                'type' => 'DE'
            ],
            [
                'name' => 'payment',
                'type' => 'DE'
            ],
            [
                'name' => 'sales_email',
                'type' => 'DE'
            ],
            [
                'name' => 'sales_pdf',
                'type' => 'DE'
            ],
            [
                'name' => 'dashboard',
                'type' => 'DE'
            ],
            [
                'name' => 'checkout',
                'type' => 'DE'
            ],
            [
                'name' => 'captcha',
                'type' => 'DE'
            ],
            [
                'name' => 'contact',
                'type' => 'DE'
            ],
            [
                'name' => 'oauth',
                'type' => 'DE'
            ],
            [
                'name' => 'carriers',
                'type' => 'DE'
            ],
            [
                'name' => 'trans_email',
                'type' => 'DE'
            ],
            [
                'name' => 'import',
                'type' => 'DE'
            ],
            [
                'name' => 'three_d_secure',
                'type' => 'DE'
            ],
            [
                'name' => 'google',
                'type' => 'DE'
            ],
            [
                'name' => 'sales_channels',
                'type' => 'DE'
            ],
            [
                'name' => 'analytics',
                'type' => 'DE'
            ],
            [
                'name' => 'shipping',
                'type' => 'DE'
            ],
            [
                'name' => 'multishipping',
                'type' => 'DE'
            ],
            [
                'name' => 'newrelicreporting',
                'type' => 'DE'
            ],
            [
                'name' => 'newsletter',
                'type' => 'DE'
            ],
            [
                'name' => 'promo',
                'type' => 'DE'
            ],
            [
                'name' => 'paypal',
                'type' => 'DE'
            ],
            [
                'name' => 'persistent',
                'type' => 'DE'
            ],
            [
                'name' => 'reports',
                'type' => 'DE'
            ],
            [
                'name' => 'url_rewrite',
                'type' => 'DE'
            ],
            [
                'name' => 'sendfriend',
                'type' => 'DE'
            ],
            [
                'name' => 'fraud_protection',
                'type' => 'DE'
            ],
            [
                'name' => 'sitemap',
                'type' => 'DE'
            ],
            [
                'name' => 'crontab',
                'type' => 'DE'
            ],
            [
                'name' => 'tax',
                'type' => 'DE'
            ],
            [
                'name' => 'msp_securitysuite_recaptcha',
                'type' => 'DE'
            ],
            [
                'name' => 'webapi',
                'type' => 'DE'
            ],
            [
                'name' => 'wishlist',
                'type' => 'DE'
            ]
        ];
    }
}
