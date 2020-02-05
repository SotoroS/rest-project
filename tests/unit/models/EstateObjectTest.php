<?php 

namespace micro\tests\unit\models;

use micro\models\EstateObject;
use micro\models\Address;
use micro\models\Street;
use micro\models\CityArea;
use micro\models\City;
use micro\models\Region;
use micro\models\RentType;
use micro\models\PropertyType;
use micro\models\BuildingType;
use micro\models\Metro;
use micro\models\User;

class EstateObjectTest extends \Codeception\Test\Unit
{
    /**
    * Test model 'EstateObject' column 'address_id'
    */
    public function testEstateObjectAddressID()
    {
        $estate_object = new  EstateObject();
        $address = new Address();
        $street = new Street();
        $city_area = new CityArea();
        $city = new City();
        $region = new Region();
        $address->regionName = 'name';
        $address->cityName = 'name';
        $address->cityAreaName = 'name';
        $address->streetName = 'name';

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

        // checking relationship
        $estate_object->address_id = $address->id;
        $this->assertTrue($estate_object->validate(['address_id']));


        // Checking for null
        $estate_object->address_id = null;
        $this->assertFalse($estate_object->validate(['address_id']));

        // checking for boolean
        $estate_object->address_id = true;
        $this->assertFalse($estate_object->validate(['address_id']));

        // checking for float
        $estate_object->address_id = 1.1;
        $this->assertFalse($estate_object->validate(['address_id']));

        // checking for string
        $estate_object->address_id = 'address_id';
        $this->assertFalse($estate_object->validate(['address_id']));      
    }

    /**
    * Test model 'EstateObject' column 'rent_type_id'
    */
    public function testEstateObjectRentTypeID()
    {
        $estate_object = new  EstateObject();
        $rent_type = new RentType(); 
        
        $rent_type->name = 'name'; 
        $this->assertTrue($rent_type->save()); 

        // checking relationship
        $estate_object->rent_type_id = $rent_type->id;
        $this->assertTrue($estate_object->validate(['rent_type_id']));

        // Checking for null
        $estate_object->rent_type_id = null;
        $this->assertFalse($estate_object->validate(['rent_type_id']));

        // checking for boolean
        $estate_object->rent_type_id = true;
        $this->assertFalse($estate_object->validate(['rent_type_id']));

        // checking for float
        $estate_object->rent_type_id = 1.1;
        $this->assertFalse($estate_object->validate(['rent_type_id']));

        // checking for string
        $estate_object->rent_type_id = 'rent_type_id';
        $this->assertFalse($estate_object->validate(['rent_type_id']));
    }

    /**
    * Test model 'EstateObject' column 'property_type_id'
    */
    public function testEstateObjectPropertyTypeID()
    {
        $estate_object = new  EstateObject();
        $property_type = new PropertyType(); 
        
        $property_type->name = 'name'; 
        $this->assertTrue($property_type->save()); 

        // checking relationship
        $estate_object->property_type_id = $property_type->id;
        $this->assertTrue($estate_object->validate(['property_type_id']));

        // Checking for null
        $estate_object->property_type_id = null;
        $this->assertFalse($estate_object->validate(['property_type_id']));

        // checking for boolean
        $estate_object->property_type_id = true;
        $this->assertFalse($estate_object->validate(['property_type_id']));

        // checking for float
        $estate_object->property_type_id = 1.1;
        $this->assertFalse($estate_object->validate(['property_type_id']));

        // checking for string
        $estate_object->property_type_id = 'property_type_id';
        $this->assertFalse($estate_object->validate(['property_type_id']));
    }

    /**
    * Test model 'EstateObject' column 'building_type_id'
    */
    public function testEstateObjectBuildingTypeID()
    {
        $estate_object = new  EstateObject();
        $building_type = new BuildingType(); 
        
        $building_type->name = 'name'; 
        $this->assertTrue($building_type->save()); 

        // checking relationship
        $estate_object->building_type_id = $building_type->id;
        $this->assertTrue($estate_object->validate(['building_type_id']));

        // Checking for null
        $estate_object->building_type_id = null;
        $this->assertFalse($estate_object->validate(['building_type_id']));

        // checking for boolean
        $estate_object->building_type_id = true;
        $this->assertFalse($estate_object->validate(['building_type_id']));

        // checking for float
        $estate_object->building_type_id = 1.1;
        $this->assertFalse($estate_object->validate(['building_type_id']));

        // checking for string
        $estate_object->building_type_id = 'building_type_id';
        $this->assertFalse($estate_object->validate(['building_type_id']));     
    }

    /**
    * Test model 'EstateObject' column 'metro_id'
    */
    public function testEstateObjectMetroID()
    {
        $estate_object = new  EstateObject();
        $metro = new Metro(); 
        
        $metro->name = 'name'; 
        $this->assertTrue($metro->save()); 

        // checking relationship
        $estate_object->metro_id = $metro->id;
        $this->assertTrue($estate_object->validate(['metro_id']));

        // checking for boolean
        $estate_object->metro_id = true;
        $this->assertFalse($estate_object->validate(['metro_id']));

        // checking for float
        $estate_object->metro_id = 1.1;
        $this->assertFalse($estate_object->validate(['metro_id']));

        // checking for string
        $estate_object->metro_id = 'metro_id';
        $this->assertFalse($estate_object->validate(['metro_id'])); 
    }

