<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200130160310CreateRequestAddressTable
 */
class M200130160310CreateRequestAddressTable extends Migration
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
        echo "M200130160310CreateRequestAddressTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('request_address', [
            'id' => $this->primaryKey(),
            'request_object_id' => 'INT NOT NULL', //FK
            'address_id' =>'INT NOT NULL' //FK
        ]);
    }

    public function down()
    {
        $this->dropTable('request_address');
    }
}
