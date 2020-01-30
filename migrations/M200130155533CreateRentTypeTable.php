<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200130155533CreateRentTypeTable
 */
class M200130155533CreateRentTypeTable extends Migration
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
        echo "M200130155533CreateRentTypeTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('rent_type', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL'
        ]);
    }

    public function down()
    {
        $this->dropTable('rent_type');
    }
}