    /**
    * Test model 'EstateObject' column 'name'
    */
    public function testEstateObjectName()
    {
        $estate_object = new  EstateObject();

        // Checking for null
        $estate_object->name = null;
        $this->assertFalse($estate_object->validate(['name']));

        // checking for boolean
        $estate_object->name = true;
        $this->assertFalse($estate_object->validate(['name']));

        // checking for integer
        $estate_object->name = 1;
        $this->assertFalse($estate_object->validate(['name']));

        // checking for float
        $estate_object->name = 1.1;
        $this->assertFalse($estate_object->validate(['name']));

        // checking the length (257)
        $estate_object->name = '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567';
        $this->assertFalse($estate_object->validate(['name']));

        // checking the length (256)
        $estate_object->name = '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456';
        $this->assertTrue($estate_object->validate(['name']));

        // checking for string
        $estate_object->name = 'name';
        $this->assertTrue($estate_object->validate(['name']));
    }

    /**
    * Test model 'EstateObject' column 'description'
    */
    public function testEstateObjectDescription()
    {
        $estate_object = new  EstateObject();

        // Checking for null
        $estate_object->description = null;
        $this->assertFalse($estate_object->validate(['description']));

        // checking for boolean
        $estate_object->description = true;
        $this->assertFalse($estate_object->validate(['description']));

        // checking for integer
        $estate_object->description = 1;
        $this->assertFalse($estate_object->validate(['description']));

        // checking for float
        $estate_object->description = 1.1;
        $this->assertFalse($estate_object->validate(['description']));

        // checking for string
        $estate_object->description = 'name';
        $this->assertTrue($estate_object->validate(['description']));
    }

    /**
    * Test model 'EstateObject' column 'price'
    */
    public function testEstateObjectPrice()
    {
        $estate_object = new  EstateObject();
    
        // Checking for null
        $estate_object->price = null;
        $this->assertFalse($estate_object->validate(['price']));

        // checking for boolean
        $estate_object->price = true;
        $this->assertFalse($estate_object->validate(['price']));

        // checking for string
        $estate_object->price = 'name';
        $this->assertFalse($estate_object->validate(['price']));

        // checking the length (13,3)
        $estate_object->price = 123456789.123;
        $this->assertTrue($estate_object->validate(['price']));
    }

    /**
    * Test model 'EstateObject' column 'url'
    */
    public function testEstateObjectUrl()
    {
        $estate_object = new  EstateObject();

        // checking for boolean
        $estate_object->url = true;
        $this->assertFalse($estate_object->validate(['url']));

        // checking for integer
        $estate_object->url = 1;
        $this->assertFalse($estate_object->validate(['url']));

        // checking for float
        $estate_object->url = 1.1;
        $this->assertFalse($estate_object->validate(['url']));

        // checking the length (257)
        $estate_object->url = '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567';
        $this->assertFalse($estate_object->validate(['url']));

        // checking the length (256)
        $estate_object->url = '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456';
        $this->assertTrue($estate_object->validate(['url']));

        // checking for string
        $estate_object->url = 'url';
        $this->assertTrue($estate_object->validate(['url']));
    }

    /**
    * Test model 'EstateObject' column 'square'
    */
    public function testEstateObjectSquare()
    {
        $estate_object = new  EstateObject();
    
        // Checking for null
        $estate_object->square = null;
        $this->assertFalse($estate_object->validate(['square']));

        // checking for boolean
        $estate_object->square = true;
        $this->assertFalse($estate_object->validate(['square']));

        // checking for string
        $estate_object->square = 'name';
        $this->assertFalse($estate_object->validate(['square']));

        // checking the length (6,1)
        $estate_object->square = 12345.1;
        $this->assertTrue($estate_object->validate(['square']));
    }

    /**
    * Test model 'EstateObject' column 'kitchen_square'
    */
    public function testEstateObjectKitchenSquare()
    {
        $estate_object = new  EstateObject();
    
        // Checking for null
        $estate_object->kitchen_square = null;
        $this->assertFalse($estate_object->validate(['kitchen_square']));

        // checking for boolean
        $estate_object->kitchen_square = true;
        $this->assertFalse($estate_object->validate(['kitchen_square']));

        // checking for string
        $estate_object->kitchen_square = 'name';
        $this->assertFalse($estate_object->validate(['kitchen_square']));

        // checking the length (6,1)
        $estate_object->kitchen_square = 12345.1;
        $this->assertTrue($estate_object->validate(['kitchen_square']));
    }

    /**
    * Test model 'EstateObject' column 'level'
    */
    public function testEstateObjectLevel()
    {
        $estate_object = new EstateObject();

        // checking for boolean
        $estate_object->level = true;
        $this->assertFalse($estate_object->validate(['level']));

        // checking for float
        $estate_object->level = 1.1;
        $this->assertFalse($estate_object->validate(['level']));

        // checking for string
        $estate_object->level = 'level';
        $this->assertFalse($estate_object->validate(['level']));

        // checking the length (20)
        $estate_object->level = 12345678901212345678;
        $this->assertFalse($estate_object->validate(['level']));

        // checking the length (19)
        $estate_object->level = 1234567890121234567;
        $this->assertTrue($estate_object->validate(['level']));        
    }

