<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200130134709CreateCityAreaTable
 */
class M200130134709CreateCityAreaTable extends Migration
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
        echo "M200130134709CreateCityAreaTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('city_area', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL',
            'city_id' => 'INT NOT NULL' //FK
        ]);
    }

    public function down()
    {
        $this->dropTable('city_area');
    }
}
