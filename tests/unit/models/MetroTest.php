<?php 

namespace micro\tests\unit\models;

use micro\models\Metro;

class MetroTest extends \Codeception\Test\Unit
{
    /**
    * Test model 'Metro' column 'name'
    */
    public function testMetroName()
    {
        $metro = new Metro();

        // Checking for null
        $metro->name = null;
        $this->assertFalse($metro->validate(['name']));

        // checking for boolean
        $metro->name = true;
        $this->assertFalse($metro->validate(['name']));

        // checking for integer
        $metro->name = 1;
        $this->assertFalse($metro->validate(['name']));

        // checking for float
        $metro->name = 1.1;
        $this->assertFalse($metro->validate(['name']));

        // checking the length (300)
        $metro->name = '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890';
        $this->assertFalse($metro->validate(['name']));

        // checking the length (256)
        $metro->name = '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456';
        $this->assertTrue($metro->validate(['name']));

        // checking for string
        $metro->name = 'name';
        $this->assertTrue($metro->validate(['name']));
    }
}