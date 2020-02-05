<?php 

namespace micro\tests\unit\models;

use micro\models\Address;

class AddressTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    public $tester;

    /**
    * Test model 'Address' column 'lt'
    */
    public function testAddressLT()
    {
        $address = new  Address();

        // Checking for null
        $address->lt = null;
        $this->assertFalse($address->validate(['lt']));

        // checking for boolean
        $address->lt = true;
        $this->assertFalse($address->validate(['lt']));

        // checking for integer
        $address->lt = 1;
        $this->assertFalse($address->validate(['lt']));

        // checking for string
        $address->lt = 'name';
        $this->assertFalse($address->validate(['lt']));

        // checking the length (10,8)
        $address->lt = 12.12345678;
        $this->assertFalse($address->validate(['lt']));
        
        // checking the length (11,7)
        $address->lt = 123.12345678;
        $this->assertFalse($address->validate(['lt']));

        // checking the length (10,7) float (-)
        $address->lt = -12.1234567;
        $this->assertTrue($address->validate(['lt']));

        // checking the length (10,7) float (+)
        $address->lt = 12.1234567;
        $this->assertTrue($address->validate(['lt']));
    }

    public function testAddressLG()
    {
        // $address = new  Address();

        // // Checking for null
        // $address->lg = null;
        // $this->assertFalse($address->validate(['lg']));

        // // checking for boolean
        // $address->lg = true;
        // $this->assertFalse($address->validate(['lg']));

        // // checking for integer
        // $address->lg = 1;
        // $this->assertFalse($address->validate(['lg']));

        // // checking for string
        // $address->lg = 'name';
        // $this->assertFalse($address->validate(['lg']));

        // // checking the length (10,8)
        // $address->lg = 12.12345678;
        // $this->assertFalse($address->validate(['lg']));
        
        // // checking the length (11,7)
        // $address->lg = 123.12345678;
        // $this->assertFalse($address->validate(['lg']));

        // // checking the length (10,7)
        // $address->lg = 12.1234567;
        // $this->assertTrue($address->validate(['lg']));
        
        // // checking the length (10,7) float (-)
        // $address->lg = -12.1234567;
        // $this->assertTrue($address->validate(['lg']));

        // // checking the length (10,7) float (+)
        // $address->lg = 12.1234567;
        // $this->assertTrue($address->validate(['lg']));
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