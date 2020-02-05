<?php

namespace micro\tests\unit\models;

use micro\models\Street;
use micro\models\CityArea;
use micro\models\City;
use micro\models\Region;
use micro\models\RequestObject;

class RequestObjectTest extends \Codeception\Test\Unit
{
    private $model;

    /**
     * @var \UnitTester
     */
    public $tester;

    // checking data Num of People
    public function testRequestObjectNumOFPeople()
    {

        // validation of correct data
        $request_object = new RequestObject();

        $request_object->num_of_people = 4;
        $this->assertTrue($request_object->validate(['num_of_people']));

        // checking for incorrect bool data
        $request_object->num_of_people = true;
        $this->assertFalse($request_object->validate(['num_of_people']));

        // checking for incorrect double data
        $request_object->num_of_people = 3132.124;
        $this->assertFalse($request_object->validate(['num_of_people']));

        // checking for incorrect null data
        $request_object->num_of_people = null;
        $this->assertFalse($request_object->validate(['num_of_people']));
        
        // checking for incorrect string data
        $request_object->num_of_people = 'string';
        $this->assertFalse($request_object->validate(['num_of_people']));
    }

    // checking data Family 
    public function testRequestObjectFamily()
    {

        // validation of correct data
        $request_object = new RequestObject();

        $request_object->family = 4;
        $this->assertTrue($request_object->validate(['family']));

        // checking for incorrect bool data
        $request_object->family = true;
        $this->assertFalse($request_object->validate(['family']));

        // checking for incorrect double data
        $request_object->family = 3132.124;
        $this->assertFalse($request_object->validate(['family']));

        // checking for incorrect null data
        $request_object->family = null;
        $this->assertFalse($request_object->validate(['family']));
        
        // checking for incorrect string data
        $request_object->family = 'string';
        $this->assertFalse($request_object->validate(['family']));
    }
    
    // checking data Pets 
    public function testRequestObjectPets()
    {

        // validation of correct data
        $request_object = new RequestObject();

        $request_object->family = 4;
        $this->assertTrue($request_object->validate(['family']));

        // checking for incorrect bool data
        $request_object->family = true;
        $this->assertFalse($request_object->validate(['family']));

        // checking for incorrect double data
        $request_object->family = 3132.124;
        $this->assertFalse($request_object->validate(['family']));

        // checking for incorrect null data
        $request_object->family = null;
        $this->assertFalse($request_object->validate(['family']));

        // checking for incorrect string data
        $request_object->family = 'string';
        $this->assertFalse($request_object->validate(['family']));
    }
    
    // checking data Square From 
    public function testRequestObjectSquareFrom()
    {

        // validation of correct data
        $request_object = new RequestObject();

        $request_object->square_from = 4;
        $this->assertTrue($request_object->validate(['square_from']));

        // checking for incorrect bool data
        $request_object->square_from = true;
        $this->assertFalse($request_object->validate(['square_from']));

        // checking for incorrect double data
        $request_object->square_from = 3132.124;
        $this->assertFalse($request_object->validate(['square_from']));

        // checking for incorrect null data
        $request_object->square_from = null;
        $this->assertFalse($request_object->validate(['square_from']));
        
        // checking for incorrect string data
        $request_object->square_from = 'string';
        $this->assertFalse($request_object->validate(['square_from']));
    }
    
    // checking data Square To 
    public function testRequestObjectSquareTo()
    {

        // validation of correct data
        $request_object = new RequestObject();

        $request_object->square_to = 4;
        $this->assertTrue($request_object->validate(['square_to']));

        // checking for incorrect bool data
        $request_object->square_to = true;
        $this->assertFalse($request_object->validate(['square_to']));

        // checking for incorrect double data
        $request_object->square_to = 3132.124;
        $this->assertFalse($request_object->validate(['square_to']));

        // checking for incorrect null data
        $request_object->square_to = null;
        $this->assertFalse($request_object->validate(['square_to']));
        
        // checking for incorrect string data
        $request_object->square_to = 'string';
        $this->assertFalse($request_object->validate(['square_to']));
    }
    
