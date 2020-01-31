<?php


namespace micro\controllers;

use micro\models\User;
use Yii;
use PHPMailer\PHPMailer\PHPMailer;
use yii\web\Controller;
use Facebook;



/**
 * Class SiteController
 * @package micro\controllers
 */
class TestController extends Controller
{
    public function actionSignup()
    {

        $request = Yii::$app->request;

        $email = $request->get('email');
        $password = $request->get('password');
        $password = password_hash($password, PASSWORD_DEFAULT);
        $signup_token = uniqid();

        $user = User::findOne(['email' => $email]);

        if(!$user)
        {
            $model = new User();

            $model->email = $email;
            $model->password = $password;
            $model->signup_token = $signup_token;
            $model->gender = s;
            $model->phone = sd;
            $model->age = 1;
            
            if(($model->validate()) && ($model->save()))
            {
                echo "Вы успешно зарегистрировались!";
            }
            else{
                $errors = $model->errors;
                return $errors;
            }


            // Отправка на почту

            $mail = new PHPMailer;

            $mail->CharSet = "UTF-8";

            $mail->isSMTP();
            $mail->Host = 'smtp.yandex.ru';
            $mail->Port = 465;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';

            $mail->Username = 'arman.shukanov@fokin-team.ru';
            $mail->Password = 'arman_shukanov';

            $mail->setFrom('arman.shukanov@fokin-team.ru');
            $mail->addAddress($email);
            $mail->Subject = 'Подтверждение аккаунта';
            $mail->Body = 'Для подтверждения перейдите по ссылке: '. $_SERVER['HTTP_HOST'] . "/verify/?token=" . $signup_token;

            $mail->isHTML(true);

            $mail->send();
        }
        else 
        {
            return"пользователь с такой почтой уже существует";
        }

    }

    public function actionFbToken() {

		$fb = new Facebook( [
			'app_id'                => 559755891418423,
			'app_secret'            => f5a86f378bca716435d1db271695dedd,
			'default_graph_version' => 'v2.4',
		]);;

		$helper = $fb->getRedirectLoginHelper();

		// Коллбэк от ФВ
		if(\Yii::$app->request->get('code')) {
			try {

				if(!$accessToken = $helper->getAccessToken()) throw new UserException("No access token");

				$oAuth2Client = $fb->getOAuth2Client();
				$tokenMetadata = $oAuth2Client->debugToken( $accessToken );
				$tokenMetadata->validateAppId(APP_ID);
				$tokenMetadata->validateExpiration();
				if (!$accessToken->isLongLived()) 
                                      $accessToken = $oAuth2Client->getLongLivedAccessToken( $accessToken );

				// В мессадже чистый токен
				$message = $accessToken->getValue();

			} catch (FacebookResponseException $e ) {
				$message = 'Graph returned an error: ' . $e->getMessage();
			} catch (FacebookSDKException $e ) {
				$message = 'Facebook SDK returned an error: ' . $e->getMessage();
			} catch (UserException $e) {
				$message = "UserException ".$e->getMessage();
			}

			echo $message;

		} else {
			// Запрос токена
			$login_url = $helper->getLoginUrl(Url::to('test/fb-token', 1), [
				'user_managed_groups', // !!! крайне важное разрешение чтобы публиковать в свои группы
				'publish_actions',
				'manage_pages',
				'publish_pages'
			]);

			// Редиректим на ФБ, который возвращается сюдаже
			return $this->redirect($login_url);
		}
	}
}