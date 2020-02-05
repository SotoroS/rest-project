<?php 

namespace micro\tests\unit\models;

use micro\models\EstateObject;

class EstateObjectTest extends \Codeception\Test\Unit
{
    /**
    * Test model 'EstateObject' column 'address_id'
    */
    public function testEstateObjectAddressID()
    {
        $estate_object = new  EstateObject();

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

        // checking the length (12)
        $estate_object->address_id = 123456789012;
        $this->assertFalse($estate_object->validate(['address_id']));

        // // checking the length (11)
        // $estate_object->address_id = 1234567890;
        // $this->assertTrue($estate_object->validate(['address_id']));        
    }

    /**
    * Test model 'EstateObject' column 'rent_type_id'
    */
    public function testEstateObjectRentTypeID()
    {
        $estate_object = new  EstateObject();

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

        // checking the length (12)
        $estate_object->rent_type_id = 123456789012;
        $this->assertFalse($estate_object->validate(['rent_type_id']));

        // // checking the length (11)
        // $estate_object->rent_type_id = 1234567890;
        // $this->assertTrue($estate_object->validate(['rent_type_id']));        
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
    * Test model 'EstateObject' column 'user_id'
    */
    public function testEstateObjectUserID()
    {
        $estate_object = new  EstateObject();

        // checking for boolean
        $estate_object->user_id = true;
        $this->assertFalse($estate_object->validate(['user_id']));

        // checking for float
        $estate_object->user_id = 1.1;
        $this->assertFalse($estate_object->validate(['user_id']));

        // checking for string
        $estate_object->user_id = 'user_id';
        $this->assertFalse($estate_object->validate(['user_id']));

        // checking the length (12)
        $estate_object->user_id = 123456789012;
        $this->assertFalse($estate_object->validate(['user_id']));

        // // checking the length (11)
        // $estate_object->user_id = 1234567890;
        // $this->assertTrue($estate_object->validate(['user_id']));        
    }
}