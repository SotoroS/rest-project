<?php

namespace micro\controllers;

use Yii;

use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;

use micro\models\User;
use PHPMailer\PHPMailer\PHPMailer;
use micro\models\Address;

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
    
	$behaviors['access'] = [
	    'class' => AccessControl::className(),
	    'only' => ['login', 'signup', 'verify', 'update', 'login-facebook', 'login-google'],
	    'rules' => [
	        [
	            'actions' => ['login', 'signup', 'verify', 'login-facebook', 'login-google'],
		    'allow' => true,
		    'roles' => ['?'],
	        ],
	        [
	    	    'actions' => ['update'],
	    	    'allow' => true,
	    	    'roles' => ['@'],
	    	],
	    ],
	];
        
        // Возвращает результаты экшенов в формате JSON  
	$behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; 
		
	$behaviors['authenticator'] = [
	    'except' => ['login', 'signup', 'verify', 'login-facebook', 'login-goole'],
	    'class' => HttpBearerAuth::className()
	];

	return $behaviors;
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

        $email = $request->post('email');
        $password = $request->post('password');

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
            
            if(!$model->validate() || !$model->save()) {
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

            $mail->Username = 'username@yandex.ru';
            $mail->Password = 'password';

            $mail->setFrom('username@yandex.ru');
            $mail->addAddress($email);
            $mail->Subject = 'Подтверждение аккаунта';
            $mail->Body = 'Для подтверждения перейдите <a href="' . $_SERVER['HTTP_HOST'] . "/user/verify/?token=" . $signup_token . '">по ссылке</a>';

            $mail->isHTML(true);

            return [
        	"mailSend" => $mail->send()
    	    ];
        } else {
            return [
        	"error" => "User exsist."
    	    ];
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

        $verification_code = $request->post('token');

        $user = User::find(['signup_token' => $verification_code])->one();

        if (!is_null($user)) {
            $user->verified = 1;

            if($user->update()) {
                return [
            	    "result" => true
                ];
            }
        } else {
            return ["result" => false];
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
        if($request->post('email')) {
            $email = $request->post('email');
            $password = $request->post('password');

            // Checking the presence of a user in the database
            $user = User::findOne(['email' => $email]);
            
            if(!is_null($user)) {
                // Password verification
                if (password_verify($password, $user->password)) {
            	    if ($user->verified == 1) {
            		$user->access_token = uniqid();
			
			if ($user->update()) {
            		    return [
            			"access_token" => $user->access_token
            		    ];
            		} else {
            		    return [
            			"error" => "Cann't generate new access token"
            		    ];
            		}
            	    } else {
            		return ["result" => "Подтвердите ваш аккаунт, перейдя по ссылке на почте"];
            	    }
                } else {
                    return ["result" => false];
                }
            } else {
                return ["result"  => false];
            }
        } else {
            return ["result" => false];
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
        $loginUrl = $helper->getLoginUrl(Yii::$app->params['facebook_client_uri'], $permissions);
    
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
                        return ["result" => false];
                    }
                } else {
                return uniqid();
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
    public function actionLoginGoogle()
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
                    return ["result" => false];
                }
            } else {
                return uniqid();
            }
        } else {
            return $auth_url;
        }
    }

    /**
     * Update user info function
     * 
     * @param $code - authorization code returned by Google
     * 
     * @return string|bool
     */
    public function actionUpdate()
    {
    
	$arr = [
	    'headers' => Yii::$app->request->getHeaders(),
	    'user' => Yii::$app->user->identity->id
	];
	
	return $arr;
    
        $request = Yii::$app->request;

        // Check authorized
        if (!Yii::$app->user->isGuest) {
            $user = User::find(Yii::$app->user->identity->id);

            if (!is_null($request->post("gender"))) {
                $user->gender = $request->post("gender");
            }

            if (!is_null($request->post("phone"))) {
                $user->phone = $request->post("phone");
            }

            if (!is_null($request->post("email"))) {
                $user->email = $request->post("email");
            }

            if (!is_null($request->post("age"))) {
                $user->age = $request->post("age");
            }

            if ($user->update()) {
                return [
            	    "return" => true
            	];
            } else {
                return $user->error;
            }
        } else {
            throw new \yii\web\UnauthorizedHttpException();
        }
    }
}