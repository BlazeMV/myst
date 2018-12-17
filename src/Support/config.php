<?php

return [
	'default' => 'sample',
	
	'bots' => [
		'sample' => [
			'username'                      => 'sample',
			'token'                         => "1234:abcdef",
			'async'                         => false,
			'process_edited_messages'       => true,
			'commands_param_separator'      => ' ',
			'cbq_param_separator'           => ' ',
			'unknown_command_reply_help'    => false,
			'engages_in'                    => [
				'private'                       => true,
				'group'                         => true,
				'supergroup'                    => true,
				'channel'                       => true,
			],
            'process'                       => [
                'commands'                      => true,
                'callback_queries'              => true,
                'texts'                         => true,
                'hashtags'                      => true,
                'mentions'                      => true,
                'conversations'                 => true,
            ],
			'commands'                      => [
			
			],
			'callback_queries'              => [
			
			],
			'conversations'                 => [
			
			],
			'texts'                      => [
			
			],
			'hashtags'                      => [
			
			],
			'mentions'                      => [
			
			]
		]
	],
    'db_connection' => env('MYST_DB_CONNECTION', env('DB_CONNECTION', 'mysql'))
];