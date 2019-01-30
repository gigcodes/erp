<?php
/**
 * Created by PhpStorm.
 * User: risha
 * Date: 1/29/2019
 * Time: 3:38 PM
 */

namespace App\Services\Scrap;


interface ScrapInterface
{
    /**
     * @param $url
     * @param string $method
     * @return mixed
     */
    public function getContent($url, $method = 'GET');
}