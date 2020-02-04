<?php 

namespace micro\tests\functional;

use micro\models\User;

class SignupCest
{
    protected $tester;

    public function _before(FunctionalTester $I)
    {
    }

    function testSignupUser()
    {
        $user = new User();
        $user->setEmail('glebikon@yandex.ru');
        $user->save();
        $this->assertEquals('glebikon@yandex.ru', $user->getEmail());
        $this->tester->seeInDatabase('rest', ['emaile' => 'glebikon@yandex.ru']);
    }
}
