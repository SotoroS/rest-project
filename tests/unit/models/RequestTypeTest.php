<?php

namespace micro\tests\unit\models;

use micro\models\RequestType;

class RequestTypeTest extends \Codeception\Test\Unit
{
    private $model;

    /**
     * @var \UnitTester
     */
    public $tester;

    public function testRequestTypeName()
    {

        // validation of correct data
        $request = new RequestType();
        $request->name = 'Lenina';
        $this->assertTrue($request->validate(['name']));

        //check for max number of characters => 256
        $request->name = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $this->assertFalse($request->validate(['name']));

        // checking for incorrect bool data
        $request->name = true;
        $this->assertFalse($request->validate(['name']));

        // checking for incorrect int data
        $request->name = 1133;
        $this->assertFalse($request->validate(['name']));

        // checking for incorrect double data
        $request->name = 3132.124;
        $this->assertFalse($request->validate(['name']));

        // checking for incorrect null data
        $request->name = null;
        $this->assertFalse($request->validate(['name']));
    }

}