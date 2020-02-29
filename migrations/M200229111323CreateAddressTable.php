<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200229111323CreateAddressTable
 */
class M200229111323CreateAddressTable extends Migration
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
        echo "M200229111323CreateAddressTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'lt' => 'DECIMAL(10,7)',
            'lg' => 'DECIMAL(10,7)',
            'region_id' => 'INT(19) DEFAULT 1', //FK
            'city_id' => 'INT(19) DEFAULT 1', //FK
            'city_area_id' => 'INT(19) DEFAULT 1', //FK
            'street_id' => 'INT(19) DEFAULT 1', //FK
        ]);
    }

    public function down()
    {
        $this->dropTable('address');
    }
}
