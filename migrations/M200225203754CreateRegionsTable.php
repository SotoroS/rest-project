<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225203754CreateRegionsTable
 */
class M200225203754CreateRegionsTable extends Migration
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
        echo "M200225203754CreateRegionsTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('regions', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256)',
            'created_at' => 'TIMESTAMP',
            'updated_at' => 'TIMESTAMP',
        ]);
    }

    public function down()
    {
        $this->dropTable('regions');
    }
}
