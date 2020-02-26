<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225202435CreateCityAreasTable
 */
class M200225202435CreateCityAreasTable extends Migration
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
        echo "M200225202435CreateCityAreasTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('city_areas', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL',
            'city_id' => 'INT(19) DEFAULT 1' //FK
        ]);
    }

    public function down()
    {
        $this->dropTable('city_areas');
    }
}
