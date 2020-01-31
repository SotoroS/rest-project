<?php

namespace micro\controllers;

use yii\web\Controller;

class LoginController extends Controller
{

    public function actionHelloWorld()
    {        if (прошёл проверку){
            return $this->render('index');
        }
        else
            return 'error';
    }
}
