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
	'controllerNamespace' => 'micro\controllers',
	'aliases' => [
		'@micro' => __DIR__,
	],
	'defaultRoute' => 'site/index',
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
			'username' => 'root',
			'password' => '',
			
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
		],
		'queue' => [
			'class' => \yii\queue\db\Queue::class,
			'db' => 'db', // DB connection component or its config
			'tableName' => '{{%queue}}', // Table name
			'channel' => 'default', // Queue channel key
			'mutex' => \yii\mutex\MysqlMutex::class, // Mutex used to sync queries
			'ttr' => 12960000,
		],

	],

];