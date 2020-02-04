<?php

namespace micro\controllers;

use Yii;
use micro\models\User;
use PHPMailer\PHPMailer\PHPMailer;
use yii\web\Controller;
use Facebook;
use Google_Client;
use Google_Service_Oauth2;


/**
 * Class SiteController
 * @package micro\controllers
 */

class UserController extends Controller
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
     * Signup function
     * 
     * @param string $email - email address
     * @param string $password - password
     * 
     * @return string|bool
     */
    public function actionSignup()
    {
        $request = Yii::$app->request;

        $email = $request->get('email');
        $password = $request->get('password');

        if (is_null($email) || is_null($password)) {
            return [
                'error' => 'Fields are not filled'
            ];
        }

        $password = password_hash($password, PASSWORD_DEFAULT);
        $signup_token = uniqid();

        // Find user by email
        $user = User::findOne(['email' => $email]);

        // Registration user if not exist user
        if(!$user) {
            $model = new User();

            $model->email = $email;
            $model->password = $password;
            $model->signup_token = $signup_token;
            
            if(($model->validate()) && ($model->save())) {
                return true;
            } else {
                return $model->errors;
            }

            // Send email message for verify
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

    /**
     * Verify user function
     * 
     * @param string $token - verify token
     * 
     * @return string|bool
     */
    public function actionVerify()
    {
        $request = Yii::$app->request;

        $verification_code = $request->get('token');

        $user = User::findOne(['signup_token' => $verification_code]);

        if (!is_null($user)) {
            $user->verified = 1;

            if($user->update()) {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Login function
     * 
     * @param $email - email user
     * @param $password - password user
     * 
     * @return string|bool
     */
    public function actionLogin()
    {   
        // Checking for data availability
        $request = Yii::$app->request;

        // Checking for email in the received data
        if($request->get('email')) {
            $email = $request->get('email');
            $password = $request->get('password');

            // Checking the presence of a user in the database
            $user = User::findOne(['email' => $email]);
            
            if(!is_null($user)) {
                // Password verification
                if (password_verify($password, $user->password)) {
                    return uniqid();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Facebook authorization
     * 
     * @param $code - code user
     * 
     * @return string|bool
     */
    public function actionLoginFacebook()
    {
        if(!session_id()) {
            session_start();
        }
 
        $fb = new Facebook\Facebook([
            'app_id' => Yii::$app->params['facebook_client_id'],
            'app_secret' => Yii::$app->params['facebook_client_secret'],
            'default_graph_version' => 'v3.2',
        ]);
        
        $helper = $fb->getRedirectLoginHelper();
        
        //Create the url
        $permissions = ['email'];
        $loginUrl = $helper->getLoginUrl(Yii::$app->params['facebook_redirect_uri'], $permissions);
    
        // Getting the authorization  code
        $code = Yii::$app->request->get('code');

        if(!is_null($code)){        
            try {
                // Getting array accessToken
                $accessToken = $helper->getAccessToken();
                $response = $fb->get('/me?fields=email', $accessToken);
                // Getting user email
                $userEmail = $response->getGraphUser();
                $email = $userEmail['email'];
                // Getting string accessToken
                $value = $accessToken->getValue();

                $user = User::findOne(['email' => $email]);

                // Check user with such email in database
                if(is_null($user)){
                    $model = new User();

                    $model->email = $email;
                    $model->signup_token = uniqid();
                    $model->verified = 1;
                    $model->access_token = $value;
                    
                    if ($model->save()) {
                        return $value;
                    } else {
                        return false;
                    }
                } else {
                return false;
                }

            } catch(Facebook\Exceptions\FacebookResponseException $e){
                echo 'Graph returned an error: ' . $e->getMessage();
            } catch(Facebook\Exceptions\FacebookSDKException $e){
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
            }    
        }
        
    return $loginUrl;
    }

    
    /**
     * Login function
     * 
     * @param $code - authorization code returned by Google
     * 
     * @return string|bool
     */
    public function actionLoginWithGoogle()
    {
        $request = Yii::$app->request;

        //Enter you google account credentials
        $g_client = new Google_Client();
        $g_client->setClientId(Yii::$app->params['google_client_id']);
        $g_client->setClientSecret(Yii::$app->params['google_client_secret']);
        $g_client->setRedirectUri(Yii::$app->params['google_redirect_uri']);
        $g_client->setScopes("email");
        
        //Create the url
        $auth_url = $g_client->createAuthUrl();
        
        // Getting the authorization  code
        $code = $request->get('code');
        
        if(isset($code)) {
            // Getting the token
            $token = $g_client->fetchAccessTokenWithAuthCode($code);
            $g_client->setAccessToken($token);

            // Getting user information
            $oauth2 = new Google_Service_Oauth2($g_client);

            $userInfo = $oauth2->userinfo->get();
            $email = $userInfo->email;

            $user = User::findOne(['email' => $email]);

            // Check user with such email in database

            if(is_null($user)) {
                $model = new User();

                $model->email = $email;
                $model->signup_token = uniqid();
                $model->verified = 1;
                $model->access_token = $token['access_token'];
                
                if ($model->save()) {
                    return $token['access_token'];
                } else {
                    return false;
                }
            } else {
                return uniqid();
            }
        } else {
            return $auth_url;
        }
    }
}