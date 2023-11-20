<?php

namespace App\Console\Commands;

use App\Host;
use App\HostItem;
use Carbon\Carbon;
use App\LogRequest;
use Illuminate\Console\Command;

class ZabbixStore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:zabbix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store Hosts from Zabbix';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Auth code
        $auth_key = $this->login_api();
        if ($auth_key != 0) {
            $hosts = $this->host_api($auth_key);
            if (! empty($hosts)) {
                $hostItems = [];
                foreach ($hosts as $host) {
                    $check_if_host_id_exist = Host::where('hostid', $host->hostid)->first();
                    if (! is_null($check_if_host_id_exist)) {
                        $hostarray = [
                            'name' => $host->name,
                            'host' => $host->host,
                        ];
                        //Host::where('hostid', $host->hostid)->update($hostarray);
                        $items = $this->item_api($auth_key, $host->hostid);
                    } else {
                        $hostarray = [
                            'hostid' => $host->hostid,
                            'name' => $host->name,
                            'host' => $host->host,
                        ];
                        $last_host_id = Host::create($hostarray);
                        $hostItems[] = [
                            'host_id' => $last_host_id->id,
                            'hostid' => $host->hostid,
                        ];
                    }
                }
                if (count($hostItems)) {
                    HostItem::Insert($hostItems);
                }
            }
        }
    }

    public function login_api()
    {
        //Get API ENDPOINT response
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = env('ZABBIX_HOST') . '/api_jsonrpc.php';
        $curl = curl_init($url);
        $data = [
            'jsonrpc' => '2.0',
            'method' => 'user.login',
            'params' => [
                'username' => env('ZABBIX_USERNAME'),
                'password' => env('ZABBIX_PASSWORD'),
            ],
            'id' => 1,
        ];

        $datas = json_encode([$data]);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        LogRequest::log($startTime, $url, 'POST', json_encode($datas), $result, $httpcode, \App\Console\Commands\ZabbixStore::class, 'login_api');

        $results = json_decode($result);

        if (!isset($results[0])){
            \Log::channel('general')->info("Response error: " . Carbon::now(). " " . json_encode($results));
            return 0;
        }

        try {
            if (isset($results[0]->result)) {
                return $results[0]->result;
            } else {
                \Log::channel('general')->info(Carbon::now() . $results[0]->error->data);
                return 0;
            }
        } catch (\Exception|\Throwable $e) {
            return 0;
        }
    }

    public function host_api($auth_key)
    {
        \Log::error('Start fetching items from Zabbix API (host_api)');
        //Get API ENDPOINT response
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = env('ZABBIX_HOST') . '/api_jsonrpc.php';
        $curl = curl_init($url);
        $data = [
            'jsonrpc' => '2.0',
            'method' => 'host.get',
            'params' => [

            ],
            'auth' => $auth_key,
            'id' => 1,
        ];
        $datas = json_encode([$data]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        LogRequest::log($startTime, $url, 'GET', json_encode($datas), $result, $httpcode, \App\Console\Commands\ZabbixStore::class, 'host_api');
        \Log::error('Fetched data from (host_api): ' . $result);
        $results = json_decode($result);

        return $results[0]->result;
    }

    public function item_api($auth_key, $hostid)
    {
        \Log::error('Start fetching items from Zabbix API (item_api)');
        //Get API ENDPOINT response
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = env('ZABBIX_HOST') . '/api_jsonrpc.php';
        $curl = curl_init($url);
        $data = [
            'jsonrpc' => '2.0',
            'method' => 'item.get',
            'params' => [
                'hostids' => $hostid,
            ],
            'auth' => $auth_key,
            'id' => 1,
        ];
        $datas = json_encode([$data]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        LogRequest::log($startTime, $url, 'GET', json_encode($datas), $result, $httpcode, \App\Console\Commands\ZabbixStore::class, 'item_api');

        $results = json_decode($result);

        return $results[0]->result;
    }
}
