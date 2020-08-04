<?php

namespace App;
use App\plesk\PleskServer;
use App\plesk\PleskClient;



class PleskHelper
{
    private $_options = null;

    function __construct()
    {
        $this->_options = [
            'username' => 'admin',
            'password' => '01Va2jc%',
            'ip' => '167.71.10.70',
        ];
    }



    public function getDomains() {

        $client = new \App\plesk\PleskClient($this->_options['ip']);
        $client->setCredentials($this->_options['username'], $this->_options['password']);

        $field = null;
        $value = null;
        $dns = $client->dns()->getAll($field, $value);
        $domains = [];        
        if(count($dns) > 0) {
            for($i=0;$i < count($dns);$i++) {
                try {
                    $str = substr($dns[$i]->host,0, -1);
                    $d = $client->server()->getDomain($str);
                    $temp = [];
                    $temp['id'] = $d['id'];
                    $temp['name'] = $d['filter-id'];
                    if(!in_array($temp, $domains)){
                        $domains[]=$temp;
                    }
                    
                }
                catch(\Exception $e) {
                    // echo $e;
                }
            }
        }

        return $domains;
    }

    public function createMail($name,$id,$mailbox,$pass) {
       
        $client = new \PleskX\Api\Client($this->_options['ip']);
        $client->setCredentials($this->_options['username'], $this->_options['password']);
        
        $response = $client->mail()->create($name,$id,$mailbox,$pass);

        return $response;
    }

    public function getMailAccounts($id) {
        $client = new \App\plesk\PleskClient($this->_options['ip']);
        $client->setCredentials($this->_options['username'], $this->_options['password']);
        $response = $client->mail()->get($id);
        $accounts = [];
        for($i=0;$i < count($response);$i++) {
                $temp['id'] = $response[$i]->id;
                $temp['name'] = $response[$i]->name;
                $accounts[]=$temp;
        }
        return $accounts;
    }


    public function viewDomain($domain_id) {

    }
}