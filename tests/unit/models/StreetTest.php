<?php

namespace micro\tests\unit\models;

use micro\models\Street;
use micro\models\CityArea;
use micro\models\City;
use micro\models\Region;

class StreetTest extends \Codeception\Test\Unit
{
    private $model;

    /**
     * @var \UnitTester
     */
    public $tester;

    public function testStreetName()
    {

        // validation of correct data
        $street = new Street();
        $street->name = 'Lenina';
        $this->assertTrue($street->validate(['name']));

        //check for max number of characters => 256
        $street->name = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
        aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $this->assertFalse($street->validate(['name']));

        // checking for incorrect bool data
        $street->name = true;
        $this->assertFalse($street->validate(['name']));

        // checking for incorrect int data
        $street->name = 1133;
        $this->assertFalse($street->validate(['name']));

        // checking for incorrect double data
        $street->name = 3132.124;
        $this->assertFalse($street->validate(['name']));

        // checking for incorrect null data
        $street->name = null;
        $this->assertFalse($street->validate(['name']));
    }

    public function testStreetCity_area_id()
    {
        // validation of correct data
        $street = new Street();
        $city_area = new CityArea();
        $city = new City();
        $region = new Region();

        $region->name = 'nameRegion';
        $this->assertTrue($region->save());

        $city->name = 'nameCity';
        $city->region_id = $region->id;
        $this->assertTrue($city->save());

        $city_area->name = 'nameCityArea';
        $city_area->city_id = $city->id;
        $this->assertTrue($city_area->save(['name']));

        $street->name = 'nameStreet';
        $street->city_area_id = $city_area->id;
        $this->assertTrue($street->validate(['city_area_id']));

        // checking for incorrect bool data
        $street->city_area_id = true;
        $this->assertFalse($street->validate(['city_area_id']));

        // checking for incorrect string data
        $street->city_area_id = 'one';
        $this->assertFalse($street->validate(['city_area_id']));

        // checking for incorrect double data
        $street->city_area_id = 3132.124;
        $this->assertFalse($street->validate(['city_area_id']));

        // checking for incorrect null data
        $street->city_area_id = null;
        $this->assertFalse($street->validate(['city_area_id']));
    }

}