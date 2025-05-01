<?php

use App\Enums\StatusEnum;

return [

    'app_name'    => "TokoPoin",
    'software_id' => "CRT0001==",
    'cacheFile'   => 'Q2FydHVzZXI=',

    'core' => [
        'appVersion' => '2.1',
        'minPhpVersion' => '8.1'
    ],

    'requirements' => [

        'php' => [
            'Core',
            'bcmath',
            'openssl',
            'pdo_mysql',
            'mbstring',
            'tokenizer',
            'json',
            'curl',
            'gd',
            'zip',
            'mbstring',

        ],
        'apache' => [
            'mod_rewrite',
        ],

    ],
    'permissions' => [
        '.env'     => '666',
        'storage'     => '666',
        'bootstrap/cache/'       => '775',
    ],

];
