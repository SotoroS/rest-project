<?php
// Строгая типизация
declare(strict_types=1);

namespace micro\controllers;

use Yii;

use yii\web\Response;
use yii\base\Exception;
use yii\rest\Controller;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;

use micro\models\User;
use micro\models\City;
use micro\models\UserWeb;
use micro\models\CityArea;
use micro\models\RentType;
use micro\models\UserMobile;
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
    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * Child classes may override this method to specify the behaviors they want to behave as.
     *
     * The return value of this method should be an array of behavior objects or configurations
     * indexed by behavior names. A behavior configuration can be either a string specifying
     * the behavior class or an array of the following structure:
     *
     * ```php
     * 'behaviorName' => [
     *     'class' => 'BehaviorClass',
     *     'property1' => 'value1',
     *     'property2' => 'value2',
     * ]
     * ```
     *
     * Note that a behavior class must extend from [[Behavior]]. Behaviors can be attached using a name or anonymously.
     * When a name is used as the array key, using this name, the behavior can later be retrieved using [[getBehavior()]]
     * or be detached using [[detachBehavior()]]. Anonymous behaviors can not be retrieved or detached.
     *
     * Behaviors declared in this method will be attached to the component automatically (on demand).
     *
     * @return array the behavior configurations.
     */
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
     * Signup from mobile phone device (<b>POST</b>)
     * <br> URL: https://rest.fokin-team.ru/user/signup-mob
     * 
     * Example of successfull response:
     * 
     * ```json
     * {
     *  'status' = true,
     *  'cities': [...],
     *  'city_areas': [...],
     *  'rent_types': [...],
     *  'property_types': [...]
     * }
     * ```
     * 
     * Example of error response:
     * 
     * ```json
     * {
     *  'error' = '...'
     * }
     * ```
     * 
     * @param string $signature The hash signature for this request
     * 
     * @param int $account_id Account ID
     * @param string $deviceType Device type
     * @param string $fcmToken fcm token
     * 
     * @throws Exception Exception
     * 
     * @return array Response
     */
    public function actionSignupMob($signature = '', $account_id = 0, $deviceType = '', $fcmToken = ''): array
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
                return ['error' => $user->errors];
            }

            $output['status'] = true;
            $output['cities'] = City::find()->asArray()->all();
            $output['city_areas'] = CityArea::find()->asArray()->all();
            $output['rent_types'] = RentType::find()->asArray()->all();
            $output['property_types'] = PropertyType::find()->asArray()->all();

            return $output;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Signup from web (<b>POST</b>)
     * <br> URL: https://rest.fokin-team.ru/user/signup-web
     * 
     * Example of successfull response:
     * 
     * ```json
     * {
     *  'status' = true,
     * }
     * ```
     * 
     * Example of error response:
     * 
     * ```json
     * {
     *  'error' = '...'
     * }
     * ```
     * 
     * @param string $signature The hash signature for this request
     * 
     * @param string $email E-mail address
     * @param string $password Password
     * 
     * @throws Exception Exception
     * 
     * @return array Response
     */
    public function actionSignupWeb($signature = '', $email = '', $password = ''): array
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
                    return ['error' => $model->errors];
                }

                $message = Yii::$app->mailer->compose();

                $message
                    ->setFrom(Yii::$app->params['email'])
                    ->setTo($email)
                    ->setSubject('Подтверждение аккаунта')
                    ->setHtmlBody('Для подтверждения перейдите <a href="' . $_SERVER['HTTP_HOST'] . "/user/verify?token=" . $signup_token . '">по ссылке</a>');

                if ($message->send()) {
                    return ['status' => true];
                } else {
                    throw new Exception("Cann't send email");
                }
            } else {
                throw new Exception("User exist");
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get all areas (<b>GET</b>)
     * <br> URL: https://rest.fokin-team.ru/user/get-areas
     * 
     * Example of successfull response:
     * 
     * ```json
     * {
     *  [
     *   {
     *    'id': '...',
     *    'name: '...'
     *   },
     *   ...
     *  ]
     * }
     * ```
     * 
     * Example of error response:
     * 
     * ```json
     * {
     *  'error' = '...'
     * }
     * ```
     * 
     * @throws Exception Exception
     * 
     * @return array Response
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
                    'id' => $city->id,
                    'name' => $city->name,
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
     * Verify user (<b>GET</b>)
     * <br> URL: https://rest.fokin-team.ru/user/verify
     * 
     * Example of successfull response:
     * 
     *```json
     * {
     *  'status': true
     * }
     * ```
     * 
     * Example of error response:
     * 
     * ```json
     * {
     *  'error' = '...'
     * }
     * 
     * @param string $signature The hash signature for this request
     * 
     * @param string $token Verify token
     * 
     * @throws Exception Exception
     * 
     * @return array Response
     */
    public function actionVerify($signature = '', $token = ''): array
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
                    return ['status' => true];
                } else {
                    return ['error' => $user->errors];
                }
            } else {
                throw new Exception("User by signup_token not found");
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Login (<b>POST</b>)
     * <br> URL: https://rest.fokin-team.ru/user/login
     * 
     * Example of successfull response:
     * 
     *```json
     * {
     *  'access_token': '...'
     * }
     * ```
     * 
     * Example of error response:
     * 
     * ```json
     * {
     *  'error' = '...'
     * }
     * 
     * @param string $signature The hash signature for this request
     * 
     * @param string $email E-mail user
     * @param string $password Password user 
     * 
     * @throws Exception Exception
     * 
     * @return array Response
     */
    public function actionLogin($signature = '', $email = '', $password = ''): array
    {
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
                    return ['access_token' => $user->access_token];
                } else {
                    return ['error' => $user->errors];
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
     * Login from Facebook (<b>GET</b>)
     * <br> URL: https://rest.fokin-team.ru/user/login-facebook
     * 
     * Example of successfull response:
     * 
     *```json
     * {
     *  'access_token': '...'
     * }
     * ```
     * 
     * Example of error response:
     * 
     * ```json
     * {
     *  'error' = '...'
     * }
     * 
     * @param string $signature The hash signature for this request
     * 
     * @param string $code Code user
     * 
     * @throws Exception Exception
     * 
     * @return array Response
     */
    public function actionLoginFacebook($signature = '', $code = null): array
    {
        session_start(); 
        $fb = new Facebook\Facebook([
            'app_id' => Yii::$app->params['facebook_client_id'],
            'app_secret' => Yii::$app->params['facebook_client_secret'],
            'default_graph_version' => 'v3.2',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email'];
        $loginUrl = $helper->getLoginUrl(Yii::$app->params['facebook_client_uri'], $permissions);

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
                        return ['access_token' => $model->access_token];
                    } else {
                        return ['error' => $model->errors];
                    }
                } else {
                    $user->access_token = uniqid();

                    if ($user->update()) {
                        return ['access_token' => $user->access_token];
                    } else {
                        return ['error' => $user->errors];
                    }
                }
            } catch (Exception $e) {
                Yii::error($e->getMessage(), __METHOD__);

                return ['error' => $e->getMessage()];
            }
        }

        return ['url' => $loginUrl];
    }

    /**
     * Login from Google (<b>GET</b>)
     * <br> URL: https://rest.fokin-team.ru/user/login-google
     * 
     * Example of successfull response:
     * 
     *```json
     * {
     *  'access_token': '...'
     * }
     * ```
     * 
     * Example of error response:
     * 
     * ```json
     * {
     *  'error' = '...'
     * }
     * 
     * @param string $signature The hash signature for this request
     * 
     * @param string $code Authorization code returned by Google
     * 
     * @throws Exception Exception
     * 
     * @return array Response
     */
    public function actionLoginGoogle($signature = '', $code = null): array
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
            try {
                $token = $g_client->fetchAccessTokenWithAuthCode($code);
                $g_client->setAccessToken($token);

                $oauth2 = new Google_Service_Oauth2($g_client);

                $userInfo = $oauth2->userinfo->get();
                $email = $userInfo->email;

                $user = User::findOne(['email' => $email]);

                if (is_null($user)) {
                    $model = new User();

                    $model->email = $email;
                    $model->signup_token = uniqid();
                    $model->verified = 1;
                    $model->access_token = $token['access_token'];

                    if ($model->save()) {
                        return ['access_token' => $token['access_token']];
                    } else {
                        return ['error' => $model->errors];
                    }
                } else {
                    $user->access_token = uniqid();

                    if ($user->update()) {
                        return ['access_token' => $user->access_token];
                    } else {
                        return ['error' => $user->errors];
                    }
                }
            } catch (Exception $e) {
                Yii::error($e->getMessage(), __METHOD__);

                return ['error' => $e->getMessage()];
            }
        } else {
            return ['url' => $auth_url];
        }
    }

    /**
     * Update user info (<b>POST</b>)
     * <br> URL: https://rest.fokin-team.ru/user/update
     * 
     * Example of successfull response:
     * 
     *```json
     * {
     *  'status': true
     * }
     * ```
     * 
     * Example of error response:
     * 
     * ```json
     * {
     *  'error' = '...'
     * }
     * 
     * @param string $signature The hash signature for this request
     * 
     * @param string $gender New value gender of user (Values: M or F)
     * @param string $phone New value phone of user
     * @param string $email New value E-mail of user
     * @param string $age New value age of user
     * 
     * @throws Exception Exception
     * 
     * @return array Response
     */
    public function actionUpdate($signature = '', $gender = '', $phone = '', $email = '', $age = ''): array
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
                return ['status' => true];
            } else {
                return ['error' => $user->errors];
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);

            return ['error' => $e->getMessage()];
        }
    }
}
