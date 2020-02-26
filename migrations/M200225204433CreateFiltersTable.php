<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225204433CreateFiltersTable
 */
class M200225204433CreateFiltersTable extends Migration
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
        echo "M200225204433CreateFiltersTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('filters', [
            'id' => $this->primaryKey(),
            'user_id' => 'INT(19) DEFAULT 1', //FK
            'num_of_people' => 'INT(19)',
            'family' => 'BOOLEAN',
            'pets' => 'BOOLEAN',
            'request_type_id' => 'INT(19) DEFAULT 1', //fk request_type
            'square_from' => 'INT(19)',
            'square_to' => 'INT(19)',
            'city_id' => 'INT(19) DEFAULT 1', //fk  city
            'price_from' => 'INT(19)',
            'price_to' => 'INT(19)',
            'description' => 'TEXT',
            'pivot_lt' => 'DECIMAL(10,7)',
            'pivot_lg' => 'DECIMAL(10,7)',
            'radius' => 'DECIMAL(10,7)',
            'city_area_id' => 'INT(19) DEFAULT 1', //FK
            'rent_type' => 'VARCHAR(256)',
            'property_type' => 'VARCHAR(256)',
            'substring' => 'VARCHAR(256)',
            'created_at' => 'TIMESTAMP',
            'updated_at' => 'TIMESTAMP',
        ]);
    }

    public function down()
    {
        $this->dropTable('filters');
    }
}
