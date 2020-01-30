<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200130110608CreateRequestObjectTable
 */
class M200130110608CreateRequestObjectTable extends Migration
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
        echo "M200130110608CreateRequestObjectTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('request_object', [
            'id' => $this->primaryKey(),
            'user_id' => 'INT NOT NULL', //fk users
            'num_of_people' => 'INT NOT NULL',
            'family' => 'BOOLEAN NOT NULL',
            'type' => 'INT NOT NULL', //fk request_type
            'square_from' => 'INT NOT NULL',
            'square_to' => 'INT NOT NULL',
            'city_id' => 'INT NOT NULL', //fk  city
            'address_ids' => 'VARCHAR(256) NOT NULL',
            'price_from' => 'INT NOT NULL',
            'price_to' => 'INT NOT NULL',
            'description' => 'TEXT NOT NULL',
            'pivot_lt' => 'DECIMAL',
            'pivot_lg' => 'DECIMAL',
            'radius' => 'DECIMAL NOT NULL'
        ]);
    }

    public function down()
    {
        $this->dropTable('request_object');
    }
}
