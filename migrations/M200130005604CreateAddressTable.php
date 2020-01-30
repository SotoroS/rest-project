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
            'city_id' => 'INT NOT NULL',
            'street_id' => 'INT NOT NULL',
            'region_id' => 'INT',
            'city_are_id' => 'INT'
        ]);
    }

    public function down()
    {
        $this->dropTable('address');
    }
}
