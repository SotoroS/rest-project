<?php 
use micro\models\User;

class UserControllerCest
{
    public $alternativeEmail = 'ghettogopnik1703@gmail.com';
    public $email = 'nape.maxim@gmail.com';
    public $password = '1234';
    public $testUser;

    public function verifyViaApi(\ApiTester $I)
    {
        $this->testUser = User::find()->where(['email' => $this->email])->one();
        $I->sendGET('/user/verify',[
            'token' => $this->testUser->signup_token,
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            array('result' => true)
        );
    }

    //НЕ РАБОТАЕТ, ОЖИДАЮ ПОКА БУДЕТ ОТПРАВКА ПОЧТЫ ПЕРЕПИСАНА НА YII2MAILER.
    public function signupWebViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        //Работает
        //Получение токена с помощью регулярного выражения из текста письма.
        $I->sendPOST('/user/signup-web',[
            'email' => $this->email,
            'password' => $this->password
        ]);
        $response = json_decode($I->grabResponse(), true);
        //\Codeception\Util\Debug::debug(is_array($response));die();
        if (array_key_exists("mailSend",$response) && ($response["mailSend"]) == true)
        {
            $this->verifyViaApi($I);
        }
        else
        {
            $I->seeResponseContainsJson(
                array('error' => 'User exist.')
            );
        }
    }
    //

    public function signupMobViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        $I->sendPOST('/user/signup-mob',[
            'account_id' => $this->testUser->ID,
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

    public function getAreasViaApi(\ApiTester $I)
    {
        $I->sendGET('/user/get-areas');
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'name' => 'string',
            'id' => 'integer'
        ]);
    }

    public function loginViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/user/login',[
            'email' => $this->email,
            'password' => $this->password,
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
    public function updateViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        //Вход, получение access_token и авторизация
        $I->sendPOST('/user/login',[
            'email' => $this->email,
            'password' => $this->password,
        ]);
        $response=$I->grabResponse();
        $response=json_decode($response);
        $token = $response->access_token;
        $I->amBearerAuthenticated($token);
        //

        //Установка нового E-Mail
        $I->sendPOST('/user/login',[
            'gender' => 'F',
            'phone' => '+79999999999',
            'email' => $this->alternativeEmail,
            'age' => '22'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            array('return' => true)
        );

        //Возвращение старого E-Mail
        $I->sendPOST('/user/login',[
            'gender' => 'F',
            'phone' => '+79999999999',
            'email' => $this->email,
            'age' => '22'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            array('return' => true)
        );
    }
}
