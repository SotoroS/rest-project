<?php
/**
 * Created by PhpStorm.
 * User: sotoros
 * Date: 15.11.2019
 * Time: 2:24
 */

namespace micro\controllers;

use yii\rest\ActiveController;
use yii\web\Response;

class MailingController extends ActiveController
{
	public $modelClass = 'micro\models\Mailing';

	public function behaviors()
	{
		// удаляем rateLimiter, требуется для аутентификации пользователя
		$behaviors = parent::behaviors();
		$behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

		unset($behaviors['rateLimiter']);

		return $behaviors;
	}
}