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

    public function testEmailIsSentOnContact()
    {
        $user = new User();

        $user->email = 'sir.sotoros@ya.ru';
        
        $this->assertTrue($user->validate());
    }
}