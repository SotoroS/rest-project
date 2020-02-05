<?php 

namespace micro\tests\unit\models;

use micro\models\City;
use micro\models\Region;

class CityTest extends \Codeception\Test\Unit
{
    /**
    * Test model 'City' column 'name'
    */
    public function testCityName()
    {
        $city = new City();
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

        // checking the length (257)
        $city->name = '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567';
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
        $region = new Region(); 

        $region->name = 'nameRegion'; 
        $this->assertTrue($region->save()); 

        $city->name = 'nameCity'; 
        $city->region_id = $region->id; 
        $this->assertTrue($city->validate(['region_id'])); 

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
    }
}