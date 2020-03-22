<?php
// Строгая типизация
declare(strict_types=1);

namespace micro\controllers;

use Yii;

use yii\base\Exception;

use yii\rest\Controller;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use micro\models\User;
use micro\models\UserMobile;
use micro\models\UserWeb;

use micro\models\City;
use micro\models\CityArea;
use micro\models\RentType;
use micro\models\PropertyType;

use Facebook;
use Google_Client;
use Google_Service_Oauth2;

/**
 * Class UserController
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
            'only' => ['login', 'signup-web', 'signup-mob', 'get-areas', 'verify', 'update', 'login-facebook', 'login-google'],
            'rules' => [
                [
                    'actions' => ['login', 'signup-web', 'signup-mob', 'get-areas', 'verify', 'login-facebook', 'login-google'],
                    'allow' => true,
                    'roles' => ['?'],
                ],
                [
                    'actions' => ['update', 'get-areas'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'signup-web' => ['post'],
                'signup-mob' => ['post'],
                'get-areas' => ['get'],
                'verify' => ['get'],
                'update' => ['post'],
                'login-facebook' => ['get'],
                'login-google' => ['get'],
                'login' => ['post'],
            ],
        ];

        // Возвращает результаты экшенов в формате JSON  
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        // Включение аутентификации по OAuth 2.0
        $behaviors['authenticator'] = [
            'except' => ['login', 'signup-mob', 'signup-web', 'get-areas', 'verify', 'login-facebook', 'login-google'],
            'class' => HttpBearerAuth::className()
        ];

        return $behaviors;
    }

    /**
     * Signup from mobile phone device
     * 
     * @param int $account_id
     * @param string $deviceType
     * @param string $fcmToken
     * 
     * 
     * @return array
     */
    public function actionSignupMob(): array
    {
        $request = Yii::$app->request;

        $output = [];
        $user = null;

        try {
            $id = $request->post('account_id');

            // Check user exist by id, if user existen't - create new user
            if (is_null($user = UserMobile::findOne($id))) {
                $user = new UserMobile();
            }

            $user->deviceType = $request->post('deviceType');
            $user->fcmToken = $request->post('fcmToken');

            if (!$user->save()) {
                return [
                    'error' => $user->errors
                ];
            }

            $output['status'] = true;
            $output['cities'] = City::find()->asArray()->all();
            $output['city_areas'] = CityArea::find()->asArray()->all();
            $output['rent_types'] = RentType::find()->asArray()->all();
            $output['property_types'] = PropertyType::find()->asArray()->all();

            return $output;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Signup from web
     * 
     * @param string $email - email address
     * @param string $password - password
     * 
     * @return array
     */
    public function actionSignupWeb(): array
    {
        $request = Yii::$app->request;

        $email = $request->post('email');
        $password = $request->post('password');

        try {
            if (empty($email) || empty($password)) {
                throw new Exception("Require fields: email or password is empty");
            }

            // Find user by email
            $user = UserWeb::findOne(['email' => $email]);

            // If not exist user by email - create new user
            if (is_null($user)) {
                $model = new UserWeb();

                $password = password_hash($password, PASSWORD_DEFAULT);
                $signup_token = uniqid();

                $model->email = $email;
                $model->password = $password;
                $model->signup_token = $signup_token;

                if (!$model->validate() || !$model->save()) {
                    throw new Exception('Cann\'t save user model');
                }

                $message = Yii::$app->mailer->compose();

                $message
                    ->setFrom(Yii::$app->params['email'])
                    ->setTo($email)
                    ->setSubject('Подтверждение аккаунта')
                    ->setHtmlBody('Для подтверждения перейдите <a href="' . $_SERVER['HTTP_HOST'] . "/user/verify?token=" . $signup_token . '">по ссылке</a>');

                if ($message->send()) {
                    return [
                        "status" => true
                    ];
                } else {
                    throw new Exception("Cann't send email");
                }
            } else {
                throw new Exception("User exist");
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all areas 
     * 
     * @return array
     */
    public function actionGetAreas(): array
    {
        try {
            $citiesArray = [];

            $cities = City::find()->all();

            if (empty($cities)) {
                throw new Exception("Cities not found.");
            }

            foreach ($cities as $city) {
                $citiesArray[] = [
                    'name' => $city->name,
                    'id' => $city->id,
                ];
            }

            return $citiesArray;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify user function
     * 
     * @param string $token - verify token
     * 
     * @return array
     */
    public function actionVerify(): array
    {
        $request = Yii::$app->request;

        $verification_code = $request->get('token');

        try {
            if (is_null($verification_code)) {
                throw new Exception('Request token not found');
            }

            $user = User::find()->where(['signup_token' => $verification_code])->one();

            if (!is_null($user)) {
                $user->verified = 1;

                if ($user->update()) {
                    return [
                        "result" => true,
                    ];
                } else {
                    throw new Exception("Cann't update user model");
                }
            } else {
                throw new Exception("User by signup_token not found");
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
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

        $email = $request->post('email');
        $password = $request->post('password');

        try {
            if (empty($email) || empty($password)) {
                throw new Exception("Required field: Email or Password is empty");
            }

            $user = User::findOne(['email' => $email]);

            if (is_null($user)) {
                throw new Exception("User not found by email");
            }
        
            if (!password_verify($password, $user->password)) {
                throw new Exception("Wrong Password");
            }

            if ($user->verified == 1) {
                $user->access_token = uniqid();

                if ($user->update()) {
                    return [
                        "access_token" => $user->access_token
                    ];
                } else {
                    throw new Exception("Cann't generate new access token");
                }
            } else {
                throw new Exception("Confirm your account by clicking on the link in the mail");
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
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
    public function actionLoginFacebook(): array
    {
        $fb = new Facebook\Facebook([
            'app_id' => Yii::$app->params['facebook_client_id'],
            'app_secret' => Yii::$app->params['facebook_client_secret'],
            'default_graph_version' => 'v3.2',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        // Create the url
        $permissions = ['email'];
        $loginUrl = $helper->getLoginUrl(Yii::$app->params['facebook_client_uri'], $permissions);

        // Getting the authorization  code
        $code = Yii::$app->request->get('code');

        if (!is_null($code)) {
            // Try-catch error check  
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
                if (is_null($user)) {
                    $model = new User();

                    $model->email = $email;
                    $model->verified = 1;
                    $model->access_token = uniqid();

                    if ($model->save()) {
                        return [
                            "access_token" => $value
                        ];
                    } else {
                        throw new Exception('Cann\'t save user model');
                    }
                } else {
                    $user->access_token = uniqid();
                    if ($user->update()) {
                        return [
                            "access_token" => $user->access_token
                        ];
                    } else {
                        throw new Exception('Cann\'t update user model');
                    }
                }
            } catch (Exception $e) {
                Yii::error($e->getMessage(), __METHOD__);

                return [
                    'error' => $e->getMessage()
                ];
            }
        }

        return ["redirect_uri" => $loginUrl];
    }

    /**
     * Login function
     * 
     * @param $code - authorization code returned by Google
     * 
     * @return string|bool
     */
    public function actionLoginGoogle(): array
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

        if (isset($code)) {
            // Try-catch error check 
            try {
                // Getting the token
                $token = $g_client->fetchAccessTokenWithAuthCode($code);
                $g_client->setAccessToken($token);

                // Getting user information
                $oauth2 = new Google_Service_Oauth2($g_client);

                $userInfo = $oauth2->userinfo->get();
                $email = $userInfo->email;

                $user = User::findOne(['email' => $email]);

                // Check user with such email in database
                if (is_null($user)) {
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
                        throw new Exception('Cann\'t save user model');
                    }
                } else {
                    $user->access_token = uniqid();
                    
                    if ($user->update()) {
                        return [
                            "access_token" => $user->access_token
                        ];
                    } else {
                        throw new Exception('Cann\'t update user model');
                    }
                }
            } catch (Exception $e) {
                Yii::error($e->getMessage(), __METHOD__);

                return [
                    'error' => $e->getMessage()
                ];
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
    public function actionUpdate(): array
    {
        $request = Yii::$app->request;
        
        $user = User::findOne(Yii::$app->user->identity->id);

        try {
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
                    "result" => true
                ];
            } else if (
                is_null($request->post("age")) 
                && is_null($request->post("email")) 
                && is_null($request->post("phone")) 
                && is_null($request->post("gender"))
            ) {
                throw new Exception('Nothing to change');
            } else {
                return [
                    "error" => $user->errors
                ];
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return [
                'error' => $e->getMessage()
            ];
        }
    }
}
