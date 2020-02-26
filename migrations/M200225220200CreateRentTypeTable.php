<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225220200CreateRentTypeTable
 */
class M200225220200CreateRentTypeTable extends Migration
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
        echo "M200225220200CreateRentTypeTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('rent_type', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('rent_type');
    }
}
