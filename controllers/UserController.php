<?php


namespace micro\controllers;

use micro\models\User;
use Yii;
use PHPMailer\PHPMailer\PHPMailer;
use yii\web\Controller;



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

    public function actionFacebook()
    {
        define ('ID', '559755891418423');
        define ('SEKRET', 'f5a86f378bca716435d1db271695dedd');
        define ('URL', '');


    }

    public function actionGoogle()
    {
        //require ("vendor/autoload.php");
        //Step 1: Enter you google account credentials
        
        $g_client = new Google_Client();
        
        $g_client->setClientId("156874812665-unh00vf96tmf4msn0j43fhie0b69k6ke.apps.googleusercontent.com");
        $g_client->setClientSecret("0qepssGons1TcyctkXfW-IPO");
        $g_client->setRedirectUri("http://rest.fokin-team.ru/login/google");
        $g_client->setScopes("email");
        
        //Step 2 : Create the url
        $auth_url = $g_client->createAuthUrl();
        echo "<a href='$auth_url'>Login Through Google </a>";
        
        //Step 3 : Get the authorization  code
        $code = isset($_GET['code']) ? $_GET['code'] : NULL;
        
        //Step 4: Get access token
        if(isset($code)) {
        
            try {
        
                $token = $g_client->fetchAccessTokenWithAuthCode($code);
                $g_client->setAccessToken($token);
        
            }catch (Exception $e){
                echo $e->getMessage();
            }
        
        
        
        
            try {
                $pay_load = $g_client->verifyIdToken();
        
        
            }catch (Exception $e) {
                echo $e->getMessage();
            }
        
        } else{
            $pay_load = null;
        }
        
        if(isset($pay_load)){
        }
        return $pay_load['email'];
    }
}