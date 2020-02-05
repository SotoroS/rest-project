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
            'address_id' => 'INT(19) NOT NULL', //fk
            'rent_type_id' => 'INT(19) NOT NULL', //FK
            'property_type_id' => 'INT(19) NOT NULL', //FK
            'building_type_id' => 'INT(19) NOT NULL',//FK
            'metro_id' => 'INT(19)', //fk
            'name'=> 'VARCHAR(256) NOT NULL',
            'description' => 'TEXT NOT NULL', 
            'price' => 'DECIMAL(13,3) NOT NULL',
            'url' => 'VARCHAR(256)',
            'square' => 'DECIMAL(6,1) NOT NULL',
            'kitchen_square' => 'DECIMAL(6,1) NOT NULL',
            'level' => 'INT(19)', 
            'rooms' => 'INT(19)', 
            'ln' => 'DECIMAL(10,7) NOT NULL',
            'lt' => 'DECIMAL(10,7) NOT NULL',
            'internal' => 'BOOLEAN DEFAULT false',
            'agent' => 'BOOLEAN NOT NULL',
            'published' => 'BOOLEAN DEFAULT false',
            'user_id' => 'INT(19)' //FK
        ]);
    }

    public function down()
    {
        $this->dropTable('estate_object');
    }

}
