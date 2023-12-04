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

    'traits'    =>   [
        'controller'    =>  [
            'tags'  =>  [
                //  This is the list of controllers that we should embed.
                'allowed_controllers'   =>  '*',
                //  This is the full trait class that we include in the project
                'class' =>  \NextDeveloper\Commons\Http\Traits\Tags::class,
                //  This is the class name that we add as trait
                'name'  =>  'Tags',
                //  this suffix is for route files for instance if we have an object with named hotels this
                //  stands for the hotels with id and then tags like;
                //  ..../hotels/{hotel-id}/tags
                'suffix'   =>   'tags',
                'get_method'   =>   'tags',
                'post_method'  =>   'saveTags'
            ],
            'addresses'  =>  [
                //  This is the list of controllers that we should embed.
                'allowed_controllers'   =>  '*',
                //  This is the full trait class that we include in the project
                'class' =>  \NextDeveloper\Commons\Http\Traits\Addresses::class,
                //  This is the class name that we add as trait
                'name'  =>  'Addresses',
                //  this suffix is for route files for instance if we have an object with named hotels this
                //  stands for the hotels with id and then tags like;
                //  ..../hotels/{hotel-id}/tags
                'suffix'   =>   'addresses',
                'get_method'   =>   'addresses',
                'post_method'  =>   'saveAddresses'
            ]
        ]
    ],

    'modules'   =>  [
        [
            'name'      =>  'Commons',
            'prefix'    =>  'common',
            'tables'    =>  'common_*',
            'views'     =>  'common_*',
            'namespace' =>  'NextDeveloper',
            'generate'  =>  false
        ],
        [
            'name'      =>  'IAM',
            'prefix'    =>  'iam',
            'tables'    =>  'iam_*',
            'views'     =>  'iam_*',
            'namespace' =>  'NextDeveloper',
            'generate'  =>  true,
        ],
        [
            'name'      =>  'Marketplace',
            'prefix'    =>  'marketplace',
            'tables'    =>  'marketplace_*',
            'views'     =>  'marketplace_*',
            'namespace' =>  'NextDeveloper',
            'generate'  =>  true,
        ],
        [
            'name'      =>  'CRM',
            'prefix'    =>  'crm',
            'tables'    =>  'crm_*',
            'views'     =>  'crm_*',
            'namespace' =>  'NextDeveloper',
            'generate'  =>  true,
        ],
        [
            'name'      =>  'IAAS',
            'prefix'    =>  'iaas',
            'tables'    =>  'iaas_*',
            'views'     =>  'iaas_*',
            'namespace' =>  'NextDeveloper',
            'generate'  =>  true,
        ],
        [
            'name'      =>  'Options',
            'prefix'    =>  'option',
            'tables'    =>  'option_*',
            'views'     =>  'option_*',
            'namespace' =>  'NextDeveloper',
            'generate'  =>  false,
        ],
        [
            'name'      =>  'Golf',
            'prefix'    =>  'golf',
            'tables'    =>  'golf_*',
            'views'     =>  'golf_*',
            'namespace' =>  'NextDeveloper',
            'generate'  =>  true,
        ],
        [
            'name'      =>  'Stay',
            'prefix'    =>  'stay',
            'tables'    =>  'stay_*',
            'views'     =>  'stay_*',
            'namespace' =>  'NextDeveloper',
            'generate'  =>  true,
        ]
    ],
];
