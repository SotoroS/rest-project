<?php
/**
 * Created by PhpStorm.
 * User: sotoros
 * Date: 15.11.2019
 * Time: 1:59
 */

return [
	'id' => 'donate',
	// basePath (базовый путь) приложения будет каталог `micro-app`
	'basePath' => __DIR__,
	// это пространство имен где приложение будет искать все контроллеры
	'controllerNamespace' => 'micro\controllers',
	// установим псевдоним '@micro', чтобы включить автозагрузку классов из пространства имен 'micro'
	'aliases' => [
		'@micro' => __DIR__,
	],
	'defaultRoute' => 'site/index',
	'controllerMap' => [
		'migrate' => [
			'class' => 'yii\console\controllers\MigrateController',
			'migrationPath' => null,
			'migrationNamespaces' => [
				'micro\migrations'
			],
		],
	],
	'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=localhost;dbname=rest',
			'username' => 'GodOfDB',
			'password' => 'hard_pass!',
			'charset' => 'utf8'
		],
	],
	'bootstrap' => [
		'gii',
	],
	'modules' => [
		'gii' => [
			'class' => 'yii\gii\Module',
		],
	],
];