<?php

namespace App\Library\Hubstaff\Src;

/**
 * Package is using for maintane hubstaff
 *
 * @phpcredits()  https://github.com/techsemicolon/hubstaffphp
 *
 */

class Hubstaff
{

    protected static $instance = null;
    private $appToken;
    private $email;
    private $password;
    private $authToken;

    public static function getInstance()
    {

        if (is_null(self::$instance)) {
            self::$instance = new Hubstaff();
        }

        return self::$instance;
    }

    public function authenticate($appToken, $email, $password, $authToken = null)
    {

        $this->appToken = $appToken;
        $this->email    = $email;
        $this->password = $password;

        if (is_null($authToken)) {
            $token           = new Token();
            $this->authToken = $token->getAuthToken($appToken, $email, $password);
        } else {
            $this->authToken = $authToken;
        }

        return $this;

    }

    public function getRepository($repo)
    {

        $repo = ucwords(strtolower($repo));
        $repo = 'Hubstaff\\Repositories\\' . $repo;
        return new $repo($this->appToken, $this->authToken);
    }

}
