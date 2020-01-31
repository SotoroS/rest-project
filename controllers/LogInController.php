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
}
