<?php 

class UserSignupCest
{
    // Тесты
    public function userSignupViaApi(\ApiTester $I)
    {
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/user/signup',[
            'email' => 'test@gmail.com',
            'password' => 'testpass',
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"result":"ok"}');
    }

    // Проверка на запрет GET запроса к действию Signup.
    public function userSignupIsGetBlocked(\ApiTester $I)
    {
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendGET('/user/signup',[
            'status' => 'pending',
        ]);
        $I->seeResponseCodeIs(404); // 200
        $I->seeResponseIsJson();
    }

    // Проверка на запрет DELETE запроса к действию Signup
    public function userSignupIsDeleteBlocked(\ApiTester $I)
    {
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendDELETE('/user/signup',[
            'email' => '123@gmail.com',
        ]);
        $I->seeResponseCodeIs(404); // 200
        $I->seeResponseIsJson();
    }

    // Проверка на запрет PUT запроса к действию Signup
    public function userSignupIsPutBlocked(\ApiTester $I)
    {
        $I->amHttpAuthenticated('service_user', 'test1234');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPUT('/user/signup',[
            'email' => '123@gmail.com',
        ]);
        $I->seeResponseCodeIs(404); // 200
        $I->seeResponseIsJson();
    }
    // public function _before(ApiTester $I)
    // {
    // }

    // // tests
    // public function tryToTest(ApiTester $I)
    // {
    // }
}
