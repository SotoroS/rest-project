<?php

namespace micro\tests\unit\models;

use micro\models\Region;

class RegionTest extends \Codeception\Test\Unit
{
    private $model;

    /**
     * @var \UnitTester
     */
    public $tester;

    public function testRegionTypeName()
    {

        // validation of correct data
        $region = new Region();
        $region->name = 'Lenina';
        $this->assertTrue($region->validate(['name']));

        //check for max number of characters => 256
        $region->name = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $this->assertFalse($region->validate(['name']));

        // checking for incorrect bool data
        $region->name = true;
        $this->assertFalse($region->validate(['name']));

        // checking for incorrect int data
        $region->name = 1133;
        $this->assertFalse($region->validate(['name']));

        // checking for incorrect double data
        $region->name = 3132.124;
        $this->assertFalse($region->validate(['name']));

        // checking for incorrect null data
        $region->name = null;
        $this->assertFalse($region->validate(['name']));
    }

}