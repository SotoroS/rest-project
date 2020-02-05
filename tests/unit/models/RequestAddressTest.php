<?php

namespace micro\tests\unit\models;

use micro\models\Address;
use micro\models\City;
use micro\models\Region;
use micro\models\CityArea;
use micro\models\Street;
use micro\models\RequestAddress;
use micro\models\RequestObject;
use micro\models\RequestType;
use micro\models\User;

class RequestAddressTest extends \Codeception\Test\Unit
{
    private $model;

    /**
     * @var \UnitTester
     */
    public $tester;

    

    // checking data Request Object ID 
    public function testRequestObject_id()
    {
        // validation of correct data
        $request_address = new RequestAddress();

        $request_object = new RequestObject();
        $user = new User();
        $request_type = new RequestType();
        $city = new City();
        $region = new Region();

        // create user for user_id
        $user->email = 'shukanov@yandex.ru';
        $this->assertTrue($user->save());

        // create request_type for request_type_id
        $request_type->name = 'nameRequestType';
        $this->assertTrue($request_type->save());

        // create region for region_id
        $region->name = 'nameRegion';
        $this->assertTrue($region->save());

        // create region for city_id
        $city->name = 'nameCity';
        $city->region_id = $region->id;
        $this->assertTrue($city->save());

        $request_object->num_of_people = 2;
        $request_object->family = 2;
        $request_object->pets = 2;
        $request_object->square_from = 2;
        $request_object->square_to = 2;
        $request_object->price_from = 2;
        $request_object->price_to = 2;
        $request_object->description = 'description';
        $request_object->radius = 32.41424;
        $request_object->user_id = $user->id;
        $request_object->request_type_id = $request_type->id;
        $request_object->city_id = $city->id;
        $this->assertTrue($request_object->save());

        $request_address->request_object_id = $request_object->id;
        $this->assertTrue($request_address->validate(['request_object_id']));

        // checking for incorrect bool data
        $request_address->request_object_id = true;
        $this->assertFalse($request_address->validate(['request_object_id']));

        // checking for incorrect string data
        $request_address->request_object_id = 'one';
        $this->assertFalse($request_address->validate(['request_object_id']));

        // checking for incorrect double data
        $request_address->request_object_id = 3132.124;
        $this->assertFalse($request_address->validate(['request_object_id']));

        // checking for incorrect null data
        $request_address->request_object_id = null;
        $this->assertFalse($request_address->validate(['request_object_id']));
    }
    
    public function testAddress_id()
    {
        // validation of correct data
        $request_address = new RequestAddress();
        $address = new Address();
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

        $address->lt = 231.41424;
        $address->lg = 231.42424;
        $address->region_id = $region->id;
        $address->city_id = $city->id;
        $address->city_area_id = $city_area->id;
        $address->street_id = $street->id;
        $this->assertTrue($address->save());

        $request_address->address_id = $address->id;
        $this->assertTrue($request_address->validate(['request_address']));
    }

}