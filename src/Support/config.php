<?php

return [
	'default' => 'sample',
	
	'bots' => [
		'sample' => [
			'username'                      => 'sample',
			'token'                         => "1234:abcdef",
			'async'                         => false,
			'process_edited_messages'       => true,
			'commands_param_seperator'      => ' ',
			'cbq_param_seperator'           => ' ',
			'unknown_command_reply_help'    => false,
			'engages_in'                    => [
				'private',
				'group',
				'supergroup',
				'channel'
			],
			'commands'                      => [
			
			],
			'callback_queries'              => [
			
			],
			'conversations'              => [
			
			]
		]
	]
];