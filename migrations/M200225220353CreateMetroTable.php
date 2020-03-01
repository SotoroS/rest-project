<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225220353CreateMetroTable
 */
class M200225220353CreateMetroTable extends Migration
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
        echo "M200225220353CreateMetroTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('metro', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL',
            'city_id' => 'INT(19) DEFAULT 1' //FK
        ]);
    }

    public function down()
    {
        $this->dropTable('metro');
    }
}
