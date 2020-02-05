<?php

namespace micro\tests\unit\models;

use micro\models\RentType;

class RentTypeTest extends \Codeception\Test\Unit
{
    private $model;

    /**
     * @var \UnitTester
     */
    public $tester;

    public function testRentTypeTypeName()
    {

        // validation of correct data
        $rent = new RentType();
        $rent->name = 'Lenina';
        $this->assertTrue($rent->validate(['name']));

        //check for max number of characters => 256
        $rent->name = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $this->assertFalse($rent->validate(['name']));

        // checking for incorrect bool data
        $rent->name = true;
        $this->assertFalse($rent->validate(['name']));

        // checking for incorrect int data
        $rent->name = 1133;
        $this->assertFalse($rent->validate(['name']));

        // checking for incorrect double data
        $rent->name = 3132.124;
        $this->assertFalse($rent->validate(['name']));

        // checking for incorrect null data
        $rent->name = null;
        $this->assertFalse($rent->validate(['name']));
    }

}