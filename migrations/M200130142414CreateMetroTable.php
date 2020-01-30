<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200130142414CreateMetroTable
 */
class M200130142414CreateMetroTable extends Migration
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
        echo "M200130142414CreateMetroTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('metro', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL'
        ]);
    }

    public function down()
    {
        $this->dropTable('metro');
    }
}
