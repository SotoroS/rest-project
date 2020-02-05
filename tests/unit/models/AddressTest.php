<?php 

namespace micro\tests\unit\models;

use micro\models\Address;

class AddressTest extends \Codeception\Test\Unit
{
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

    /**
    * Test model 'Address' column 'region_id'
    */
    public function testAddressRegionID()
    {
        $address = new  Address();

        // checking for boolean
        $address->region_id = true;
        $this->assertFalse($address->validate(['region_id']));

        // checking for float
        $address->region_id = 1.1;
        $this->assertFalse($address->validate(['region_id']));

        // checking for string
        $address->region_id = 'region_id';
        $this->assertFalse($address->validate(['region_id']));

        // checking the length (12)
        $address->region_id = 123456789012;
        $this->assertFalse($address->validate(['region_id']));

        // // checking the length (11)
        // $address->region_id = 1234567890;
        // $this->assertTrue($address->validate(['region_id']));        
    }

    /**
    * Test model 'Address' column 'city_id'
    */
    public function testAddressCityID()
    {
        $address = new  Address();

        // Checking for null
        $address->city_id = null;
        $this->assertFalse($address->validate(['city_id']));

        // checking for boolean
        $address->city_id = true;
        $this->assertFalse($address->validate(['city_id']));

        // checking for float
        $address->city_id = 1.1;
        $this->assertFalse($address->validate(['city_id']));

        // checking for string
        $address->city_id = 'city_id';
        $this->assertFalse($address->validate(['city_id']));

        // checking the length (12)
        $address->city_id = 1234567890;
        $this->assertFalse($address->validate(['city_id']));

        // // checking the length (11)
        // $address->city_id = 12345678901;
        // $this->assertTrue($address->validate(['city_id']));   
    }
    
    /**
    * Test model 'Address' column 'city_area_id'
    */
    public function testAddressCityAreaID()
    {
        $address = new  Address();

        // checking for boolean
        $address->city_area_id = true;
        $this->assertFalse($address->validate(['city_area_id']));

        // checking for float
        $address->city_area_id = 1.1;
        $this->assertFalse($address->validate(['city_area_id']));

        // checking for string
        $address->city_area_id = 'city_area_id';
        $this->assertFalse($address->validate(['city_area_id']));

        // checking the length (12)
        $address->city_area_id = 123456789012;
        $this->assertFalse($address->validate(['city_area_id']));

        // // checking the length (11)
        // $address->city_area_id = 12345678901;
        // $this->assertTrue($address->validate(['city_area_id']));        
    }

    /**
    * Test model 'Address' column 'street_id'
    */
    public function testAddressStreetID()
    {
        $address = new  Address();

        // Checking for null
        $address->street_id = null;
        $this->assertFalse($address->validate(['street_id']));

        // checking for boolean
        $address->street_id = true;
        $this->assertFalse($address->validate(['street_id']));

        // checking for float
        $address->street_id = 1.1;
        $this->assertFalse($address->validate(['street_id']));

        // checking for string
        $address->street_id = 'street_id';
        $this->assertFalse($address->validate(['street_id']));

        // checking the length (12)
        $address->street_id = 123456789012;
        $this->assertFalse($address->validate(['street_id']));

        // // checking the length (11)
        // $address->street_id = 12345678901;
        // $this->assertTrue($address->validate(['street_id']));        

    }
}