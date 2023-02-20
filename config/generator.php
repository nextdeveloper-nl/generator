<?php

/**
 * This file is part of the NextDeveloper Generator library.
 *
 * (c) Harun Baris Bulut <baris.bulut@plusclouds.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'structure' =>  [
        'config',
        'docs',
        'resources' =>  [
            'views'
        ],
        'schemas',
        'src'   =>  [
            'Broadcasts',
            'Common'    =>  [
                'Cache',
                'Enums'
            ],
            'Console'   =>  [
                'Commands'
            ],
            'Database'  =>  [
                'Filters',
                'GlobalScopes',
                'Migrations',
                'Models',
                'Observers',
                'Seeders',
                'Traits'
            ],
            'Events',
            'EventHandlers',
            'Exceptions',
            'Helpers',
            'Http'  =>  [
                'Controllers',
                'Transformers',
                'Requests',
                'Middlewares'
            ],
            'Initiators',
            'Jobs',
            'Notifications',
            'Policies',
            'Services',
        ],
        'tests',
        'workers'
    ],

    'pagination'    =>  [
        //'perPage'   =>  0,    // This is the number of records we return by default. 0 for unlimited. Default is 20
        'perPage'   =>  20
    ],
];