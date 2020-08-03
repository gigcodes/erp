<?php
// Copyright 1999-2019. Plesk International GmbH.

namespace App\plesk;
use PleskX\Api\Struct\Mail as Struct;

class PleskMail extends \PleskX\Api\Operator\Mail
{

    /**
     * @return array
     */


    public function get($siteId) {
        $packet = $this->_client->getPacket();
        $getinfo = $packet->addChild('mail')->addChild('get_info');
        ;
        $getinfo->addChild('filter')->addChild('site-id',$siteId);
        $getinfo->addChild('mailbox');
        $response = $this->_client->request($packet, \PleskX\Api\Client::RESPONSE_FULL);
        $items = [];
        foreach ($response->xpath('//result') as $xmlResult) {
            $item = new Struct\Info($xmlResult->mailname);
            $item->id = (int)$item->id;
            $items[] = $item;
        }
        return $items;
    }
}
