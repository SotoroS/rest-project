<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200130134753CreateRegionTable
 */
class M200130134753CreateRegionTable extends Migration
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
        echo "M200130134753CreateRegionTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('region', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL'
        ]);
    }

    public function down()
    {
        $this->dropTable('region');
    }
}
