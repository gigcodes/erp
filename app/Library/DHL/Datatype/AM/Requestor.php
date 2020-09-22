<?php

namespace App\Library\DHL\Datatype\GB;

use App\Library\DHL\Datatype\Base;

/**
 * Class Requestor
 *
 * @package Mtc\Dhl
 */
class Requestor extends Base
{
    /**
     * Is this object a subobject
     * @var boolean
     */
    protected $_isSubobject = true;

    /**
     * Parameters of the datatype
     * @var array
     */
    protected $params = [
        'AccountType' => [
            'type' => 'string',
            'required' => false,
            'subobject' => false,
            'comment' => 'Account type',
        ],
        'AccountNumber' => [
            'type' => 'string',
            'required' => false,
            'subobject' => false,
            'comment' => 'Account number',
        ],
        'RequestorContact' => [
            'type' => 'RequestorContact',
            'required' => false,
            'subobject' => true,
            'comment' => 'RequestorContact',
        ],
        'CompanyName' => [
            'type' => 'string',
            'required' => false,
            'subobject' => false,
            'comment' => 'Company Name',
        ],
    ];
}
