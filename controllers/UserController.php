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

        $request = Yii::$app->request;

        $code = $request->get('code');
        if(!$code)
        {
            return "error code";
        }

        $token = json_decode(file_get_contents("https://graph.facebook.com/v5.0/oauth/access_token?client_id='.ID.'&redirect_uri='.URL.'&client_secret='.SEKRET.'&code=$code"), true);

        if(!$token)
        {
            return "error token";
        }

        $data = json_decode(file_get_contents("https://graph.facebook.com/v5.0/me?client_id='.ID.'&redirect_uri='.URL.'&client_secret='.SEKRET.'&code=$code'&access_token='.$token['access_token'].'&fields=name,email"), true);

        if(!$data)
        {
            return "error data";
        }

        // https://www.facebook.com/v5.0/dialog/oauth?client_id={'ID'}&redirect_uri={'URL'}&response_type=code&scope=public_profile,email,
    }

    public function actionGoogle()
    {
        
    }
}