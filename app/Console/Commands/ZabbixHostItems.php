<?php

namespace App\Console\Commands;

use App\Host;
use App\HostItem;
use App\ZabbixHistory;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ZabbixHostItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:zabbixhostitems';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store all host items';

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
        $auth_key = $this->login_api();
        $get_hosts = Host::get();
        if (! is_null($get_hosts)) {
            foreach ($get_hosts as $host) {
                $item_interrupt = $this->item_api($auth_key, $host->hostid, 'Interrupts per second');
                if (! empty($item_interrupt)) {
                    HostItem::where('hostid', $host->hostid)->update(['interrupts_per_second' => $item_interrupt->lastvalue, 'item_id' => $item_interrupt->itemid]);
                    ZabbixHistory::create(['hostid'=>$host->hostid,'interrupts_per_second' => $item_interrupt->lastvalue, 'item_id' => $item_interrupt->itemid]);
                    
                }
                $item_free_inode = $this->item_api($auth_key, $host->hostid, 'Free inodes in');
                if (!empty($item_free_inode)) {
                    HostItem::where('hostid', $host->hostid)->update(['free_inode_in' => $item_free_inode->lastvalue, 'item_id' => $item_interrupt->itemid]);
                    ZabbixHistory::create(['hostid'=> $host->hostid,'free_inode_in' => $item_free_inode->lastvalue, 'item_id' => $item_interrupt->itemid]);
                }
                $item_space_utilization = $this->item_api($auth_key, $host->hostid, 'Space Utilization');
                if (! empty($item_space_utilization)) {
                    HostItem::where('hostid', $host->hostid)->update(['space_utilization' => $item_space_utilization->lastvalue, 'item_id' => $item_interrupt->itemid]);
                    ZabbixHistory::create(['hostid'=> $host->hostid,'space_utilization' => $item_space_utilization->lastvalue, 'item_id' => $item_interrupt->itemid]);
                }
                $item_total_space = $this->item_api($auth_key, $host->hostid, 'Total Space');
                if (! empty($item_total_space)) {
                    HostItem::where('hostid', $host->hostid)->update(['total_space' => $item_total_space->lastvalue, 'item_id' => $item_interrupt->itemid]);
                    ZabbixHistory::create(['hostid'=> $host->hostid,'total_space' => $item_total_space->lastvalue, 'item_id' => $item_interrupt->itemid]);
                }
                $item_used_space = $this->item_api($auth_key, $host->hostid, 'Used Space');
                if (! empty($item_used_space)) {
                    HostItem::where('hostid', $host->hostid)->update(['used_space' => $item_used_space->lastvalue, 'item_id' => $item_interrupt->itemid]);
                    ZabbixHistory::create(['hostid'=> $host->hostid,'used_space' => $item_used_space->lastvalue, 'item_id' => $item_interrupt->itemid]);
                }
                $item_available_memory = $this->item_api($auth_key, $host->hostid, 'Available Memory');
                if (! empty($item_available_memory)) {
                    HostItem::where('hostid', $host->hostid)->update(['available_memory' => $item_available_memory->lastvalue, 'item_id' => $item_interrupt->itemid]);
                    ZabbixHistory::create(['hostid'=>$host->hostid,'available_memory' => $item_available_memory->lastvalue, 'item_id' => $item_interrupt->itemid]);
                }
                $item_available_memory_in = $this->item_api($auth_key, $host->hostid, 'Available Memory in');
                if (! empty($item_available_memory_in)) {
                    ZabbixHistory::create(['hostid' => $host->hostid,'available_memory_in' => $item_available_memory_in->lastvalue, 'item_id' => $item_interrupt->itemid]);
                }
                $item_cpu_idle_time = $this->item_api($auth_key, $host->hostid, 'CPU Idle Time');
                if (! empty($item_cpu_idle_time)) {
                    HostItem::where('hostid', $host->hostid)->update(['cpu_idle_time' => $item_cpu_idle_time->lastvalue, 'item_id' => $item_interrupt->itemid]);
                    ZabbixHistory::create(['hostid'=> $host->hostid,'cpu_idle_time' => $item_cpu_idle_time->lastvalue, 'item_id' => $item_interrupt->itemid]);
                }

                $item_cpu_utilization = $this->item_api($auth_key, $host->hostid, 'CPU Utilizatio');
                if (! empty($item_cpu_utilization)) {
                    HostItem::where('hostid', $host->hostid)->update(['cpu_utilization' => $item_cpu_utilization->lastvalue, 'item_id' => $item_interrupt->itemid]);
                    ZabbixHistory::create(['hostid'=> $host->hostid,'cpu_utilization' => $item_cpu_utilization->lastvalue, 'item_id' => $item_interrupt->itemid]);
                }
             
            }
           
        }
    }

    public function login_api()
    {
        //Get API ENDPOINT response
        $curl = curl_init(env('ZABBIX_HOST').'/api_jsonrpc.php');
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
        curl_close($curl);
        $results = json_decode($result);

        if (isset($results[0]->result)) {
            return $results[0]->result;
        } else {
            \Log::channel('general')->info(Carbon::now().$results[0]->error->data);

            return 0;
        }
    }

    public function item_api($auth_key, $hostid, $name)
    {
        //Get API ENDPOINT response
        $curl = curl_init(env('ZABBIX_HOST').'/api_jsonrpc.php');
        $data = [
            'jsonrpc' => '2.0',
            'method' => 'item.get',
            'params' => [
                'hostids' => $hostid,
                'limit' => 1,
                'search' => [
                    'name' => $name,
                ],
                'sortfield' => 'name',
            ],
            'auth' => $auth_key,
            'id' => 1,
        ];
        $datas = json_encode([$data]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);
        $results = json_decode($result);
        return $results[0]->result[0];
    }
}
