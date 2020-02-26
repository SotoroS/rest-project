<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225220925CreatePhonesTable
 */
class M200225220925CreatePhonesTable extends Migration
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
        echo "M200225220925CreatePhonesTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('phones', [
            'id' => $this->primaryKey(),
            'phone' => 'VARCHAR(256)',
            'object_id' => 'INT(19) DEFAULT 1', //FK
            'created_at' => 'TIMESTAMP',
            'updated_at' => 'TIMESTAMP',
        ]);
    }

    public function down()
    {
        $this->dropTable('phones');
    }
}
