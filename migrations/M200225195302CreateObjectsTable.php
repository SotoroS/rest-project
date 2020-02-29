<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225195302CreateObjectsTable
 */
class M200225195302CreateObjectsTable extends Migration
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
        echo "M200225195302CreateObjectsTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('objects', [
            'id' => $this->primaryKey(),
            'address_id' => 'INT(19) DEFAULT 1',//FK
            'building_type_id' => 'INT(19) DEFAULT 1',//FK
            'rent_type' => 'INT(19) DEFAULT 1', //FK
            'property_type' => 'INT(19) DEFAULT 1', //FK
            'metro_id' => 'INT(19) DEFAULT 1', //FK
            'name' => 'VARCHAR(256) NOT NULL',
            'description' => 'TEXT NOT NULL',
            'price' => 'DECIMAL(13,3) NOT NULL',
            'url' => 'TEXT',
            'user_id' => 'INT(19) DEFAULT 1', //FK
            'city_id' => 'INT(19) DEFAULT 1', //FK
            'region_id' => 'INT(19) DEFAULT 1', //FK
            'city_area_id' => 'INT(19) DEFAULT 1', //FK
            'created_at' => 'TIMESTAMP',
            'updated_at' => 'TIMESTAMP',
            'data' => 'TEXT'
        ]);
    }

    public function down()
    {
        $this->dropTable('objects');

    }
}