    /**
    * Test model 'EstateObject' column 'rooms'
    */
    public function testEstateObjectRooms()
    {
        $estate_object = new  EstateObject();

        // checking for boolean
        $estate_object->rooms = true;
        $this->assertFalse($estate_object->validate(['rooms']));

        // checking for float
        $estate_object->rooms = 1.1;
        $this->assertFalse($estate_object->validate(['rooms']));

        // checking for string
        $estate_object->rooms = 'rooms';
        $this->assertFalse($estate_object->validate(['rooms']));

        // checking the length (20)
        $estate_object->rooms = 12345678901212345678;
        $this->assertFalse($estate_object->validate(['rooms']));

        // checking the length (19)
        $estate_object->rooms = 1234567890121234567;
        $this->assertTrue($estate_object->validate(['rooms']));        
    }

    /**
    * Test model 'EstateObject' column 'ln'
    */
    public function testEstateObjectLN()
    {
        $estate_object = new  EstateObject();
    
        // Checking for null
        $estate_object->ln = null;
        $this->assertFalse($estate_object->validate(['ln']));

        // checking for boolean
        $estate_object->ln = true;
        $this->assertFalse($estate_object->validate(['ln']));

        // checking for string
        $estate_object->ln = 'name';
        $this->assertFalse($estate_object->validate(['ln']));

        // checking the length (10,7) float (+)
        $estate_object->ln = 12.1234567;
        $this->assertTrue($estate_object->validate(['ln']));
    }


    /**
    * Test model 'EstateObject' column 'lt'
    */
    public function testEstateObjectLT()
    {
        $estate_object = new  EstateObject();
    
        // Checking for null
        $estate_object->lt = null;
        $this->assertFalse($estate_object->validate(['lt']));

        // checking for boolean
        $estate_object->lt = true;
        $this->assertFalse($estate_object->validate(['lt']));

        // checking for string
        $estate_object->lt = 'name';
        $this->assertFalse($estate_object->validate(['lt']));

        // checking the length (10,7) float (+)
        $estate_object->lt = 12.1234567;
        $this->assertTrue($estate_object->validate(['lt']));
    }

    /**
    * Test model 'EstateObject' column 'internal'
    */
    public function testEstateObjectInternal()
    {
        $estate_object = new  EstateObject();

        // checking for float
        $estate_object->internal = 1.1;
        $this->assertFalse($estate_object->validate(['internal']));

        // checking for string
        $estate_object->internal = 'internal';
        $this->assertFalse($estate_object->validate(['internal']));

        // checking for boolean
        $estate_object->internal = 0;
        $this->assertTrue($estate_object->validate(['internal']));

        // checking for boolean
        $estate_object->internal = 1;
        $this->assertTrue($estate_object->validate(['internal']));
    }

    /**
    * Test model 'EstateObject' column 'agent'
    */
    public function testEstateObjectAgent()
    {
        $estate_object = new  EstateObject();

        // checking for float
        $estate_object->agent = 1.1;
        $this->assertFalse($estate_object->validate(['agent']));

        // checking for string
        $estate_object->agent = 'agent';
        $this->assertFalse($estate_object->validate(['agent']));
        
        // checking for boolean
        $estate_object->agent = 0;
        $this->assertTrue($estate_object->validate(['agent']));

        // checking for boolean
        $estate_object->agent = 1;
        $this->assertTrue($estate_object->validate(['agent']));
    }

    /**
    * Test model 'EstateObject' column 'published'
    */
    public function testEstateObjecPublished()
    {
        $estate_object = new  EstateObject();

        // checking for float
        $estate_object->published = 1.1;
        $this->assertFalse($estate_object->validate(['published']));

        // checking for string
        $estate_object->published = 'published';
        $this->assertFalse($estate_object->validate(['published']));
        
        // checking for boolean
        $estate_object->published = 0;
        $this->assertTrue($estate_object->validate(['published']));

        // checking for boolean
        $estate_object->published = 1;
        $this->assertTrue($estate_object->validate(['published']));
    }

    /**
    * Test model 'EstateObject' column 'user_id'
    */
    public function testEstateObjectUserID()
    {
        $estate_object = new  EstateObject();
        $user = new User(); 
        
        $user->email = 'email@email.email'; 
        $this->assertTrue($user->save()); 

        // checking relationship
        $estate_object->user_id = $user->id;
        $this->assertTrue($estate_object->validate(['user_id']));

        // checking for boolean
        $estate_object->user_id = true;
        $this->assertFalse($estate_object->validate(['user_id']));

        // checking for float
        $estate_object->user_id = 1.1;
        $this->assertFalse($estate_object->validate(['user_id']));

        // checking for string
        $estate_object->user_id = 'user_id';
        $this->assertFalse($estate_object->validate(['user_id']));     
    }
}