    // checking data Price From 
    public function testRequestObjectPriceFrom()
    {

        // validation of correct data
        $request_object = new RequestObject();

        $request_object->price_from = 4;
        $this->assertTrue($request_object->validate(['price_from']));

        // checking for incorrect bool data
        $request_object->price_from = true;
        $this->assertFalse($request_object->validate(['price_from']));

        // checking for incorrect double data
        $request_object->price_from = 3132.124;
        $this->assertFalse($request_object->validate(['price_from']));

        // checking for incorrect null data
        $request_object->price_from = null;
        $this->assertFalse($request_object->validate(['price_from']));
        
        // checking for incorrect string data
        $request_object->price_from = 'string';
        $this->assertFalse($request_object->validate(['price_from']));
    }
    
    // checking data Price To 
    public function testRequestObjectPriceTo()
    {

        // validation of correct data
        $request_object = new RequestObject();

        $request_object->price_to = 4;
        $this->assertTrue($request_object->validate(['price_to']));

        // checking for incorrect bool data
        $request_object->price_to = true;
        $this->assertFalse($request_object->validate(['price_to']));

        // checking for incorrect double data
        $request_object->price_to = 3132.124;
        $this->assertFalse($request_object->validate(['price_to']));

        // checking for incorrect null data
        $request_object->price_to = null;
        $this->assertFalse($request_object->validate(['price_to']));

        // checking for incorrect string data
        $request_object->price_to = 'string';
        $this->assertFalse($request_object->validate(['price_to']));
    }
    
    // checking data Description 
    public function testRequestObjectDescription()
    {

        // validation of correct data
        $request_object = new RequestObject();

        $request_object->description = 'string description';
        $this->assertTrue($request_object->validate(['description']));
        
        // checking for incorrect int data
        $request_object->description = 4;
        $this->assertFalse($request_object->validate(['description']));

        // checking for incorrect bool data
        $request_object->description = true;
        $this->assertFalse($request_object->validate(['description']));

        // checking for incorrect double data
        $request_object->description = 3132.124;
        $this->assertFalse($request_object->validate(['description']));

        // checking for incorrect null data
        $request_object->description = null;
        $this->assertFalse($request_object->validate(['description']));
    }

    // checking data Pivot Lt 
    public function testRequestObjectPivotLt()
    {

        // validation of correct data
        $request_object = new RequestObject();

        $request_object->price_to = 4;
        $this->assertTrue($request_object->validate(['price_to']));

        // checking for incorrect bool data
        $request_object->price_to = true;
        $this->assertFalse($request_object->validate(['price_to']));

        // checking for incorrect double data
        $request_object->price_to = 3132.124;
        $this->assertFalse($request_object->validate(['price_to']));

        // checking for incorrect null data
        $request_object->price_to = null;
        $this->assertFalse($request_object->validate(['price_to']));

        // checking for incorrect string data
        $request_object->price_to = 'string';
        $this->assertFalse($request_object->validate(['price_to']));
    }

    public function testRequestObjectCity_id()
    {
        // validation of correct data
        $request_object = new RequestObject();
        $city = new City();
        $region = new Region();

        $region->name = 'nameRegion';
        $this->assertTrue($region->save());

        $city->name = 'nameCity';
        $city->region_id = $region->id;
        $this->assertTrue($city->save());

        $request_object->city_id = $city->id;
        $this->assertTrue($request_object->validate(['city_id']));
    }
    
    // public function testRequestObjectRequest_type_id()
    // {
        // validation of correct data
        // $request_object = new RequestObject();
        // $city = new City();
        // $region = new Region();

        // $region->name = 'nameRegion';
        // $this->assertTrue($region->save());

        // $city->name = 'nameCity';
        // $city->region_id = $region->id;
        // $this->assertTrue($city->save());

        // $request_object->city_id = $city->id;
        // $this->assertTrue($request_object->validate(['city_id']));
    // }


}