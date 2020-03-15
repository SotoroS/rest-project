<?php

use micro\models\User;

class UserControllerCest
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
    public $email = 'nape.maxim@gmail.com';
    
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
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $I->seeResponseIsJson();
        
        $I->seeResponseMatchesJsonType([
            'access_token' => 'string',
        ]);
    }

    /**
     * Login from facebook (check only return auth url)
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function loginFacebookViaApi(\ApiTester $I)
    {
        $I->sendGET('/user/login-facebook');

        $I->seeResponseIsJson();
        
        $I->seeResponseMatchesJsonType([
            'redirect_uri' => 'string:url'
        ]);
    }

    /**
     * Login from google (check only return auth url)
     * 
     * @param \ApiTester $I
     * 
     * @return void
     */
    public function loginGoogleViaApi(\ApiTester $I)
    {
        $I->sendGET('/user/login-google');

        $I->seeResponseIsJson();
        
        $I->seeResponseMatchesJsonType([
            'redirect_uri' => 'string:url'
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

        $response = json_decode($I->grabResponse(), true);
        
        if (empty($response)) {
            $I->seeResponseIsValidOnJsonSchemaString(json_encode([]));
        } else {
            $I->seeResponseMatchesJsonType([
                'name' => 'string',
                'id' => 'integer'
            ]);
        }
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

        $I->sendPOST('/user/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $response = $I->grabResponse();
        $response = json_decode($response);

        $token = $response->access_token;
        
        $I->amBearerAuthenticated($token);

        $I->sendPOST('/user/update/' . $this->testUser->ID, [
            'gender' => 'F',
            'phone' => '+79999999999',
            'email' => $this->alternativeEmail,
            'age' => '22'
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            array('result' => true)
        );

        $I->sendPOST('/user/update/' . $this->testUser->ID, [
            'gender' => 'F',
            'phone' => '+79999999999',
            'email' => $this->email,
            'age' => '22'
        ]);

        $I->seeResponseIsJson();
        
        $I->seeResponseContainsJson(
            array('result' => true)
        );
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
            array('result' => true)
        );
    }
}
