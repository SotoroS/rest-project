<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200229111920CreateRequestTypeTable
 */
class M200229111920CreateRequestTypeTable extends Migration
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
        echo "M200229111920CreateRequestTypeTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('request_type', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('request_type');
    }
}
