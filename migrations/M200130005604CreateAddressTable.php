<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200130005604CreateAddressTable
 */
class M200130005604CreateAddressTable extends Migration
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
        echo "M200130005604CreateAddressTable cannot be reverted.\n";

        return false;
    }

    
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'lt' => 'DECIMAL NOT NULL',
            'lg' => 'DECIMAL NOT NULL',
            'city_id' => 'INTEGER NOT NULL',
            'street_id' => 'INTEGER NOT NULL',
            'region_id' => 'INTEGER',
            'city_are_id' => 'INTEGER'
        ]);
    }

    public function down()
    {
        $this->dropTable('address');
    }
}
