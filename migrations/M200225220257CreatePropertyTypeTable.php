<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225220257CreatePropertyTypeTable
 */
class M200225220257CreatePropertyTypeTable extends Migration
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
        echo "M200225220257CreatePropertyTypeTable cannot be reverted.\n";

        return false;
    }
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('property_type', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('property_type');
    }
}
