<?php

namespace micro\tests\unit\models;

use micro\models\City;
use micro\models\Region;
use micro\models\RequestObject;
use micro\models\RequestType;
use micro\models\User;

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

        // checking the length (10,7) float 
        $request_object->pivot_lt = 43.24134;
        $this->assertTrue($request_object->validate(['pivot_lt']));

        // checking for incorrect bool data
        $request_object->pivot_lt = true;
        $this->assertFalse($request_object->validate(['pivot_lt']));

        // checking for incorrect null data
        $request_object->pivot_lt = null;
        $this->assertTrue($request_object->validate(['pivot_lt']));

        // checking for incorrect string data
        $request_object->pivot_lt = 'string';
        $this->assertFalse($request_object->validate(['pivot_lt']));
    }
    
    // checking data Pivot Lg 
    public function testRequestObjectPivotLg()
    {

        // validation of correct data
        $request_object = new RequestObject();

        // checking the length (10,7) float 
        $request_object->pivot_lg = 43.24134;
        $this->assertTrue($request_object->validate(['pivot_lg']));

        // checking for incorrect bool data
        $request_object->pivot_lg = true;
        $this->assertFalse($request_object->validate(['pivot_lg']));

        // checking for incorrect null data
        $request_object->pivot_lg = null;
        $this->assertTrue($request_object->validate(['pivot_lg']));

        // checking for incorrect string data
        $request_object->pivot_lg = 'string';
        $this->assertFalse($request_object->validate(['pivot_lg']));
    }
    
    // checking data Radius 
    public function testRequestObjectRadius()
    {

        // validation of correct data
        $request_object = new RequestObject();

        // checking the length (10,7) float 
        $request_object->radius = 43.24134;
        $this->assertTrue($request_object->validate(['radius']));

        // checking for incorrect bool data
        $request_object->radius = true;
        $this->assertFalse($request_object->validate(['radius']));

        // checking for incorrect null data
        $request_object->radius = null;
        $this->assertFalse($request_object->validate(['radius']));

        // checking for incorrect string data
        $request_object->radius = 'string';
        $this->assertFalse($request_object->validate(['radius']));
    }

    // checking data City ID 
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

        // checking for incorrect bool data
        $request_object->city_id = true;
        $this->assertFalse($request_object->validate(['city_id']));

        // checking for incorrect string data
        $request_object->city_id = 'one';
        $this->assertFalse($request_object->validate(['city_id']));

        // checking for incorrect double data
        $request_object->city_id = 3132.124;
        $this->assertFalse($request_object->validate(['city_id']));

        // checking for incorrect null data
        $request_object->city_id = null;
        $this->assertFalse($request_object->validate(['city_id']));
    }
    
    // checking data Request Type ID 
    public function testRequestObjectRequest_type_id()
    {
        // validation of correct data
        $request_object = new RequestObject();
        $request_type = new RequestType();

        $request_type->name = 'nameRequestType';
        $this->assertTrue($request_type->save());

        $request_object->request_type_id = $request_type->id;
        $this->assertTrue($request_object->validate(['request_type_id']));
        
        // checking for incorrect bool data
        $request_object->request_type_id = true;
        $this->assertFalse($request_object->validate(['request_type_id']));

        // checking for incorrect string data
        $request_object->request_type_id = 'one';
        $this->assertFalse($request_object->validate(['request_type_id']));

        // checking for incorrect double data
        $request_object->request_type_id = 3132.124;
        $this->assertFalse($request_object->validate(['request_type_id']));

        // checking for incorrect null data
        $request_object->request_type_id = null;
        $this->assertFalse($request_object->validate(['request_type_id']));
    }
    
    // checking data User ID
    public function testRequestObjectUser_id()
    {
        // validation of correct data
        $request_object = new RequestObject();
        $user = new User();

        $user->email = 'shukanov@yandex.ru';
        $this->assertTrue($user->save());

        $request_object->user_id = $user->id;
        $this->assertTrue($request_object->validate(['user_id']));
        
        // checking for incorrect bool data
        $request_object->user_id = true;
        $this->assertFalse($request_object->validate(['user_id']));

        // checking for incorrect string data
        $request_object->user_id = 'one';
        $this->assertFalse($request_object->validate(['user_id']));

        // checking for incorrect double data
        $request_object->user_id = 3132.124;
        $this->assertFalse($request_object->validate(['user_id']));

        // checking for incorrect null data
        $request_object->user_id = null;
        $this->assertFalse($request_object->validate(['user_id']));
    }

}