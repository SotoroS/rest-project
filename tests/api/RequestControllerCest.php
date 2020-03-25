<?php

use micro\models\User;
use micro\models\Filter;

/**
 * Api test for request controller
 * 
 * Class RequestControllerCest
 */
class RequestControllerCest
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
            'num_of_people' => 1,
            'family' => 2,
            'pets' => 3,
            'price_from' => 20000,
            'price_to' => 6000000,
            'description' => 'Description',
            'rent_type' => '[1,2,3]',
            'property_type' => '[2,3]',
            'substring' => 'Substring',
            'addresses' => ['Саратов улица Вишневая 24'],
            'requestName' => 'Проверка'
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'id' => 'integer'
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
            'fcmToken' => 'token',
            'city_area_id' => 1,
            'rent_type' => '[1,3]',
            'push_notification' => 1,
            'price_from' => 40000,
            'price_to' => 500000,
            'substring' => 'substring',
            'requestName' => 'Abcd',
            'push_enabled' => 1
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'cities' => 'array',
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
            'num_of_people' => 1,
            'family' => 2,
            'pets' => 3,
            'square_from' => 200,
            'square_to' => 500,
            'city_id' => 1,
            'price_from' => 20000,
            'price_to' => 5300000,
            'description' => 'Description',
            'city_area_id' => 1,
            'rent_type' => '[1,2,3]', 
            'property_type' => '[1,2]',
            'substring' => 'Substring',
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            array('status' => true)
        );
    }

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

        $I->sendGET('/request/view/' . $this->testFilter->id);

        // Проверка формата данных для даты в формате "гггг-мм-дд чч:мм:cc"
        Codeception\Util\JsonType::addCustomFilter('datetime', function ($value) {
            return preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $value, $matches);
        });

        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'user_id' => 'integer',
            'num_of_people' => 'integer|null',
            'family' => 'integer|null',
            'pets' => 'integer|null',
            'square_from' => 'integer|null',
            'square_to' => 'integer|null',
            'city_id' => 'integer',
            'price_from' => 'integer',
            'price_to' => 'integer',
            'description' => 'string|null',
            'pivot_lt' => 'number|null',
            'pivot_lg' => 'number|null',
            'radius' => 'number|null',
            'city_area_id' => 'integer',
            'rent_type' => 'string|null',
            'property_type' => 'string|null',
            'substring' => 'string',
            'created_at' => 'string:datetime|null',
            'updated_at' => 'string:datetime|null',
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

        // Set OAuth 2.0 token
        $I->amBearerAuthenticated($this->token);

        $this->testFilter = Filter::find()->where(['user_id' => $this->testUser->id])->one();

        // Create filter if need
        if (is_null($this->testFilter) && $needTestFilter) {
            $this->newViaApi($I);
        }
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

        if (array_key_exists("status", $response) && ($response["status"]) == true) {
            $this->_verifyViaApi($I);
        } else {
            $I->seeResponseContainsJson(
                ['error' => 'User exist']
            );
        }
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
            array('status' => true)
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
}
