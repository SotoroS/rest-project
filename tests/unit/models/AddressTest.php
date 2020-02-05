<?php 

namespace micro\tests\unit\models;

use micro\models\Address;

class AddressTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    public $tester;

    $address = new  Address();

    public function testAddressLT()
    {
        // Checking for null
        $building->lt = null;
        $this->assertFalse($building->validate(['lt']));

        // checking for boolean
        $building->lt = true;
        $this->assertFalse($building->validate(['lt']));

        // checking for integer
        $building->lt = 1;
        $this->assertFalse($building->validate(['lt']));

        // checking for string
        $building->lt = 'name';
        $this->assertFalse($building->validate(['lt']));

        // checking the length (300)
        $building->lt = 23.12345678;
        $this->assertFalse($building->validate(['lt']));

        // checking the length (2.7)
        $building->lt = 23.1234567;
        $this->assertTrue($building->validate(['lt']));
        
        // checking for float (-)
        $building->lt = -23.1345;
        $this->assertTrue($building->validate(['lt']));

        // checking for float (+)
        $building->lt = 23.1345;
        $this->assertTrue($building->validate(['lt']));
    }

    public function testAddressLG()
    {
        // Checking for null
        $building->lt = null;
        $this->assertFalse($building->validate(['lt']));

        // checking for boolean
        $building->lt = true;
        $this->assertFalse($building->validate(['lt']));

        // checking for integer
        $building->lt = 1;
        $this->assertFalse($building->validate(['lt']));

        // checking for string
        $building->lt = 'name';
        $this->assertFalse($building->validate(['lt']));

        // checking the length (300)
        $building->lt = 23.12345678;
        $this->assertFalse($building->validate(['lt']));

        // checking the length (2.7)
        $building->lt = 23.1234567;
        $this->assertTrue($building->validate(['lt']));
        
        // checking for float (-)
        $building->lt = -23.1345;
        $this->assertTrue($building->validate(['lt']));

        // checking for float (+)
        $building->lt = 23.1345;
        $this->assertTrue($building->validate(['lt']));
    }
    public function testAddressCityID()
    {
        
    }
    public function testAddressStreetID()
    {
        
    }
    public function testAddressRegionID()
    {
        
    }
    public function testAddressCityAreaID()
    {
        
    }
}