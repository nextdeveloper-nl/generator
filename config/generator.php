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
		'backup',
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
                'Transformers'  =>  [
                    'AbstractTransformers'
                ],
                'Requests',
                'Middlewares'
            ],
            'Initiators',
            'Jobs',
            'Notifications',
            'Policies',
            'Services' => [
                'AbstractServices'
            ],
            'WebSockets'
        ],
        'tests' => [
            'Database'  =>  [
                'Models',
            ],
            'Events'    =>  [
                'Models',
                'Http',
                'Console'
            ]
        ],
        'workers'
    ],

    'pagination'    =>  [
        //'perPage'   =>  0,    // This is the number of records we return by default. 0 for unlimited. Default is 20
        'perPage'   =>  20
    ],

    'action-events' =>  [
        'events'    =>  [
            'retrieved',
            'created',
            'creating',
            'saving',
            'saved',
            'updating',
            'updated',
            'deleting',
            'deleted',
            'restoring',
            'restored'
        ],
        'handlers'  =>  [
            'creating',
            'created',
            'updating',
            'updated',
            'deleting',
            'deleted'
        ]
    ],
        /* Relations diye açıp representative in user olduğunu ekle 
        [
        'representative' => 'user'
        ]*/
    'enableBroadcast'   =>  true,

    'extend'    =>  [
        'model' => '\NextDeveloper\IAM\Database\Abstract\AuthorizationModel'
    ],

    'modules'   =>  [
        [
            'name'      =>  'IAM',
            'prefix'    =>  'iam',
            'tables'    =>  'iam_*',
            'namespace' =>  'NextDeveloper',
            'generate'  =>  true,
        ],
        [
            'name'      =>  'Commons',
            'prefix'    =>  'common',
            'tables'    =>  'common_*',
            'namespace' =>  'NextDeveloper',
            'generate'  =>  false
        ]
    ],
];