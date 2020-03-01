<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225203354CreateCitiesTable
 */
class M200225203354CreateCitiesTable extends Migration
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
        echo "M200225203354CreateCitiesTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('cities', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL',
            'region_id' => 'INT(19) DEFAULT 1', //FK
        ]);
    }

    public function down()
    {
        $this->dropTable('cities');
    }
}
