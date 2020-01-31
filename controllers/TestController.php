<?php


namespace micro\controllers;

use micro\models\User;
use Yii;
use PHPMailer\PHPMailer\PHPMailer;
use yii\web\Controller;
// use yii\swiftmailer\Mailer;



class TestController extends Controller
{
    public function actionSignup()
    {

        $request = Yii::$app->request;

        $email = $request->get('email');
        $password = $request->get('password');
        $signup_token = uniqid();

        $model = new User();

        $model->email = $email;
        $model->password = $password;
        $model->signup_token = $signup_token;
        

        $uniq = uniqid();

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

    public function actionUrl()
    {
        return $_SERVER['HTTP_HOST'];
    }
} 



// ?email=glebikon@yandex.ru&password=1234