<?php 

class UserControllerCest
{
    public function signupMobViaApi(\ApiTester $I)
    {
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        //Нужно разобраться как происходит signup-mob
        $I->sendPOST('/user/signup-mob',[
            'account_id' => '1',
            'deviceType' => 'android',
            'fcmToken' => 'token',
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'status' => 'boolean',
            'cities' => 'array',
            'city_areas' => 'array',
            'rent_types' => 'array',
            'property_types' => 'array'
        ]);
    }

    //НЕ РАБОТАЕТ, ОЖИДАЮ ПОКА БУДЕТ ОТПРАВКА ПОЧТЫ ПЕРЕПИСАНА НА YII2MAILER.
    public function signupWebViaApi(\ApiTester $I)
    {
        // $I->amHttpAuthenticated('service_user', 'test1234');
        // $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        //Работает
        // //Получение токена с помощью регулярного выражения из текста письма.
        // $mail = 'ASfasfasfz \\n?token=4573452341fgjs';
        // preg_match('/token=[a-z0-9]{13}/',$mail,$matches);
        // preg_match('/[a-z0-9]{13}/',$matches[0],$match);
        // $token = $match[0];
        //

        // $I->sendGET('/user/verify',[
        //     'token' => $token,
        // ]);
        // //
        // \Codeception\Util\Debug::debug($token);die();
        // $token = strstr($I->grabLastSentEmail(),'?token=');
        // $token = strstr($token,'">по ссылке</a>',true);

        // $I->sendPOST('/user/verify',[
        //     'token' => $token,
        // ]);
        // $I->seeResponseIsJson();
        // $I->seeResponseContains('{"error":"User exist"}');
    }
    //

    public function getAreasViaApi(\ApiTester $I)
    {
        $I->sendGET('/user/get-areas');
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'name' => 'string',
            'id' => 'integer'
        ]);
    }

    public function verifyViaApi(\ApiTester $I)
    {
        // ПУСТО
        // Чтобы покрыть этот Action тестами, нужно получить текст письма, отправляемого пользователю
        // Жду пока перепишут на Yii2\Mailer.
    }

    public function loginViaApi(\ApiTester $I)
    {
        // СНАЧАЛА НУЖНО СОЗДАТЬ ПОЛЬЗОВАТЕЛЯ ЗДЕСЬ
        // И ПОТОМ ИСПОЛЬЗОВАТЬ ЕГО ДАННЫЕ ДЛЯ ВХОДА

        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/user/login',[
            'email' => 'ghettogopnik1703@gmail.com',
            'password' => '45678',
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'access_token' => 'string',
        ]);
    }

    //При входе через соцсети необходимо проверить только то, что возвращется URL при первом запросе и всё. 
    //Необходимо подождать пока исправят пробел в ключе JSON, возвращаемом user/login-facebook
    public function loginFacebookViaApi(\ApiTester $I)
    {
        $I->sendGET('/user/login-facebook');
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'redirect_uri' => 'string:url'
        ]);
    }
    //

    public function loginGoogleViaApi(\ApiTester $I)
    {
        $I->sendGET('/user/login-google');
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'redirect_uri' => 'string:url'
        ]);
    }

    //Нужно дописать
    // public function updateViaApi(\ApiTester $I)
    // {
    //     // СНАЧАЛА НУЖНО СОЗДАТЬ ПОЛЬЗОВАТЕЛЯ ЗДЕСЬ
    //     // И ПОТОМ ИСПОЛЬЗОВАТЬ ЕГО ДАННЫЕ ДЛЯ ВХОДА

    //     $I->amHttpAuthenticated('service_user', 'test1234');
    //     $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
    //     //Вход, получение access_token и авторизация
    //     $I->sendPOST('/user/login',[
    //         'email' => 'nape.maxim@gmail.com',
    //         'password' => '45678',
    //     ]);
    //     $response=$I->grabResponse();
    //     $response=json_decode($response);
    //     $token = $response->access_token;
    //     $I->amBearerAuthenticated($token);
    //     //
    //     $I->sendPOST('/user/login',[
    //         'gender' => 'F',
    //         'phone' => '+79999999999',
    //         'email' => 'nape.maxim@gmail.com',
    //         'age' => '22'
    //     ]);
    //     $I->seeResponseIsJson();
    //     $I->seeResponseMatchesJsonType([
    //         'return' => 'boolean',
    //     ]);
    // }
}
