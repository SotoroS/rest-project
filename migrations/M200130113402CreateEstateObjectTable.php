<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200130113402CreateEstateObjectTable
 */
class M200130113402CreateEstateObjectTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "M200130113402CreateEstateObjectTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('estate_object', [
            'id' => $this->primaryKey(),
            'city_id' => 'INT NOT NULL', //fk
            'region_id' => 'INT', //fk
            'street_id' => 'INT NOT NULL',//FK
            'city_area_id' => 'INT', //FK (район)
            'rent_type' => 'INT NOT NULL', //FK
            'property_type' => 'INT NOT NULL', //FK
            'building_type' => 'INT NOT NULL',//FK
            'metro_id' => 'INT', //fk
            'name'=> 'VARCHAR(256) NOT NULL',
            'description' => 'TEXT NOT NULL', 
            'price' => 'DECIMAL NOT NULL',
            'url' => 'VARCHAR(256)',
            'square' => 'DECIMAL NOT NULL',
            'kitchen_square' => 'DECIMAL NOT NULL',
            'level' => 'INT', 
            'rooms' => 'INT', 
            'ln' => 'DECIMAL NOT NULL',
            'lt' => 'DECIMAL NOT NULL',
            'internal' => 'BOOLEAN DEFAULT false',
            'agent' => 'BOOLEAN NOT NULL',
            'published' => 'BOOLEAN DEFAULT false'
        ]);
    }

    public function down()
    {
        $this->dropTable('estate_object');
    }

}
