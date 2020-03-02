<?php
//Строгая типизация
declare(strict_types=1);

namespace micro\controllers;

use Yii;
use \Datetime;

use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;

use micro\models\User;
use micro\models\CityArea;
use micro\models\RentType;
use micro\models\PropertyType;
use PHPMailer\PHPMailer\PHPMailer;
use micro\models\Address;

use Facebook;
use Google_Client;
use Google_Service_Oauth2;
use micro\models\City;

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
            'only' => ['login', 'signup', 'get-areas', 'verify', 'update', 'login-facebook', 'login-google', 'get-time'],
            'rules' => [
                [
                    'actions' => ['login', 'signup', 'get-areas', 'verify', 'login-facebook', 'login-google', 'get-time'],
                    'allow' => true,
                    'roles' => ['?'],
                ],
                [
                    'actions' => ['update', 'get-areas', 'get-time'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
            
            // Возвращает результаты экшенов в формате JSON  
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; 
            
        $behaviors['authenticator'] = [
            'except' => ['login', 'signup', 'get-areas', 'verify', 'login-facebook', 'login-google', 'get-time'],
            'class' => HttpBearerAuth::className()
        ];

        return $behaviors;
    }

    public function actionGetTime(): string
    {
        $dateTime = new DateTime(null, new \DateTimeZone("Europe/Kiev"));
        return $dateTime->format('Y-m-d H:i:s');
    }
    
    /**
     * Signup function
     * 
     * 
     * @param string $email - email address
     * @param string $password - password
     * 
     * @return string|bool
     */
    public function actionSignup(): array
    {
        $request = Yii::$app->request;

        $output = [];
        try {
            $account_id = $request->post('account_id');

            // looking for a user by id, if not - create a new one
            $user = User::findOne($account_id) ?: new User();

            $user->deviceType = $request->post('deviceType');
            $user->fcmToken = $request->post('fcmToken');

            //$user->email = $request->post('email');
            //$user->password = password_hash($request->post('password'), PASSWORD_DEFAULT);

            $user->save();

            // fill the array
            $output['status'] = true;
            $output['cities'] = City::find()->asArray()->all();
            $output['city_areas'] = CityArea::find()->asArray()->all();
            $output['rent_types'] = RentType::find()->asArray()->all();
            $output['property_types'] = PropertyType::find()->asArray()->all();

        } catch (\Throwable $e) {

            $output['status'] = false;
            $output['error'] = $e->getMessage();

            Yii::$app->response->statusCode = 500;
            return $output;
        }

        Yii::$app->response->statusCode = 200;
        return $output;
        



        // $email = $request->post('email');
        // $password = $request->post('password');

        // if (is_null($email) || is_null($password)) {
        //     return [
        //         'error' => 'Fields are not filled'
        //     ];
        // }

        // $password = password_hash($password, PASSWORD_DEFAULT);
        // $signup_token = uniqid();

        // // Find user by email
        // $user = User::findOne(['email' => $email]);

        // // Registration user if not exist user
        // if(!$user) {
        //     $model = new User();

        //     $model->email = $email;
        //     $model->password = $password;
        //     $model->signup_token = $signup_token;
            
        //     if(!$model->validate() || !$model->save()) {
        //         return [
        //         "errors" => $model->errors
        //         ];
        //     }

        //     // Send email message for verify
        //     $mail = new PHPMailer;

        //     $mail->CharSet = "UTF-8";

        //     $mail->isSMTP();
        //     $mail->Host = 'smtp.yandex.ru';
        //     $mail->Port = 465;
        //     $mail->SMTPAuth = true;
        //     $mail->SMTPSecure = 'ssl';

        //     $mail->Username = 'arman.shukanov@fokin-team.ru';
        //     $mail->Password = 'arman_shukanov';

        //     $mail->setFrom('arman.shukanov@fokin-team.ru');
        //     $mail->addAddress($email);
        //     $mail->Subject = 'Подтверждение аккаунта';
        //     $mail->Body = 'Для подтверждения перейдите <a href="' . $_SERVER['HTTP_HOST'] . "/user/verify?token=" . $signup_token . '">по ссылке</a>';

        //     $mail->isHTML(true);

        //     return [
        // 	"mailSend" => $mail->send()
    	//     ];
        // } else {
        //     return [
        // 	"error" => "User exsist."
    	//     ];
        // }
    }

    public function actionGetAreas(): array
    {
        $output = [];
        try {
            $citiesArray = [];
            // print all cities and add them to the array
            $cities = City::find()->all();
            foreach ($cities as $city) {
                $citiesArray[] = [
                    'name' => $city->name,
                    'id' => $city->id,
                ];
            }
            $output['data'] = $citiesArray;
        } catch (\Exception $e) {
            $output['error'] = $e->getMessage();

        } finally {
            return $output;
        }
    }

    /**
     * Verify user function
     * 
     * @param string $token - verify token
     * 
     * @return string|bool
     */
    public function actionVerify(): string
    {
        $request = Yii::$app->request;

        $verification_code = $request->get('token');
        $user = User::find()->where(['signup_token' => $verification_code])->one();
        
        if (!is_null($user)) {
            $user->verified = 1;

            if($user->update()) {
                return [
            	    "result" => true,
                ];
            } else {
                return [
                    "errors" => $user->errors
                ];
            }
        } else {
            return [
                "result" => false
            ];
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
    public function actionLogin(): array
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
                        return [
                            "result" => "Confirm your account by clicking on the link in the mail"
                        ];
                    }
                } else {
                    return [
                        "result" => false
                    ];
                }
            } else {
                return [
                    "result"  => false
                ];
            }
        } else {
            return [
                "result" => false
            ];
        }
    }

    /**
     * Facebook authorization
     * 
     * @param $code - code user
     * 
     * @return string|bool
     */
    public function actionLoginFacebook(): string
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
                    $model->verified = 1;
                    $model->access_token = $value;
                    
                    if ($model->save()) {
                        return [
                	        "access_token" => $value
                        ];
                    } else {
                        return [
                            "result" => false
                        ];
                    }
                } else {
            	    $user->access_token = uniqid();
            	    if ($user->update()) {
            		    return [
            		        "access_token" => $user->access_token
            		    ];
            	    } else {
                        return [
                            "errors" => $user->errors
                        ];
            	    }
                }
            } catch(Facebook\Exceptions\FacebookResponseException $e){
                echo 'Graph returned an error: ' . $e->getMessage();
            } catch(Facebook\Exceptions\FacebookSDKException $e){
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
            }    
        }
        return ["redirect_uri " => $loginUrl];
    }
    
    /**
     * Login function
     * 
     * @param $code - authorization code returned by Google
     * 
     * @return string|bool
     */
    public function actionLoginGoogle(): string
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
                    return [
                        "access_token" => $token['access_token']
                    ];
                } else {
                    return [
                        "result" => false
                    ];
                }
            } else {
                $user->access_token = uniqid();
                if ($user->update()) {
                    return [
                        "access_token" => $user->access_token
                    ];
                } else {
                    return [
                        "errors" => $user->errors
                    ];
                }
            }
        } else {
            return [
                "redirect_uri" => $auth_url
            ];
        }
    }

    /**
     * Update user info function
     * 
     * @param $code - authorization code returned by Google
     * 
     * @return string|bool
     */
    public function actionUpdate(): string
    {
        $request = Yii::$app->request;

        // Check authorized
        if (!Yii::$app->user->isGuest) {
            $user = User::find(Yii::$app->user->identity->id)->one();
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
                return [
                    "error" => 'Nothing to change'
                ];
            }
        } else {
            throw new \yii\web\UnauthorizedHttpException();
        }
    }
}