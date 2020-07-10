<?php

return [
    'api'     => [
        'DHL_ID'          => env('DHL_ID', ''),
        'DHL_KEY'         => env('DHL_KEY', ''),
        'DHL_USER'        => env('DHL_USER', ''),
        'DHL_PASSWORD'    => env('DHL_PASSWORD', ''),
        'DHL_ACCOUNT'     => env('DHL_ACCOUNT', ''),
        'DHL_COUNTRY'     => env('DHL_COUNTRY', 'ZA'),
        'DHL_CURRECY'     => env('DHL_CURRECY', 'USD'),
        'DHL_COUNTRYCODE' => env('DHL_COUNTRYCODE', ''),
        'DHL_POSTALCODE'  => env('DHL_POSTALCODE', ''),
        'DHL_CITY'        => env('DHL_CITY', ''),
    ],
    'shipper' => [
        "street"       => "Woluwelaan 151",
        "city"         => "Diegem",
        "postal_code"  => "1831",
        "country_code" => "BE",
        "person_name"  => "Pravin Solanki",
        "company_name" => "LUXURY UNLIMITED",
        "phone"        => "971502609192",
    ],
];
