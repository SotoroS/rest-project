<?php

namespace micro\controllers;

use yii\web\Controller;
use micro\models\User;
use Yii;

class LoginController extends Controller
{
    public function actionCheckUser()
    {        
        $request = Yii::$app->request;

        $email = $request->get('email');
        $password = $request->get('password');

        $user = User::findOne(['email' => $email]);

        if($user)
        {
            if ($password == $user->password)
            {
                //логиним юзера
                return 'chotko';
            }
            else
            {
                return 'invalid password';
            }
        }
        else
        {
            return 'not exist user with this email';
        }
    }
}
