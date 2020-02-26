<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225215959CreateBuildingTypeTable
 */
class M200225215959CreateBuildingTypeTable extends Migration
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
        echo "M200225215959CreateBuildingTypeTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('building_type', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('building_type');
    }
}
