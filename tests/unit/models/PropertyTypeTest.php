<?php 

namespace micro\tests\unit\models;

use micro\models\PropertyType;

class PropertyTypeTest extends \Codeception\Test\Unit
{
    /**
    * Test model 'PropertyType' column 'name'
    */
    public function testPropertyName()
    {
        $property = new PropertyType();

        // Checking for null
        $property->name = null;
        $this->assertFalse($property->validate(['name']));

        // checking for boolean
        $property->name = true;
        $this->assertFalse($property->validate(['name']));

        // checking for integer
        $property->name = 1;
        $this->assertFalse($property->validate(['name']));

        // checking for float
        $property->name = 1.1;
        $this->assertFalse($property->validate(['name']));

        // checking the length (257)
        $property->name = '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567';
        $this->assertFalse($property->validate(['name']));

        // checking the length (256)
        $property->name = '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456';
        $this->assertTrue($property->validate(['name']));

        // checking for string
        $property->name = 'name';
        $this->assertTrue($property->validate(['name']));
    }
}