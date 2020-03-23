<?php

use micro\models\User;
use micro\models\EstateObject;
use yii\web\UploadedFile;

/**
 * Test for ObjectController
 * 
 * Class ObjectControllerCest
 */
class ObjectControllerCest
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
    public $testObject = null;

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
            'address' => 'г.Волгоград, ул.50-летия ВЛКСМ',
            'name' => 'test',
            'description' => 'test',
            'price' => '5000000'
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
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
            'data' => 'array',
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

        $this->_init($I);

        $images = UploadedFile::getInstancesByName('images');

        $I->sendPOST('/object/update/' . $this->testObject->id, [
            'name' => 'updateTest',
            'phone' => '+79999999999',
            'images' => [
                codecept_data_dir('1.jpg'),
                codecept_data_dir('2.jpg'),
                codecept_data_dir('3.jpg'),
            ]
        ]);

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            array('result' => true)
        );
    }

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

        $I->sendGET('/object/view/' . $this->testObject->id);

        $I->seeResponseIsJson();

        Codeception\Util\JsonType::addCustomFilter('datetime', function ($value) {
            return preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $value, $matches);
        });

        $I->seeResponseMatchesJsonType([
            'object' => array(
                'id' => 'integer',
                'address_id' => 'integer',
                'building_type_id' => 'integer',
                'rent_type' => 'integer',
                'property_type' => 'integer',
                'metro_id' => 'integer',
                'name' => 'string',
                'description' => 'string',
                'price' => 'integer',
                'url' => 'string:url|null',
                'user_id' => 'integer',
                'city_id' => 'integer',
                'city_area_id' => 'integer',
                'created_at' => 'string:datetime',
                'updated_at' => 'string:datetime',
                'data' => 'boolean|null'
            ),
            'images' => 'array',
            'phones' => 'array'
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

        // Set OAuth 2.0 token
        $I->amBearerAuthenticated($this->token);

        $this->testObject = EstateObject::find()
                ->where(['user_id' => $this->testUser->id])
                ->orderBy('id DESC')
                ->one();

        if (is_null($this->testObject) && $needTestObject)
        {
            $this->testObject = EstateObject::find()
                ->where(['user_id' => $this->testUser->id])
                ->orderBy('id DESC')
                ->one();
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
