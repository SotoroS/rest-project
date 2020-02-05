<?php 

namespace micro\tests\unit\models;

use micro\models\City;

class CityTest extends \Codeception\Test\Unit
{
    /**
    * Test model 'City' column 'name'
    */
    public function testCityName()
    {
        $city = new City();

        // validation of correct data 
        // $street = new Street(); 
        // $city_area = new CityArea(); 
        // $city = new City(); 
        // $region = new Region(); 

        // $region->name = 'nameRegion'; 
        // $this->assertTrue($region->save()); 

        // $city->name = 'nameCity'; 
        // $city->region_id = $region->id; 
        // $this->assertTrue($city->save()); 

        // $city_area->name = 'nameCityArea'; 
        // $city_area->city_id = $city->id; 
        // $this->assertTrue($city_area->save(['name'])); 

        // $street->name = 'nameStreet'; 
        // $street->city_area_id = $city_area->id; 
        // $this->assertTrue($street->validate(['city_area_id']));

        // Checking for null
        $city->name = null;
        $this->assertFalse($city->validate(['name']));

        // checking for boolean
        $city->name = true;
        $this->assertFalse($city->validate(['name']));

        // checking for integer
        $city->name = 1;
        $this->assertFalse($city->validate(['name']));

        // checking for float
        $city->name = 1.1;
        $this->assertFalse($city->validate(['name']));

        // checking the length (300)
        $city->name = '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890';
        $this->assertFalse($city->validate(['name']));

        // checking the length (256)
        $city->name = '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456';
        $this->assertTrue($city->validate(['name']));

        // checking for string
        $city->name = 'name';
        $this->assertTrue($city->validate(['name']));
    }

    /**
    * Test model 'City' column 'region_id'
    */
    public function testCityRegionId()
    {
        $city = new City();

        // Checking for null
        $city->region_id = null;
        $this->assertFalse($city->validate(['region_id']));

        // checking for boolean
        $city->region_id = true;
        $this->assertFalse($city->validate(['region_id']));

        // checking for float
        $city->region_id = 1.1;
        $this->assertFalse($city->validate(['region_id']));

        // checking for string
        $city->region_id = 'city';
        $this->assertFalse($city->validate(['region_id']));

        // checking the length (12)
        $city->region_id = 123456789012;
        $this->assertFalse($city->validate(['region_id']));

        // // checking the length (11)
        // $address->region_id = 12345678901;
        // $this->assertTrue($address->validate(['region_id']));        
    }
}