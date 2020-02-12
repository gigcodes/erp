<?php

namespace App\Providers;

/**
 * This  provider is using for update the whatsapp number direct from database
 *
 *
 */

use Illuminate\Support\ServiceProvider;

class WhatsappConfigProvider extends ServiceProvider
{
    public static function getWhatsappConfigs()
    {
        try {
            $q = \DB::table("whatsapp_configs")->select([
                "number", "instance_id", "token", "is_customer_support", "status", "is_default",
            ])->where("instance_id", "!=", "")
                ->where("token", "!=", "")
                ->where("status", 1)
                ->orderBy("is_default", "DESC")
                ->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $q = null;
        }

        return $q;
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // we have to check with try catch so we don't have issue while running migration
        try {
            $instance = self::getWhatsappConfigs();
            $default  = [];
            $others   = [];
            if (!empty($instance)) {
                foreach ($instance as $inst) {
                    $array = [
                        "number"          => $inst->number,
                        "instance_id"     => $inst->instance_id,
                        "token"           => $inst->token,
                        "customer_number" => ($inst->is_customer_support == 1) ? true : false,
                    ];
                    if ($inst->is_default == 1) {
                        $default[0] = $array;
                    }
                    $others[$inst->number] = $array;

                }
                // merge array to default instances and update the config file
                $nos = array_merge($default, $others);
                if(!empty($nos)) {
                    config(['apiwha.instances' => $nos]);
                }
            }
        } catch (\Exeception $e) {

        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
