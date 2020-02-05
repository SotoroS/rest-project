<?php 

namespace micro\tests\unit\models;

use micro\models\BuildingType;

class BuildingTypeTest extends \Codeception\Test\Unit
{
    /**
    * Test model 'BuildingType' column 'name'
    */
    public function testBuildingName()
    {
        $building = new BuildingType();

        // Checking for null
        $building->name = null;
        $this->assertFalse($building->validate(['name']));

        // checking for boolean
        $building->name = true;
        $this->assertFalse($building->validate(['name']));

        // checking for integer
        $building->name = 1;
        $this->assertFalse($building->validate(['name']));

        // checking for float
        $building->name = 1.1;
        $this->assertFalse($building->validate(['name']));

        // checking the length (257)
        $building->name = '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567';
        $this->assertFalse($building->validate(['name']));

        // checking the length (256)
        $building->name = '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456';
        $this->assertTrue($building->validate(['name']));

        // checking for string
        $building->name = 'name';
        $this->assertTrue($building->validate(['name']));
    }
}