<?php
/**
 * Created by PhpStorm.
 * User: sotoros
 * Date: 14.11.2019
 * Time: 1:11
 */

return [
	'id' => 'donate',
	'basePath' => __DIR__,
	'runtimePath' => __DIR__ . '/runtime',
	'controllerNamespace' => 'micro\controllers',
	'aliases' => [
		'@micro' => __DIR__,
	],
	'params' => [
		'here_api_key' => 'GIGsSEJb9m1LlcOOpL6jQSP-Mz51UEaV-kGj4orep1k',
		'email_login' => '',
		'email_pass' => '',
		'email_port' => '',
		'email_host' => '',
		'email_from' => '',
		'google_client_id' => '156874812665-unh00vf96tmf4msn0j43fhie0b69k6ke.apps.googleusercontent.com',
		'google_client_secret' => '0qepssGons1TcyctkXfW-IPO',
		'google_redirect_uri' => 'https://rest.fokin-team.ru/user/login-google',
		'facebook_client_id' => 559755891418423,
		'facebook_client_secret' => 'f5a86f378bca716435d1db271695dedd',
		'facebook_client_uri' => 'https://rest.fokin-team.ru/user/login-facebook',
	],
	'defaultRoute' => 'site/index',
	'bootstrap' => ['log'],
	'components' => [
		'urlManager' => [
			'class' => 'yii\web\UrlManager',
			'showScriptName' => false,
			'enablePrettyUrl' => true,
			'rules' => [
				'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			],
		],
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=localhost;dbname=rest',
			'username' => 'GodOfDB',
			'password' => 'hard_pass!',
			'charset' => 'utf8',
		], 
		'request' => [
			'enableCookieValidation' => false,
			'enableCsrfValidation' => false,
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			]
		],
		'response' => [
			'formatters' => [
				\yii\web\Response::FORMAT_JSON => [
					'class' => 'yii\web\JsonResponseFormatter',
					'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
					'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
				],
			],
		],
		'mailer' => [
		    'class' => 'yii\swiftmailer\Mailer',
		      'transport' => [
		           'class' => 'Swift_SmtpTransport',
		            'host' => 'smtp.yandex.ru',
			    'username' => 'arman.shukanov@fokin-team.ru',
                           'password' => 'arman_shukanov',
                           'port' => '465',
                            'encryption' => 'ssl',
                    ],
                ],
		'address' => [
			'class' => 'micro\components\AddressComponent',
		],
		'user' => [
            'identityClass' => 'micro\models\User',
            'enableSession' => false,  
		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
					'levels' => ['info', 'error', 'warning'],
				],
			],
		],
	],

];