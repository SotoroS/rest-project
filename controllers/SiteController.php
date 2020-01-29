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
		$request = Yii::$app->request;
		$mailingFormModel = new MailingForm();

		if ($request->isPost) {
			$mailingFormModel->name = $request->post('name');
			$mailingFormModel->desc = $request->post('desc');
			$mailingFormModel->title = $request->post('title');

			// Загрузка csv
			$mailingFormModel->file = UploadedFile::getInstanceByName('file'); 

			if ($mailingModel = $mailingFormModel->save()) {
				$file = $this->utf8_fopen_read(Yii::$app->basePath . '/web/api/' . $mailingFormModel->path, "r");

				$recipients = [];

				while (($data = fgetcsv($file, 1000, ';')) !== false) {
					$recipients[] = $data;
				}

				unset($recipients[0]);

				foreach ($recipients as $recipient) {
					$recipientModel = new Recipient();

					$recipientModel->first_name = $recipient[0];
					$recipientModel->last_name = $recipient[1];
					$recipientModel->parameter1 = $recipient[2];
					$recipientModel->parameter2 = $recipient[3];
					$recipientModel->parameter3 = $recipient[4];
					$recipientModel->email = $recipient[5];
					$recipientModel->status = Recipient::NEW_RECIPIENT;

					$recipientModel->link('mailing', $mailingModel);
				}

				// Загрузка файлов
				$attachments = UploadedFile::getInstancesByName('attachments');  

				// Проверка на наличие файлов загрузки
				if(!empty($attachments))
				{
					// Создание папки под каждый загрузочный файл
					mkdir(Yii::$app->basePath . '/web/api/uploads/' . $mailingModel->getPrimaryKey()); 

					// Запись файлов в папки с id их модельки
					foreach ($attachments as $attachment) {
						$attachment->saveAs(Yii::$app->basePath . '/web/api/uploads/' . $mailingModel->getPrimaryKey() . '/' . uniqid() . '.' . $attachment->extension);
					}
				}		

				return $recipients;
			} else {
				return false;
			}


		}
	}

	public function actionGetMailing() {
		$mailings = Mailing::find()->all();

		$arrMailings = [];

		foreach ($mailings as $mailing) {
			$arrMailings[] = [
				'id' => $mailing->id,
				'name' => $mailing->name,
				'desc' => $mailing->desc,
				'title' => $mailing->title,
				'total' => $mailing->getRecipients()->count(),
				'done' => $mailing->getRecipients()->where(['<>', 'status', Recipient::NEW_RECIPIENT])->count(),
				'date' => $mailing->date,
				'status' => $mailing->status
			];
		}

		return $arrMailings;
	}

	public function actionStart() {
		$id = Yii::$app->request->post('id');

		$mailingModel = Mailing::findOne($id);
		$mailingModel->status = Mailing::EXECUTE;

		if ($mailingModel->save()) {
			Yii::$app->queue->push(new MailingJob([
				'id' => $id
			]));

			return true;
		} else {
			return false;
		}
	}

	public function actionDelete() {
		$id = Yii::$app->request->post('id');

		$mailingModel = Mailing::findOne($id);

		foreach ($mailingModel->recipients as $recipient) {
			$recipient->delete();
		}

		$mailingModel->delete();
	}

	public function actionTest() {

		$model = new Mailing();
		return $model->getUrl(17);
	}

	private function utf8_fopen_read($fileName)
	{
		$fc = iconv('windows-1251', 'utf-8', file_get_contents($fileName));
		$handle = fopen("php://memory", "rw");

		fwrite($handle, $fc);
		fseek($handle, 0);

		return $handle;
	}
}