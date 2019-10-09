<?php

return [
  'api_keys' => [
        [
            'number' => '971562744570',
            'key' => 'Z802FWHI8E2OP0X120QR'
        ],
        [
            'number' => '919152731483',
            'key' => '1KWDP9M0LDCKY9O6QQW8'
        ],
        [
            'number' => '919004418502',
            'key' => 'YRM9TGDQ4JPSFYRQML28'
        ],

    ],
    'media_path' => realpath(implode(DIRECTORY_SEPARATOR, array(__DIR__, "..", "public", "apiwha", "media"))),
    'instances' => [
        "919004780634" => [
            "instance_id" => 43281,
            "token" => "yi841xjhrwyrwrc7"
        ],
        "971545889192" => [
            "instance_id" => 43112,
            "token" => "vbi9bpkoejv2lvc4"
        ],
        "971562744570" => [
            "instance_id" => 55202,
            "token" => "42ndn0qg5om26vzf"
        ],
        "971547763482" => [
            "instance_id" => 55211,
            "token" => "3b92u5cbg215c718"
        ],
        "971502609192" => [
            "instance_id" => 62439,
            "token" => "jdcqh3ladeuvwzp4"
        ],
        // Default
        "0" => [
            "instance_id" => 62439,
            "token" => "jdcqh3ladeuvwzp4"
        ],
//        OLD 04
//        "919152731483" => [
//            "instance_id" => 55211,
//            "token" => "3b92u5cbg215c718"
//        ],
    ]
];