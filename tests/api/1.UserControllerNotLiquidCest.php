<?php 

use micro\models\User;
use micro\models\City;

class UserControllerNotLiquidCest
{
    /**
     * Alternative e-mail for change info about user
     * 
     * @var string
     */
    public $alternativeEmail = 'ghettogopnik1703@gmail.com';
    
    /**
     * @var string
     */
    public $email = 'max.napedenin@gmail.com';
    
    /**
     * @var string
     */
    public $password = '1234';

    /**
     * Model of test user
     * 
     * @var User
     */
    public $testUser;

    /**
     * Signup test user from web
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function signupWebViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $I->sendPOST('/user/signup-web', [
            'email' => $this->email,
            'password' => ''
        ]);

        $I->seeResponseMatchesJsonType([
            'error' => 'string|array',
        ]);

        $I->sendPOST('/user/signup-web', [
            'email' => 'neEmail',
            'password' => 'nePassword'
        ]);

        $I->seeResponseMatchesJsonType([
            'error' => 'string|array',
        ]);

        $I->sendPOST('/user/signup-web', [
            'email' => '',
            'password' => 'nePassword'
        ]);

        $I->seeResponseMatchesJsonType([
            'error' => 'string|array',
        ]);
    }

    /**
     * Signup test user from mob app
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function signupMobViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $I->sendPOST('/user/signup-mob', [
            'account_id' => -1,
            'deviceType' => '',
            'fcmToken' => '',
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'error' => 'string|array',
        ]);
    }

    
    /**
     * Login 
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function loginViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        $I->sendPOST('/user/login', [
            'email' => 'neEmail',
            'password' => 'nePassword',
        ]);

        $I->seeResponseIsJson();
        
        $I->seeResponseMatchesJsonType([
            'error' => 'string|array',
        ]);
    }

    /**
     * Get areas
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function getAreasViaApi(\ApiTester $I)
    {
        $I->sendGET('/user/get-areas');
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'error' => 'string|array',
        ]);
    }

    /**
     * Update user info
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function updateViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        //Create new user for testing update
        $this->_signupWebViaApi($I);

        $I->sendPOST('/user/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $response = $I->grabResponse();
        $response = json_decode($response);

        $token = $response->access_token;
        
        $I->amBearerAuthenticated($token);

        $I->sendPOST('/user/update/' . $this->testUser->ID, [
            'gender' => 'T',
            'phone' => '+7999999',
            'email' => 'neEmail@gmail.com',
            'age' => '-52'
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'error' => 'string|array',
        ]);
    }

    /**
     * Verify new test user
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
     * Signup test user from web
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    private function _signupWebViaApi(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $I->sendPOST('/user/signup-web', [
            'email' => $this->email,
            'password' => $this->password
        ]);

        $response = json_decode($I->grabResponse(), true);

        if (array_key_exists("status", $response) && ($response["status"]) == true) {
            $this->_verifyViaApi($I);
        } else {
            $I->seeResponseContainsJson(
                array('error' => 'User exist')
            );
            $testUser = User::find()->where(['email' => $this->email])->one();
            $this->testUser = $testUser;
        }
    }
}
