<?php

namespace micro\controllers;


use micro\models\User;
use Yii;
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
    public function actionSignup()
    {

        $request = Yii::$app->request;

        $email = $request->get('email');
        $password = $request->get('password');
        if(!$password)
        {
            exit("error password");
        }
        $password = password_hash($password, PASSWORD_DEFAULT);
        $signup_token = uniqid();

        // поиск эл.почты в базе данных
        $user = User::findOne(['email' => $email]);

        // если такой почты не существует, то регистрируем пользователя
        if(!$user)
        {
            $model = new User();

            $model->email = $email;
            $model->password = $password;
            $model->signup_token = $signup_token;
            
            if(($model->validate()) && ($model->save()))
            {
                echo "Вы успешно зарегистрировались!";
            }
            else{
                $errors = $model->errors;
                return $errors;
            }

            // Отправка сообщения со ссылкой на почту пользователя
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
            $mail->Body = 'Для подтверждения перейдите по ссылке: '. $_SERVER['HTTP_HOST'] . "/verification_code/?token=" . $signup_token;

            $mail->isHTML(true);

            $mail->send();
        }
        else 
        {
            return"пользователь с такой почтой уже существует";
        }

    }

     public function actionVerify()
     {
        $request = Yii::$app->request;

        $verification_code = $request->get('verification_code');

        $user = User::findOne(['signup_token' => $verification_code]);

        if($user)
        {
            $user->verified = 1;
            if($user->save())
            {
                return "Ваш аккаунт подтвержден!";
            }
        }
        else
        {
            return "Код подтверждения не верен.";
        }
    }

    public function actionLogin()
    {        
        $request = Yii::$app->request;
        if($request->get('email'))
        {
            $email = $request->get('email');
            $password = $request->get('password');

            $user = User::findOne(['email' => $email]);

            if($user)
            {
                if (password_verify($password, $user->password))
                {
                    return 'chotko, ti avtorizovan';
                }
                else
                {
                    return 'invalid password'.$password.' '.$user->password;
                }
            }
            else
            {
                return 'not exist user with this email';
            }
        }
        else
            return 'ну введи запрос, чё ты как этот';
    }

    public function actionLoginFacebook()
    {
        $ID = 559755891418423;
        $SEKRET = "f5a86f378bca716435d1db271695dedd";
        $URL = 'https://rest.fokin-team.ru/user/login-facebook';

        $fb = new Facebook\Facebook([
        'app_id' => $ID, // Replace {app-id} with your app id
        'app_secret' => $SEKRET,
        'default_graph_version' => 'v3.2',
        ]);
        
        $helper = $fb->getRedirectLoginHelper();
        
        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl('https://rest.fokin-team.ru/user/login-facebook', $permissions);
        
	if (isset($_GET['code']))
	{
	    $params = array(
		'client_id' => $ID,
		'redirect_uri' => 'https://rest.fokin-team.ru/user/login-facebook',
		'client_secret' => $SEKRET,
		'code' => $_GET['code']
		);
	    $url = 'https://graph.facebook.com/v5.0/oauth/access_token';
	}        

	//$token = file_get_contents('https://graph.facebook.com/v5.0/oauth/access_token?client_id=559755891418423&redirect_uri=rest.fokin-team.ru&client_secret=f5a86f378bca716435d1db271695dedd&code='.$_GET['code']);
	//var_dump($URL);
        $tokenInfo = null;
        parse_str(file_get_contents($url . '?' . http_build_query($params)), $tokenInfo);
        
        var_dump($tokenInfo);
    }
    public function actionCallBackFacebook()
    {
        return;
    }

    public function actionGoogle()
    {
        //Enter you google account credentials
        $g_client = new Google_Client();
        $g_client->setClientId("156874812665-unh00vf96tmf4msn0j43fhie0b69k6ke.apps.googleusercontent.com");
        $g_client->setClientSecret("0qepssGons1TcyctkXfW-IPO");
        $g_client->setRedirectUri("https://rest.fokin-team.ru/user/google");
        $g_client->setScopes("email");
        
        //Create the url
        $auth_url = $g_client->createAuthUrl();
        
        //Get the authorization  code
        $code = isset($_GET['code']) ? $_GET['code'] : NULL;
        
        //Get access token
        if(isset($code)) 
        {
            $token = $g_client->fetchAccessTokenWithAuthCode($code);
            $g_client->setAccessToken($token);
            // Get user information
            $oauth2 = new Google_Service_Oauth2($g_client);
            $userInfo = $oauth2->userinfo->get();
            $email = $userInfo->email;
            $user = User::findOne(['email' => $email]);
            // Check user with such email in database
            if(!$user)
            {
                $model = new User();
                $model->email = $email;
                $model->signup_token = uniqid();
                $model->verified = 1;
                $model->access_token = $token['access_token'];
                $model->save();
                echo 'Вы успешно зарегистрировались!';
            }        
            else
            {
                echo 'пользователь с такой почтой уже существует';
            }
        } 
        else
        {
            return $auth_url;
        }
    }
}