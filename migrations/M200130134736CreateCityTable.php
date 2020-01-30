<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200130134736CreateCityTable
 */
class M200130134736CreateCityTable extends Migration
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
        echo "M200130134736CreateCityTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('city', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL',
            'region_id' => 'INT NOT NULL' //FK
        ]);
    }

    public function down()
    {
        $this->dropTable('city');
    }
}
