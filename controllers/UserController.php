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
                    // TODO: return token
                    return 'You are logged in.';
                } else {
                    // TODO: return false
                    return 'Invalid password.';
                }
            }
            else
            {
                // TODO: return false
                return 'Not exist user with this email.';
            }
        } else {
            // TODO: return false
            return 'No data received.';
        }
    }

    // TODO: add comment
    public function actionLoginFacebook()
    {
        // TODO: Move to param in config.php
        $ID = 559755891418423;
        $SEKRET = "f5a86f378bca716435d1db271695dedd";
<<<<<<< HEAD
        $URL = 'https://rest.fokin-team.ru/user/login-facebook';
=======
>>>>>>> 2b84050ef88e0e5b3ad5c54801cb406a4a1dc452

        $fb = new Facebook\Facebook([
            'app_id' => $ID,
            'app_secret' => $SEKRET,
            'default_graph_version' => 'v3.2',
        ]);
        
        $helper = $fb->getRedirectLoginHelper();
        
<<<<<<< HEAD
        $permissions = ['email'];
        $loginUrl = $helper->getLoginUrl('https://rest.fokin-team.ru/user/login-facebook', $permissions);
    
	$code = Yii::$app->request->get('code');

	if(isset($code)){        
	    //return $code;
    	    try {
    	        $response = $fb->get('me?access_token='.$code);
	        $resp = $response->getDecodeBody();
	        //$fb->setDefaultAccessToken($accessToken);
	        echo $resp;
    	    } catch(Facebook\Exceptions\FacebookResponseException $e){
		    echo 'Graph returned an error: ' . $e->getMessage();
		    exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e){
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
    		exit;
    	    }    
    	}
    	
	return $loginUrl;
        //$helper = $fb->getRedirectLoginHelper();
        
        //$permissions = ['email']; // Optional permissions
        //$loginUrl = $helper->getLoginUrl('https://rest.fokin-team.ru/user/login-facebook', $permissions);
        
        //$request = Yii::$app->request;
	//if ($request->get('code'))
	//{
	//    $params = array(
	//	'client_id' => $ID,
	//	'redirect_uri' => 'https://rest.fokin-team.ru/user/login-facebook',
	//	'client_secret' => $SEKRET,
	//	'code' => $_GET['code']
	//	);
	    //$url = 'https://graph.facebook.com/v5.0/oauth/access_token';        

	    //$token = file_get_contents('https://graph.facebook.com/v5.0/oauth/access_token?client_id=559755891418423&redirect_uri=rest.fokin-team.ru&client_secret=f5a86f378bca716435d1db271695dedd&code='.$_GET['code']);
	    //var_dump($URL);
    	    //$tokenInfo = null;
    	    //parse_str(file_get_contents($url . '?' . http_build_query($params)), $tokenInfo);
=======
        $loginUrl = $helper->getLoginUrl('rest.fokin-team.ru/user/login-facebook', ['email']);
>>>>>>> 2b84050ef88e0e5b3ad5c54801cb406a4a1dc452
        
    	//    var_dump());
    	//}
    }

    // TODO: Add comment
    public function actionLoginWithGoogle()
    {
        $request = Yii::$app->request;

        //Enter you google account credentials
        $g_client = new Google_Client();

        // TODO: Move to param in config.php
        $g_client->setClientId("156874812665-unh00vf96tmf4msn0j43fhie0b69k6ke.apps.googleusercontent.com");
        $g_client->setClientSecret("0qepssGons1TcyctkXfW-IPO");
        $g_client->setRedirectUri("https://rest.fokin-team.ru/user/login-with-google");
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

            if(!is_null($user)) {
                $model = new User();

                $model->email = $email;
                $model->signup_token = uniqid();
                $model->verified = 1;
                $model->access_token = $token['access_token'];
                
                if ($model->save()) {
                    // TODO: return access token
                } else {
                    return false;
                }
            } else {
               return false;
            }
        } else {
            return $auth_url;
        }
    }
}