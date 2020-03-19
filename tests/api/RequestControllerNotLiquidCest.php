<?php 

use micro\models\User;
use micro\models\Filter;

class RequestControllerNotLiquidCest
{
    /**
     * Email address test user
     * 
     * @var string
     */
    private $email = 'nape.maxim@gmail.com';

    /**
     * Password test user
     * 
     * @var string
     */
    private $password = '1234';
    
    /**
     * @var User
     */
    private $testUser;

    /**
     * @var Filter
     */
    private $testFilter;
    
    /**
     * Access token test user
     * 
     * @var string
     */
    private $token;

    /**
     * View filter
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function viewViaApi(\ApiTester $I)
    {
        $this->_init($I, true);

        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $I->sendGET('/request/view/-1');

        $I->seeResponseMatchesJsonType([
            'message' => 'string',
        ]);
    }

    /**
     * Create new request object
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function newViaApi(\ApiTester $I)
    {
        $this->_init($I);

        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $I->sendPOST('/request/new-filter', [
            'num_of_people' => -1,
            'family' => -2,
            'pets' => -3,
            'price_from' => -2000000,
            'price_to' => -6000000,
            'description' => '',
            'rent_type' => '',
            'property_type' => '',
            'substring' => '',
            'addresses' => [-1],
            'requestName' => ''
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'error' => 'string',
        ]);
    }

    /**
     * Set filter
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function setViaApi(\ApiTester $I)
    {
        $this->_init($I);

        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $I->sendPOST('/request/set-filter', [
            'fcmToken' => '',
            'city_area_id' => -1,
            'request_type_id' => -1,
            'push_notification' => -1,
            'price_from' => -40000,
            'price_to' => -500000,
            'substring' => '',
            'requestName' => '',
            'push_enabled' => -1
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'error' => 'string',
        ]);
    }

    /**
     * Update filter
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function updateViaApi(\ApiTester $I)
    {
        $this->_init($I, true);

        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $I->sendPOST('/request/update/' . $this->testFilter->id, [
            'num_of_people' => -1,
            'family' => -2,
            'pets' => -3,
            'request_type_id' => 1,
            'square_from' => -200,
            'square_to' => -500,
            'city_id' => 10,
            'price_from' => -20000,
            'price_to' => -5300000,
            'description' => '',
            'city_area_id' => -15,
            'rent_type' => '',
            'property_type' => '',
            'substring' => '',
        ]);

        $I->seeResponseIsJson();

        // TODO: Check rule on update request 

        $I->seeResponseMatchesJsonType([
            'error' => 'string',
        ]);
    }

    /**
     * Init workspace for test
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    private function _init(\ApiTester $I, bool $needTestFilter = false) {
        $this->_signupViaApi($I);
        $this->_loginViaApi($I);

        $this->testFilter = Filter::find()->where(['user_id' => $this->testUser->id])->one();

        // Create filter if need
        if (is_null($this->testFilter) && $needTestFilter) {
            $this->_newValidViaApi($I);
        }

        // Set OAuth 2.0 token
        $I->amBearerAuthenticated($this->token);
    }

    /**
     * Signup test user
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    private function _signupViaApi(\ApiTester $I)
    {
        $I->sendPOST('/user/signup-web', [
            'email' => $this->email,
            'password' => $this->password
        ]);

        $response = json_decode($I->grabResponse(), true);

        if (!array_key_exists("status", $response)) {
            $I->seeResponseContainsJson(
                ['error' => 'User exist']
            );
        }

        $this->_verifyViaApi($I);
    }

    /**
     * Verify test user
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    private function _verifyViaApi(\ApiTester $I)
    {
        $this->testUser = User::find()->where(['email' => $this->email])->one();

        $I->sendGET('/user/verify', [
            'token' => $this->testUser->signup_token,
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            array('result' => true)
        );
    }

    /**
     * Login test user and get access token
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    private function _loginViaApi(\ApiTester $I)
    {
        $I->sendPOST('/user/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $response = $I->grabResponse();
        $response = json_decode($response);

        $testUser = User::find()->where(['email' => $this->email])->one();

        $this->testUser = $testUser;
        $this->token = $response->access_token;
    }

        /**
     * Create new request object
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function _newValidViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $I->sendPOST('/request/new-filter', [
            'num_of_people' => 1,
            'family' => 2,
            'pets' => 3,
            'price_from' => 20000,
            'price_to' => 6000000,
            'description' => 'Description',
            'rent_type' => 'Rent Type',
            'property_type' => 'Property Type',
            'substring' => 'Substring',
            'addresses' => ['Саратов улица Вишневая 24'],
            'requestName' => 'Проверка'
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            array('result' => true)
        );
    }
}
