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
        $region = new Region(); 
        
        $region->name = 'name'; 
        $this->assertTrue($region->save()); 

        // checking relationship
        $address->region_id = $region->id;
        $this->assertTrue($address->validate(['region_id']));

        // checking for boolean
        $address->region_id = true;
        $this->assertFalse($address->validate(['region_id']));

        // checking for float
        $address->region_id = 1.1;
        $this->assertFalse($address->validate(['region_id']));

        // checking for string
        $address->region_id = 'region_id';
        $this->assertFalse($address->validate(['region_id']));    
    }

    /**
    * Test model 'Address' column 'city_id'
    */
    public function testAddressCityID()
    {
        $address = new  Address();
        $city = new City();
        $region = new Region();

        $region->name = 'name';
        $this->assertTrue($region->save());

        $city->name = 'name';
        $city->region_id = $region->id;
        $this->assertTrue($city->save());

        // checking relationship
        $address->city_id = $city->id;
        $this->assertTrue($address->validate(['city_id']));

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
    }
    
    /**
    * Test model 'Address' column 'city_area_id'
    */
    public function testAddressCityAreaID()
    {
        $address = new  Address();
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

        // checking relationship
        $address->city_area_id = $city_area->id;
        $this->assertTrue($address->validate(['city_area_id']));

        // checking for boolean
        $address->city_area_id = true;
        $this->assertFalse($address->validate(['city_area_id']));

        // checking for float
        $address->city_area_id = 1.1;
        $this->assertFalse($address->validate(['city_area_id']));

        // checking for string
        $address->city_area_id = 'city_area_id';
        $this->assertFalse($address->validate(['city_area_id']));     
    }

    /**
    * Test model 'Address' column 'street_id'
    */
    public function testAddressStreetID()
    {
        $address = new  Address();
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
        $this->assertTrue($city_area->save());

        $street->name = 'nameStreet';
        $street->city_area_id = $city_area->id;
        $this->assertTrue($street->save());

        // checking relationship
        $address->street_id = $street->id;
        $this->assertTrue($address->validate(['street_id']));

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
    }
}