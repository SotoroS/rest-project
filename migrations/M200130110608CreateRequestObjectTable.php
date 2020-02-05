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
            'user_id' => 'INT(19) NOT NULL', //fk users
            'num_of_people' => 'INT(19) NOT NULL',
            'family' => 'BOOLEAN NOT NULL',
            'pets' => 'BOOLEAN NOT NULL',
            'request_type_id' => 'INT(19) NOT NULL', //fk request_type
            'square_from' => 'INT(19) NOT NULL',
            'square_to' => 'INT(19) NOT NULL',
            'city_id' => 'INT(19) NOT NULL', //fk  city
            'price_from' => 'INT(19) NOT NULL',
            'price_to' => 'INT(19) NOT NULL',
            'description' => 'TEXT NOT NULL',
            'pivot_lt' => 'DECIMAL(10,7)',
            'pivot_lg' => 'DECIMAL(10,7)',
            'radius' => 'DECIMAL(10,7) NOT NULL'
        ]);
    }

    public function down()
    {
        $this->dropTable('request_object');
    }
}
