<?php

use micro\models\User;
use micro\models\EstateObject;

/**
 * Test for ObjectController with not liquid data
 * 
 * Class ObjectControllerNotLiquidCest
 */
class ObjectControllerNotLiquidCest
{
    /**
     * @var string
     */
    public $alternativeEmail = 'ghettogopnik1703@gmail.com';

    /**
     * @var string
     */
    public $email = 'nape.maxim@gmail.com';

    /**
     * @var string
     */
    public $password = '1234';

    /**
     * Model of test user
     * 
     * @var micro\models\User
     */
    public $testUser;

    /**
     * Model of test object
     * 
     * @var micro\models\EstateObject
     */
    public $testObject;

    /**
     * View object
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function viewObjectViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $this->_init($I);

        $I->sendGET('/object/view/-1');

        // TODO: check status code

        $I->seeResponseMatchesJsonType([
            'message' => 'string',
        ]);
    }

    /**
     * Get objects 
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function getObjectsViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $this->_init($I);

        $I->sendGET('/object/get-objects');

        $I->seeResponseMatchesJsonType([
            'error' => 'string',
        ]);
    }

    /**
     * Create new estate object
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function newObjectViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $this->_init($I);

        $I->sendPOST('/object/new', [
            'address' => '-1',
            'name' => '',
            'description' => '',
            'price' => '-5000000'
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'error' => 'string',
        ]);
    }

    /**
     * Update object
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function updateObjectViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $this->_init($I, true);

        $I->sendPOST('/object/update/' . $this->testObject->id, [
            'name' => '',
            'phone' => '+79999999',
            'images' => [
                codecept_data_dir('1'),
                codecept_data_dir(''),
                codecept_data_dir('3.php'),
            ]
        ]);

        $I->seeResponseIsJson();

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
    private function _init(\ApiTester $I, bool $needTestObject = false)
    {
        $this->_signupViaApi($I);
        $this->_loginViaApi($I);

        if (is_null($this->testObject) && $needTestObject) {
            $this->testObject = EstateObject::find()
                ->where(['user_id' => $this->testUser->id])
                ->orderBy('id DESC')
                ->one();
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
}
