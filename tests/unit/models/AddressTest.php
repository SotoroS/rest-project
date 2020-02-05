<?php 

namespace micro\tests\unit\models;

use micro\models\Address;
use micro\models\Street;
use micro\models\CityArea;
use micro\models\City;
use micro\models\Region;

class AddressTest extends \Codeception\Test\Unit
{
    /**
    * Test model 'Address' column 'lt'
    */
    public function testAddressLT()
    {
        $address = new Address();
        $address->regionName = 'name';
        $address->cityName = 'name';
        $address->cityAreaName = 'name';
        $address->streetName = 'name';

        // Checking for null
        $address->lt = null;
        $this->assertFalse($address->validate(['lt']));

        // checking for boolean
        $address->lt = true;
        $this->assertFalse($address->validate(['lt']));

        // checking for string
        $address->lt = 'name';
        $this->assertFalse($address->validate(['lt']));

        // checking the length (10,7) float (-)
        $address->lt = -12.1234567;
        $this->assertTrue($address->validate(['lt']));

        // checking the length (10,7) float (+)
        $address->lt = 12.1234567;
        $this->assertTrue($address->validate(['lt']));
    }

    /**
    * Test model 'Address' column 'lg'
    */
    public function testAddressLG()
    {
        $address = new  Address();
        $address->regionName = 'name';
        $address->cityName = 'name';
        $address->cityAreaName = 'name';
        $address->streetName = 'name';

        // Checking for null
        $address->lg = null;
        $this->assertFalse($address->validate(['lg']));

        // checking for boolean
        $address->lg = true;
        $this->assertFalse($address->validate(['lg']));

        // checking for string
        $address->lg = 'name';
        $this->assertFalse($address->validate(['lg']));

        // checking the length (10,7) float (-)
        $address->lg = -12.1234567;
        $this->assertTrue($address->validate(['lg']));

        // checking the length (10,7) float (+)
        $address->lg = 12.1234567;
        $this->assertTrue($address->validate(['lg']));
    }


}