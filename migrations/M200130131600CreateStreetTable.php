<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200130131600CreateStreetTable
 */
class M200130131600CreateStreetTable extends Migration
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
        echo "M200130131600CreateStreetTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('street', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL',
            'city_area_id' => 'INT(19) NOT NULL' //FK
        ]);
    }

    public function down()
    {
        $this->dropTable('street');
    }
}
