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
            'lt' => 'DECIMAL(10,7) NOT NULL',
            'lg' => 'DECIMAL(10,7) NOT NULL',
            'city_id' => 'INT(19) NOT NULL',
            'street_id' => 'INT(19) NOT NULL',
            'region_id' => 'INT(19)',
            'city_area_id' => 'INT(19)'
        ]);
    }

    public function down()
    {
        $this->dropTable('address');
    }
}
