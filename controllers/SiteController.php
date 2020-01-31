<?php
/**
 * Created by PhpStorm.
 * User: sotoros
 * Date: 14.11.2019
 * Time: 1:12
 */

namespace micro\controllers;

use micro\models\Mailing;
use micro\models\Recipient;
use micro\models\MailingForm;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use micro\jobs\MailingJob;


/**
 * Class SiteController
 * @package micro\controllers
 */
class SiteController extends Controller
{
	public function behaviors()
	{
		// удаляем rateLimiter, требуется для аутентификации пользователя
		$behaviors = parent::behaviors();

		return $behaviors;
	}

	/**
	 * Function executing before all action
	 *
	 * - set json format for response
	 *
	 * @param $action
	 * @return bool
	 * @throws \yii\web\BadRequestHttpException
	 */
	public function beforeAction($action)
	{
		if (parent::beforeAction($action)) {
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Create mailing
	 *
	 * @return array|void
	 */
	public function actionMailing()
	{
		return "Hi";
	}
}