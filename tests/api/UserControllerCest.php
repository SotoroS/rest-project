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
        // $I->sendPOST('/user/signup-web',[
        //     'email' => 'test11@gmail.com',
        //     'password' => 'testpass111',
        // ]);
        // $mailer = $I->grabComponent('mailer');
        // echo print_r($mailer,true);
        // // $token = strstr($I->grabLastSentEmail(),'?token=');
        // // $token = strstr($token,'">по ссылке</a>',true);

        // // $I->sendPOST('/user/verify',[
        // //     'token' => $token,
        // // ]);
        // // $I->seeResponseIsJson();
        // // $I->seeResponseContains('{"error":"User exist"}');
    }
    //

    public function getAreasViaApi(\ApiTester $I)
    {
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendGET('/user/get-areas',[
          'token' => 'token'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'data' => 'array',
        ]);
    }

    public function verifyViaApi(\ApiTester $I)
    {
        // ПУСТО
    }

    public function loginViaApi(\ApiTester $I)
    {
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
        //!!!Узнать как работает L O G I N через F A C E B O O K!!!

        // $I->amHttpAuthenticated('service_user', 'test1234');
        // $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        // $I->sendPOST('/user/login-facebook',[
        //
        // ]);
        // $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        // $I->seeResponseIsJson();
        // $I->seeResponseMatchesJsonType([
        //
        // ]);
    }
    //

    public function loginGoogleViaApi(\ApiTester $I)
    {
        //!!!Узнать как работает L O G I N через G O O G L E!!!

        // $I->amHttpAuthenticated('service_user', 'test1234');
        // $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        // $I->sendPOST('/user/login-google',[
        //
        // ]);
        // $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        // $I->seeResponseIsJson();
        // $I->seeResponseMatchesJsonType([
        //  
        // ]);
    }

    //Нужно дописать
    public function updateViaApi(\ApiTester $I)
    {
        // ПУСТО
    }
}
