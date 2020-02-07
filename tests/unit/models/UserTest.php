<?php

namespace micro\tests\unit\models;

use micro\models\User;

class UserTest extends \Codeception\Test\Unit
{
    private $model;

    /**
     * @var \UnitTester
     */
    public $tester;

    // validation of correct data
    public function testEmailIsSentOnContact()
    {
        $user = new User();
        $user->email = 'sir.sotoros@ya.ru';
        $this->assertTrue($user->validate());

        //check for max number of characters => 256
        $user->email = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $this->assertFalse($user->validate());

        // checking for empty value
        $user->email = '';
        $this->assertFalse($user->validate(['email']));

        // mail validation
        $user->email = 424;
        $this->assertFalse($user->validate(['email']));

        // mail validation
        $user->email = true;
        $this->assertFalse($user->validate(['email']));

        // mail validation
        $user->email = 24.31;
        $this->assertFalse($user->validate(['email']));
        
        // mail validation
        $user->email = 'string';
        $this->assertFalse($user->validate(['email']));

        // ---------------------------Age------------------------------------------------------

        // validation of correct data age
        $user->age = 24;
        $this->assertTrue($user->validate(['age']));

        // checking for incorrect string data age
        $user->age = 'seven';
        $this->assertFalse($user->validate(['age']));

        // checking for incorrect double data age
        $user->age = 341.242;
        $this->assertFalse($user->validate(['age']));

        // checking for incorrect bool data age
        $user->age = false;
        $this->assertFalse($user->validate(['age']));

        // --------------------------Verified--------------------------------------------------

        // validation of correct data verified
        $user->verified = 1;
        $this->assertTrue($user->validate(['verified']));

        // checking for incorrect string data verified
        $user->verified = 'One';
        $this->assertFalse($user->validate(['verified']));

        // checking for incorrect bool data verified
        $user->verified = true;
        $this->assertFalse($user->validate(['verified']));

        // checking for incorrect double data verified
        $user->verified = 1.23;
        $this->assertFalse($user->validate(['verified']));

        // --------------------------Gender----------------------------------------------------

        // validation of correct data verified Gender
        $user->gender = 'M';
        $this->assertTrue($user->validate(['gender']));

        // checking for incorrect int data Gender
        $user->gender = 1;
        $this->assertFalse($user->validate(['gender']));

        // checking for incorrect double data Gender
        $user->gender = 1.42;
        $this->assertFalse($user->validate(['gender']));

        // checking for incorrect bool data Gender
        $user->gender = true;
        $this->assertFalse($user->validate(['gender']));

        // check for max number of characters => 1
        $user->gender = 'Fe';
        $this->assertFalse($user->validate(['gender']));

        // --------------------------------Phone------------------------------------------------

        // validation of correct data verified Phone
        $user->phone = '8-999-777-66-55';
        $this->assertTrue($user->validate(['phone']));

        // checking for incorrect int data Phone
        $user->phone = 1124413312;
        $this->assertFalse($user->validate(['phone']));

        // checking for incorrect double data Phone
        $user->phone = 112441.3312;
        $this->assertFalse($user->validate(['phone']));

        // check for max number of characters => 30
        $user->phone = '00000000000000000000000000000000000';
        $this->assertFalse($user->validate(['phone']));

        // checking for incorrect bool data Phone
        $user->phone = true;
        $this->assertFalse($user->validate(['phone']));

        // -----------------------------------------Pasword-------------------------------------------

        // validation of correct data verified Password
        $user->password = 'passwordTest';
        $this->assertTrue($user->validate(['password']));

        // checking for incorrect int data Password
        $user->password = 1124413312;
        $this->assertFalse($user->validate(['password']));

        // checking for incorrect double data Password
        $user->password = 1124413.312;
        $this->assertFalse($user->validate(['password']));

        // checking for incorrect bool data Password
        $user->password = true;
        $this->assertFalse($user->validate(['password']));

        // check for max number of characters => 256
        $user->password = 'paswordddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd
        ddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd
        ddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd';
        $this->assertFalse($user->validate(['password']));

        // ----------------------------------------------------------AccessToken-------------------------------------------

        // validation of correct data verified Access Token
        $user->access_token = 'access_tokenTest';
        $this->assertTrue($user->validate(['access_token']));

        // checking for incorrect int data Access Token
        $user->access_token = 1124413312;
        $this->assertFalse($user->validate(['access_token']));

        // checking for incorrect double data Access Token
        $user->access_token = 1124413.312;
        $this->assertFalse($user->validate(['access_token']));

        // checking for incorrect bool data Access Token
        $user->access_token = true;
        $this->assertFalse($user->validate(['access_token']));

        // check for max number of characters => 256
        $user->access_token = 'access_tokennnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn
        nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn
        nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn';
        $this->assertFalse($user->validate(['access_token']));

        // ---------------------------------------------SignupToken---------------------------------------------------------------

        // validation of correct data verified Signup Token
        $user->signup_token = 'signup_token';
        $this->assertTrue($user->validate(['signup_token']));

        // checking for incorrect int data Signup Token
        $user->signup_token = 1124413312;
        $this->assertFalse($user->validate(['signup_token']));

        // checking for incorrect double data Signup Token
        $user->signup_token = 1124413.312;
        $this->assertFalse($user->validate(['signup_token']));

        // checking for incorrect bool data Signup Token
        $user->signup_token = true;
        $this->assertFalse($user->validate(['signup_token']));

        // check for max number of characters => 256
        $user->signup_token = 'signup_tokennnnnn';
        $this->assertFalse($user->validate(['signup_token']));
    }
}