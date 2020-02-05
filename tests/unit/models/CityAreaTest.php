<?php 

namespace micro\tests\unit\models;

use micro\models\CityArea;
use micro\models\City;
use micro\models\Region;

class CityAreaTest extends \Codeception\Test\Unit
{
    /**
    * Test model 'CityArea' column 'name'
    */
    public function testCityAreaName()
    {
        $city_area = new CityArea();

        // Checking for null
        $city_area->name = null;
        $this->assertFalse($city_area->validate(['name']));

        // checking for boolean
        $city_area->name = true;
        $this->assertFalse($city_area->validate(['name']));

        // checking for integer
        $city_area->name = 1;
        $this->assertFalse($city_area->validate(['name']));

        // checking for float
        $city_area->name = 1.1;
        $this->assertFalse($city_area->validate(['name']));

        // checking the length (257)
        $city_area->name = '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567';
        $this->assertFalse($city_area->validate(['name']));

        // checking the length (256)
        $city_area->name = '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456';
        $this->assertTrue($city_area->validate(['name']));

        // checking for string
        $city_area->name = 'name';
        $this->assertTrue($city_area->validate(['name']));
    }

    /**
    * Test model 'CityArea' column 'city_id'
    */
    public function testCityAreaCityId()
    {
        $city_area = new CityArea();
        $city = new City();
        $region = new Region();

        $region->name = 'nameRegion';
        $this->assertTrue($region->save());

        $city->name = 'nameCity';
        $city->region_id = $region->id;
        $this->assertTrue($city->save());

        // checking relationship
        $city_area->name = 'nameCityArea';
        $city_area->city_id = $city->id;
        $this->assertTrue($city_area->validate(['city_id']));

        // Checking for null
        $city_area->city_id = null;
        $this->assertFalse($city_area->validate(['city_id']));

        // checking for boolean
        $city_area->city_id = true;
        $this->assertFalse($city_area->validate(['city_id']));

        // checking for float
        $city_area->city_id = 1.1;
        $this->assertFalse($city_area->validate(['city_id']));

        // checking for string
        $city_area->city_id = 'city';
        $this->assertFalse($city_area->validate(['city_id']));     
    }
}