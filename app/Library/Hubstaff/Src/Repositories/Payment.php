<?php 

namespace App\Library\Hubstaff\Src\Repositories;

use Curl\Curl;

class Payment {

    
    private $appToken;
    private $authToken;
    private $url = 'https://api.hubstaff.com/v1/team_payments';


    /**
    * Constructor to initialize appToken and authToken
    * 
    * @param appToken [string]  authToken [string]
    * @return object this 
    */

    public function __construct($appToken, $authToken){
        
        $this->appToken = $appToken;
        $this->authToken = $authToken;

        return $this;
    }

    /**
    * Retrieve team payments
    * 
    * @param startTime [Date ISO 86010]  stopTime [Date ISO 86010] organizatuions [array of organization IDs]   users [array of user IDs] offset [integer]
    * @return object screenshots 
    */
    
    public function getTeamPayments($startTime, $stopTime, $organizationIds = [], $userIds = [], $offset = []){
        
        $curl = new Curl();
        $curl->setHeader('App-Token', $this->appToken);
        $curl->setHeader('Auth-Token', $this->authToken);
        
        $curl->get($this->url , array(
            'start_time' => date(DATE_ISO8601, strtotime($startTime)),
            'stop_time' => date(DATE_ISO8601, strtotime($stopTime)),
            'organizations' => implode(",", $organizationIds),
            'users' => implode(",", $userIds),
            'offset' => $offset
        ));
        if ($curl->error) {
            echo 'errorCode' . $curl->error_code;
            die();
        }
        else {
            $response = json_decode($curl->response);
        }
        
        $curl->close();

        return $response;
    }
}